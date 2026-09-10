<?php

/**
 * homeController — Page d'accueil publique
 */

class homeController
{
    /**
     * Affiche la page d'accueil (GET /)
     */
    public function index(): void
    {
        if (!empty($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $categories = (new categoryModel())->allWithProductCount();
        $products = (new productModel())->all();

        require BASE_PATH . '/apps/views/home/index.php';
    }
}
