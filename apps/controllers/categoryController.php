<?php

/**
 * categoryController — Gestion des catégories de produits
 */

class categoryController extends DashboardPage
{
    public function index(): void
    {
        Auth::requireRole(['admin', 'vendeur']);

        $this->render(BASE_PATH . '/apps/views/dashboard/categories_list.php', [
            'categories' => (new categoryModel())->all(),
            'csrfToken' => generateCsrfToken(),
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
            'supprimer' => $this->supprimer(),
            default => $this->redirectAvecErreur('Action inconnue.'),
        };
    }

    private function ajouter(): void
    {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($name === '') {
            $this->redirectAvecErreur('Le nom de la catégorie est obligatoire.');
        }

        $model = new categoryModel();
        if ($model->findByName($name)) {
            $this->redirectAvecErreur('Cette catégorie existe déjà.');
        }

        $model->create($name, $description);

        $_SESSION['flash_success'] = 'Catégorie ajoutée avec succès.';
        header('Location: ' . BASE_URL . '/categories');
        exit;
    }

    private function modifier(): void
    {
        $id = trim($_POST['id'] ?? '');
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($id === '' || $name === '') {
            $this->redirectAvecErreur('Le nom de la catégorie est obligatoire.');
        }

        $model = new categoryModel();
        if (!$model->find($id)) {
            $this->redirectAvecErreur('Catégorie introuvable.');
        }

        $existing = $model->findByName($name);
        if ($existing && $existing['id'] !== $id) {
            $this->redirectAvecErreur('Une autre catégorie porte déjà ce nom.');
        }

        $model->update($id, $name, $description);

        $_SESSION['flash_success'] = 'Catégorie modifiée avec succès.';
        header('Location: ' . BASE_URL . '/categories');
        exit;
    }

    private function supprimer(): void
    {
        $id = trim($_POST['id'] ?? '');
        $model = new categoryModel();

        if ($id === '' || !$model->find($id)) {
            $this->redirectAvecErreur('Catégorie introuvable.');
        }

        if ($model->countProducts($id) > 0) {
            $this->redirectAvecErreur('Impossible de supprimer : des produits sont rattachés à cette catégorie.');
        }

        $model->delete($id);

        $_SESSION['flash_success'] = 'Catégorie supprimée avec succès.';
        header('Location: ' . BASE_URL . '/categories');
        exit;
    }

    private function redirectAvecErreur(string $message): void
    {
        $_SESSION['flash_error'] = $message;
        header('Location: ' . BASE_URL . '/categories');
        exit;
    }
}
