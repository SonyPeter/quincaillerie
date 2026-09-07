<?php

/**
 * venteController — Ventes (fiche imprimable en 3 étapes)
 *
 * 1. Le vendeur remplit la fiche avec les produits -> enregistrée en statut
 *    "en_attente" (proforma), le stock n'est pas encore touché.
 * 2. La fiche peut être imprimée (proforma) sans rien valider.
 * 3. Une fois l'argent reçu, un clic sur "Valider la vente" décrémente le
 *    stock de chaque produit (via productModel::adjustStock) et passe le
 *    statut à "payé" — ce qui la fait compter dans le total des ventes.
 *
 * Un vendeur ne voit et ne valide jamais que ses propres ventes ; l'admin
 * voit et peut valider toutes les ventes.
 */

class venteController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        if (!empty($_GET['fiche'])) {
            $this->afficherFiche((string) $_GET['fiche']);
            return;
        }

        if (!empty($_GET['historique'])) {
            $this->afficherHistorique();
            return;
        }

        $this->render(BASE_PATH . '/apps/views/dashboard/ventes_saisie.php', [
            'produits' => (new productModel())->all(),
            'csrfToken' => generateCsrfToken(),
        ]);
    }

    private function afficherHistorique(): void
    {
        $sellerId = Auth::isAdmin() ? null : $_SESSION['user_id'];

        $this->render(BASE_PATH . '/apps/views/dashboard/ventes_historique.php', [
            'ventes' => (new saleModel())->all($sellerId),
        ]);
    }

    /**
     * Fiche de vente imprimable : page autonome (sans le menu du tableau de
     * bord) pour que l'impression ne montre que le reçu
     */
    private function afficherFiche(string $saleId): void
    {
        $saleModel = new saleModel();
        $sale = $saleModel->find($saleId);

        if (!$sale || (!Auth::isAdmin() && $sale['seller_id'] !== $_SESSION['user_id'])) {
            http_response_code(404);
            echo '<h1>404 — Fiche introuvable</h1>';
            return;
        }

        $items = $saleModel->itemsBySale($saleId);
        $csrfToken = generateCsrfToken();
        $peutValider = $sale['payment_status'] === 'en_attente'
            && (Auth::isAdmin() || $sale['seller_id'] === $_SESSION['user_id']);

        require BASE_PATH . '/apps/views/dashboard/vente_fiche.php';
    }

    public function store(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirectAvecErreur('Requête invalide, veuillez réessayer.');
        }

        $action = $_POST['action'] ?? '';

        match ($action) {
            'enregistrer' => $this->enregistrer(),
            'valider' => $this->valider(),
            'annuler' => $this->annuler(),
            default => $this->redirectAvecErreur('Action inconnue.'),
        };
    }

    private function enregistrer(): void
    {
        $productIds = $_POST['product_id'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $paymentMethod = $_POST['payment_method'] ?? '';
        $customerName = trim($_POST['customer_name'] ?? '');

        if (!in_array($paymentMethod, ['cash', 'moncash', 'natcash'], true)) {
            $this->redirectAvecErreur('Veuillez choisir un moyen de paiement.');
        }

        $productModel = new productModel();
        $lignes = [];

        foreach ($productIds as $i => $productId) {
            $productId = trim((string) $productId);
            $quantity = $quantities[$i] ?? '';

            if ($productId === '' || $quantity === '') {
                continue;
            }

            if (!ctype_digit((string) $quantity) || (int) $quantity <= 0) {
                $this->redirectAvecErreur('Chaque quantité doit être un nombre entier positif.');
            }

            $product = $productModel->find($productId);
            if (!$product || !$product['is_published']) {
                $this->redirectAvecErreur('Un des produits sélectionnés est introuvable.');
            }

            if ((int) $quantity > (int) $product['quantity']) {
                $this->redirectAvecErreur('Stock insuffisant pour "' . $product['name'] . '" (disponible : ' . $product['quantity'] . ').');
            }

            $lignes[] = [
                'product_id' => $productId,
                'quantity' => (int) $quantity,
                'unit_price' => (float) $product['unit_price'],
            ];
        }

        if (empty($lignes)) {
            $this->redirectAvecErreur('Veuillez ajouter au moins un produit à vendre.');
        }

        $total = array_sum(array_map(fn($l) => $l['quantity'] * $l['unit_price'], $lignes));

        $saleModel = new saleModel();
        $saleId = $saleModel->create($_SESSION['user_id'], $paymentMethod, $customerName, $total);

        foreach ($lignes as $ligne) {
            $saleModel->addItem($saleId, $ligne['product_id'], $ligne['quantity'], $ligne['unit_price']);
        }

        header('Location: ' . BASE_URL . '/ventes?fiche=' . urlencode($saleId));
        exit;
    }

    private function valider(): void
    {
        $id = trim($_POST['id'] ?? '');
        $saleModel = new saleModel();
        $sale = $saleModel->find($id);

        if (!$sale) {
            $this->redirectAvecErreur('Vente introuvable.');
        }

        if (!Auth::isAdmin() && $sale['seller_id'] !== $_SESSION['user_id']) {
            $this->redirectAvecErreur('Vous ne pouvez valider que vos propres ventes.');
        }

        if ($sale['payment_status'] === 'paye') {
            $this->redirectVersFiche($id);
        }

        $productModel = new productModel();
        $items = $saleModel->itemsBySale($id);

        try {
            foreach ($items as $item) {
                $productModel->adjustStock(
                    $item['product_id'],
                    -(int) $item['quantity'],
                    'vente',
                    'Vente #' . $sale['sale_number'],
                    $_SESSION['user_id']
                );
            }
        } catch (RuntimeException $e) {
            $this->redirectAvecErreur('Stock insuffisant pour valider cette vente (un produit a été vendu entre-temps).');
        }

        $saleModel->markAsPaid($id);

        $_SESSION['flash_success'] = 'Vente validée : paiement reçu et stock mis à jour.';
        $this->redirectVersFiche($id);
    }

    private function annuler(): void
    {
        $id = trim($_POST['id'] ?? '');
        $saleModel = new saleModel();
        $sale = $saleModel->find($id);

        if (!$sale || (!Auth::isAdmin() && $sale['seller_id'] !== $_SESSION['user_id'])) {
            $this->redirectAvecErreur('Vente introuvable.');
        }

        $saleModel->cancel($id);

        $_SESSION['flash_success'] = 'Proforma annulée.';
        header('Location: ' . BASE_URL . '/ventes');
        exit;
    }

    private function redirectVersFiche(string $id): void
    {
        header('Location: ' . BASE_URL . '/ventes?fiche=' . urlencode($id));
        exit;
    }

    private function redirectAvecErreur(string $message): void
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_URL . '/ventes');
        exit;
    }
}
