<?php

/**
 * productController — Gestion des produits (stock)
 */

class productController extends DashboardPage
{
    private const IMAGE_TAILLE_MAX = 2 * 1024 * 1024; // 2 Mo
    private const IMAGE_TYPES_AUTORISES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    public function index(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        if (!empty($_GET['historique'])) {
            $this->afficherHistorique((string) $_GET['historique']);
            return;
        }

        $model = new productModel();

        $this->render(BASE_PATH . '/apps/views/dashboard/produits_list.php', [
            'produits' => $model->all(),
            'produitsEnAttente' => Auth::isAdmin() ? $model->allPending() : [],
            'categories' => (new categoryModel())->all(),
            'csrfToken' => generateCsrfToken(),
        ]);
    }

    /**
     * Historique des mouvements de stock d'un produit (réapprovisionnement,
     * produit défectueux, ajustement, vente) — admin uniquement
     */
    private function afficherHistorique(string $productId): void
    {
        Auth::requireRole(['admin']);

        $product = (new productModel())->find($productId);
        if (!$product) {
            $this->redirectAvecErreur('Produit introuvable.');
        }

        $this->render(BASE_PATH . '/apps/views/dashboard/produit_historique.php', [
            'product' => $product,
            'movements' => (new stockMovementModel())->allByProduct($productId),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['admin']);

        if (!verifyCsrfToken($_POST['csrf_token'] ?? null)) {
            $this->redirectAvecErreur('Requête invalide, veuillez réessayer.');
        }

        $action = $_POST['action'] ?? '';

        match ($action) {
            'ajouter' => $this->ajouter(),
            'modifier' => $this->modifier(),
            'ajuster_stock' => $this->ajusterStock(),
            'publier' => $this->publier(),
            'supprimer' => $this->supprimer(),
            default => $this->redirectAvecErreur('Action inconnue.'),
        };
    }

    /**
     * Champs communs à l'ajout et à la modification (hors quantité : elle
     * ne se change que via l'action "Ajuster le stock", pour garder un
     * historique complet des mouvements)
     */
    private function lireEtValiderInfos(): array
    {
        $categoryId = trim($_POST['category_id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $unitPrice = $_POST['unit_price'] ?? '';
        $threshold = $_POST['low_stock_threshold'] ?? '10';

        if ($categoryId === '' || $name === '' || $unitPrice === '') {
            $this->redirectAvecErreur('Veuillez remplir la catégorie, le nom et le prix.');
        }

        if (!is_numeric($unitPrice) || (float) $unitPrice < 0) {
            $this->redirectAvecErreur('Le prix doit être un nombre positif.');
        }

        if (!ctype_digit((string) $threshold) || (int) $threshold < 1) {
            $threshold = 10;
        }

        if (!(new categoryModel())->find($categoryId)) {
            $this->redirectAvecErreur('Catégorie introuvable.');
        }

        return [
            'category_id' => $categoryId,
            'name' => $name,
            'description' => $description,
            'unit_price' => (float) $unitPrice,
            'low_stock_threshold' => (int) $threshold,
        ];
    }

    private function ajouter(): void
    {
        $data = $this->lireEtValiderInfos();

        $quantiteInitiale = $_POST['quantity'] ?? '0';
        if (!ctype_digit((string) $quantiteInitiale) || (int) $quantiteInitiale < 0) {
            $this->redirectAvecErreur('La quantité initiale doit être un nombre entier positif.');
        }
        $data['quantity'] = (int) $quantiteInitiale;

        $model = new productModel();
        $id = $model->create($data);

        if ($data['quantity'] > 0) {
            (new stockMovementModel())->create(
                $id,
                'reapprovisionnement',
                $data['quantity'],
                $data['quantity'],
                'Stock initial à la création du produit',
                $_SESSION['user_id']
            );
        }

        $filename = $this->traiterImage($id);
        if ($filename) {
            $model->updateImage($id, $filename);
        }

        $_SESSION['flash_success'] = 'Produit ajouté avec succès.';
        header('Location: ' . BASE_URL . '/produits');
        exit;
    }

    private function modifier(): void
    {
        $id = trim($_POST['id'] ?? '');
        $model = new productModel();

        if ($id === '' || !$model->find($id)) {
            $this->redirectAvecErreur('Produit introuvable.');
        }

        $data = $this->lireEtValiderInfos();
        $model->update($id, $data);

        $filename = $this->traiterImage($id);
        if ($filename) {
            $model->updateImage($id, $filename);
        }

        $_SESSION['flash_success'] = 'Produit modifié avec succès.';
        header('Location: ' . BASE_URL . '/produits');
        exit;
    }

    /**
     * Ajuste le stock d'un produit : réapprovisionnement, produit défectueux
     * ou ajustement manuel. Chaque action est enregistrée dans l'historique.
     */
    private function ajusterStock(): void
    {
        $id = trim($_POST['id'] ?? '');
        $model = new productModel();

        if ($id === '' || !$model->find($id)) {
            $this->redirectAvecErreur('Produit introuvable.');
        }

        $type = $_POST['movement_type'] ?? '';
        $sens = $_POST['sens'] ?? '';
        $quantity = $_POST['movement_quantity'] ?? '';
        $reason = trim($_POST['reason'] ?? '');

        if (!in_array($type, ['reapprovisionnement', 'produit_defectueux', 'ajustement'], true)) {
            $this->redirectAvecErreur('Type de mouvement invalide.');
        }

        if (!in_array($sens, ['ajouter', 'retirer'], true)) {
            $this->redirectAvecErreur('Veuillez indiquer si le stock doit augmenter ou diminuer.');
        }

        if (!ctype_digit((string) $quantity) || (int) $quantity <= 0) {
            $this->redirectAvecErreur('La quantité doit être un nombre entier positif.');
        }

        $delta = $sens === 'ajouter' ? (int) $quantity : -(int) $quantity;

        try {
            $model->adjustStock($id, $delta, $type, $reason ?: null, $_SESSION['user_id']);
        } catch (RuntimeException $e) {
            $this->redirectAvecErreur('Stock insuffisant pour ce retrait.');
        }

        $_SESSION['flash_success'] = 'Stock mis à jour avec succès.';
        header('Location: ' . BASE_URL . '/produits');
        exit;
    }

    /**
     * Ajoute au catalogue un produit créé depuis un achat (sans prix de
     * vente) : l'admin fixe sa description finale et son prix, ce qui le
     * rend visible et vendable dans /produits
     */
    private function publier(): void
    {
        $id = trim($_POST['id'] ?? '');
        $model = new productModel();
        $product = $model->find($id);

        if ($id === '' || !$product) {
            $this->redirectAvecErreur('Produit introuvable.');
        }

        $description = trim($_POST['description'] ?? '');
        $unitPrice = $_POST['unit_price'] ?? '';
        $threshold = $_POST['low_stock_threshold'] ?? '10';

        if ($unitPrice === '' || !is_numeric($unitPrice) || (float) $unitPrice < 0) {
            $this->redirectAvecErreur('Veuillez indiquer un prix de vente valide.');
        }

        if (!ctype_digit((string) $threshold) || (int) $threshold < 1) {
            $threshold = 10;
        }

        $model->publish($id, [
            'description' => $description,
            'unit_price' => (float) $unitPrice,
            'low_stock_threshold' => (int) $threshold,
        ]);

        $filename = $this->traiterImage($id);
        if ($filename) {
            $model->updateImage($id, $filename);
        }

        $_SESSION['flash_success'] = 'Produit ajouté au catalogue avec succès.';
        header('Location: ' . BASE_URL . '/produits');
        exit;
    }

    /**
     * Upload de l'image du produit (optionnelle) : nom de fichier = id produit,
     * avec vérification réelle du contenu image (getimagesize) plutôt que la
     * seule extension déclarée. Retourne le nom de fichier, ou null si aucune
     * image n'a été envoyée.
     */
    private function traiterImage(string $productId): ?string
    {
        $fichier = $_FILES['image'] ?? null;

        if (!$fichier || $fichier['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($fichier['error'] !== UPLOAD_ERR_OK) {
            $this->redirectAvecErreur("Erreur lors de l'envoi de l'image.");
        }

        if ($fichier['size'] > self::IMAGE_TAILLE_MAX) {
            $this->redirectAvecErreur("L'image ne doit pas dépasser 2 Mo.");
        }

        $infosImage = @getimagesize($fichier['tmp_name']);
        $type = $infosImage['mime'] ?? null;

        if (!$type || !isset(self::IMAGE_TYPES_AUTORISES[$type])) {
            $this->redirectAvecErreur("Format d'image non supporté (JPG, PNG ou WEBP uniquement).");
        }

        $dossier = BASE_PATH . '/public/uploads/produits';
        if (!is_dir($dossier)) {
            mkdir($dossier, 0755, true);
        }

        $extension = self::IMAGE_TYPES_AUTORISES[$type];

        foreach (self::IMAGE_TYPES_AUTORISES as $ext) {
            $ancien = $dossier . '/' . $productId . '.' . $ext;
            if (is_file($ancien)) {
                unlink($ancien);
            }
        }

        $nomFichier = $productId . '.' . $extension;
        move_uploaded_file($fichier['tmp_name'], $dossier . '/' . $nomFichier);

        return $nomFichier;
    }

    private function supprimer(): void
    {
        $id = trim($_POST['id'] ?? '');
        $model = new productModel();
        $product = $model->find($id);

        if ($id === '' || !$product) {
            $this->redirectAvecErreur('Produit introuvable.');
        }

        if (!empty($product['image'])) {
            $chemin = BASE_PATH . '/public/uploads/produits/' . $product['image'];
            if (is_file($chemin)) {
                unlink($chemin);
            }
        }

        $model->delete($id);

        $_SESSION['flash_success'] = 'Produit supprimé avec succès.';
        header('Location: ' . BASE_URL . '/produits');
        exit;
    }

    private function redirectAvecErreur(string $message): void
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_URL . '/produits');
        exit;
    }
}
