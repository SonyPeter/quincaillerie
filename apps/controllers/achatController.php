<?php

/**
 * achatController — Saisie des achats (réapprovisionnement fournisseur)
 *
 * Un achat peut porter sur des produits déjà au catalogue (choisis dans une
 * liste) ou sur de nouveaux produits inconnus du système (catégorie, nom,
 * description, photo saisis directement ici). Ces nouveaux produits sont
 * créés SANS prix de vente : ils restent hors catalogue tant que l'admin ne
 * clique pas sur "Ajouter au catalogue" (dans /produits) pour leur fixer
 * une description finale et un prix — voir productModel::createDraft()/publish().
 *
 * Chaque achat validé augmente automatiquement le stock des produits
 * concernés via productModel::adjustStock() et apparaît dans l'historique
 * de chaque produit ainsi que dans l'historique global des achats.
 */

class achatController extends DashboardPage
{
    private const IMAGE_TAILLE_MAX = 2 * 1024 * 1024; // 2 Mo
    private const IMAGE_TYPES_AUTORISES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function index(): void
    {
        Auth::requireRole(['admin']);

        if (!empty($_GET['historique'])) {
            $this->afficherHistorique();
            return;
        }

        $this->render(BASE_PATH . '/apps/views/dashboard/achats_saisie.php', [
            'produits' => (new productModel())->all(),
            'categories' => (new categoryModel())->all(),
            'csrfToken' => generateCsrfToken(),
        ]);
    }

    private function afficherHistorique(): void
    {
        $purchaseModel = new purchaseModel();
        $achats = $purchaseModel->all();

        foreach ($achats as &$achat) {
            $achat['items'] = $purchaseModel->itemsByPurchase($achat['id']);
        }
        unset($achat);

        $this->render(BASE_PATH . '/apps/views/dashboard/achats_historique.php', [
            'achats' => $achats,
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['admin']);

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirectAvecErreur('Requête invalide, veuillez réessayer.');
        }

        $modes = $_POST['mode'] ?? [];
        $productIds = $_POST['product_id'] ?? [];
        $categoryIds = $_POST['category_id'] ?? [];
        $names = $_POST['name'] ?? [];
        $descriptions = $_POST['description'] ?? [];
        $quantities = $_POST['quantity'] ?? [];
        $unitCosts = $_POST['unit_cost'] ?? [];
        $supplier = trim($_POST['supplier'] ?? '');
        $note = trim($_POST['note'] ?? '');

        $productModel = new productModel();
        $categoryModel = new categoryModel();

        $lignes = [];

        foreach ($modes as $i => $mode) {
            $quantity = $quantities[$i] ?? '';
            $unitCost = $unitCosts[$i] ?? '';

            if ($quantity === '' && $unitCost === '') {
                continue;
            }

            if (!ctype_digit((string) $quantity) || (int) $quantity <= 0) {
                $this->redirectAvecErreur('Chaque quantité doit être un nombre entier positif.');
            }

            if (!is_numeric($unitCost) || (float) $unitCost < 0) {
                $this->redirectAvecErreur("Chaque prix d'achat doit être un nombre positif.");
            }

            if ($mode === 'nouveau') {
                $categoryId = trim($categoryIds[$i] ?? '');
                $name = trim($names[$i] ?? '');
                $description = trim($descriptions[$i] ?? '');

                if ($categoryId === '' || $name === '') {
                    $this->redirectAvecErreur('Veuillez indiquer la catégorie et le nom de chaque nouveau produit.');
                }

                if (!$categoryModel->find($categoryId)) {
                    $this->redirectAvecErreur('Catégorie introuvable pour un des nouveaux produits.');
                }

                $productId = $productModel->createDraft([
                    'category_id' => $categoryId,
                    'name' => $name,
                    'description' => $description,
                ]);

                $filename = $this->traiterImage($productId, $i);
                if ($filename) {
                    $productModel->updateImage($productId, $filename);
                }
            } else {
                $productId = trim($productIds[$i] ?? '');
                if ($productId === '' || !$productModel->find($productId)) {
                    $this->redirectAvecErreur('Un des produits sélectionnés est introuvable.');
                }
            }

            $lignes[] = [
                'product_id' => $productId,
                'quantity' => (int) $quantity,
                'unit_cost' => (float) $unitCost,
            ];
        }

        if (empty($lignes)) {
            $this->redirectAvecErreur('Veuillez ajouter au moins un produit acheté.');
        }

        $total = array_sum(array_map(fn($l) => $l['quantity'] * $l['unit_cost'], $lignes));

        $purchaseModel = new purchaseModel();
        $purchaseId = $purchaseModel->create($supplier, $_SESSION['user_id'], $total, $note);

        foreach ($lignes as $ligne) {
            $purchaseModel->addItem($purchaseId, $ligne['product_id'], $ligne['quantity'], $ligne['unit_cost']);

            $productModel->adjustStock(
                $ligne['product_id'],
                $ligne['quantity'],
                'reapprovisionnement',
                $supplier !== '' ? "Achat auprès de : $supplier" : 'Achat fournisseur',
                $_SESSION['user_id']
            );

            // Le prix d'achat peut varier d'une fois à l'autre : on garde toujours
            // le dernier payé, indépendamment du prix de vente déjà fixé.
            $productModel->updateCostPrice($ligne['product_id'], $ligne['unit_cost']);
        }

        $_SESSION['flash_success'] = 'Achat enregistré avec succès. Le stock a été mis à jour.';
        header('Location: ' . BASE_URL . '/achats');
        exit;
    }

    /**
     * Upload de l'image d'un nouveau produit (champ répété image[$index]),
     * avec vérification réelle du contenu image plutôt que la seule
     * extension déclarée. Retourne le nom de fichier, ou null si absente.
     */
    private function traiterImage(string $productId, int $index): ?string
    {
        $name = $_FILES['image']['name'][$index] ?? '';
        $error = $_FILES['image']['error'][$index] ?? UPLOAD_ERR_NO_FILE;
        $tmpName = $_FILES['image']['tmp_name'][$index] ?? '';
        $size = $_FILES['image']['size'][$index] ?? 0;

        if ($name === '' || $error === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($error !== UPLOAD_ERR_OK) {
            $this->redirectAvecErreur("Erreur lors de l'envoi d'une image.");
        }

        if ($size > self::IMAGE_TAILLE_MAX) {
            $this->redirectAvecErreur("Une image ne doit pas dépasser 2 Mo.");
        }

        $infosImage = @getimagesize($tmpName);
        $type = $infosImage['mime'] ?? null;

        if (!$type || !isset(self::IMAGE_TYPES_AUTORISES[$type])) {
            $this->redirectAvecErreur("Format d'image non supporté (JPG, PNG ou WEBP uniquement).");
        }

        $dossier = BASE_PATH . '/public/uploads/produits';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        $nomFichier = $productId . '.' . self::IMAGE_TYPES_AUTORISES[$type];
        move_uploaded_file($tmpName, $dossier . '/' . $nomFichier);

        return $nomFichier;
    }

    private function redirectAvecErreur(string $message): void
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_URL . '/achats');
        exit;
    }
}
