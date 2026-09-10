<?php

/**
 * catalogueController — Consultation du catalogue des produits par catégorie
 */
class catalogueController extends DashboardPage
{
    /**
     * Page principale du catalogue (GET /catalogue)
     */
    public function index(): void
    {
        $categories = (new categoryModel())->all();
        $products = (new productModel())->all();

        // Structure par catégorie
        $productsByCategory = [];
        foreach ($categories as $cat) {
            $productsByCategory[$cat['id']] = [
                'category' => $cat,
                'products' => []
            ];
        }

        // Répartition des produits publiés dans leur catégorie
        foreach ($products as $p) {
            if (isset($productsByCategory[$p['category_id']])) {
                $productsByCategory[$p['category_id']]['products'][] = $p;
            }
        }

        $prices = array_column($products, 'unit_price');
        $minPrice = !empty($prices) ? floor(min($prices)) : 0;
        $maxPrice = !empty($prices) ? ceil(max($prices)) : 10000;

        $totalProducts = count($products);

        // Si l'utilisateur est connecté (vendeur ou admin), on affiche dans le tableau de bord
        if (!empty($_SESSION['user_id'])) {
            Auth::requireRole(['admin', 'vendeur']);
            $this->render(BASE_PATH . '/apps/views/dashboard/catalogue.php', [
                'categories' => $categories,
                'products' => $products,
                'productsByCategory' => $productsByCategory,
                'totalProducts' => $totalProducts,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
            ]);
        } else {
            // Sinon vue publique avec header/footer du site
            require BASE_PATH . '/apps/views/home/catalogue_public.php';
        }
    }
}
