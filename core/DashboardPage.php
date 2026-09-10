<?php

/**
 * Classe de base partagée par tous les contrôleurs du tableau de bord :
 * menu latéral groupé (filtré par rôle) + rendu via le layout commun.
 */

abstract class DashboardPage
{
    protected const MENU_GROUPS = [
        [
            'heading' => null,
            'items' => [
                ['label' => 'Tableau de bord', 'route' => '/dashboard', 'icon' => 'home', 'roles' => ['admin', 'vendeur']],
            ],
        ],
        [
            'heading' => 'Catalogue',
            'items' => [
                ['label' => 'Catalogue', 'route' => '/catalogue', 'icon' => 'grid', 'roles' => ['admin', 'vendeur']],
                ['label' => 'Produits', 'route' => '/produits', 'icon' => 'package', 'roles' => ['admin', 'vendeur']],
                ['label' => 'Catégories', 'route' => '/categories', 'icon' => 'layers', 'roles' => ['admin', 'vendeur']],
            ],
        ],
        [
            'heading' => 'Opérations',
            'items' => [
                ['label' => 'Achats', 'route' => '/achats', 'icon' => 'truck', 'roles' => ['admin']],
                ['label' => 'Ventes', 'route' => '/ventes', 'icon' => 'receipt', 'roles' => ['admin', 'vendeur']],
                ['label' => 'Caisse & retraits', 'route' => '/caisse', 'icon' => 'wallet', 'roles' => ['admin', 'vendeur']],
            ],
        ],
        [
            'heading' => 'Administration',
            'items' => [
                ['label' => 'Comptes', 'route' => '/utilisateurs', 'icon' => 'users', 'roles' => ['admin', 'vendeur']],
            ],
        ],
    ];

    protected const BOTTOM_ITEMS = [];

    /**
     * Icônes SVG (style Lucide) en HTML natif, sans dépendance npm/React
     */
    private const ICON_PATHS = [
        'home' => '<path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'grid' => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>',
        'package' => '<path d="M16.5 9.4 7.5 4.21"/><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" x2="12" y1="22.08" y2="12"/>',
        'layers' => '<path d="m12.83 2.18a2 2 0 0 0-1.66 0L2.6 6.08a1 1 0 0 0 0 1.83l8.58 3.91a2 2 0 0 0 1.66 0l8.58-3.9a1 1 0 0 0 0-1.83Z"/><path d="m22 17.65-9.17 4.16a2 2 0 0 1-1.66 0L2 17.65"/><path d="m22 12.65-9.17 4.16a2 2 0 0 1-1.66 0L2 12.65"/>',
        'truck' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>',
        'receipt' => '<path d="M4 2v20l2-1 2 1 2-1 2 1 2-1 2 1 2-1 2 1V2l-2 1-2-1-2 1-2-1-2 1-2-1-2 1Z"/><path d="M8 7h8"/><path d="M8 11h8"/><path d="M8 15h5"/>',
        'wallet' => '<path d="M21 12V7H5a2 2 0 0 1 0-4h14v4"/><path d="M3 5v14a2 2 0 0 0 2 2h16v-5"/><path d="M18 12a2 2 0 0 0 0 4h4v-4Z"/>',
        'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'message-circle' => '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
        'wrench' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'droplet' => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.5-4 6.5s-3 3.5-3 5.5a7 7 0 0 0 7 7z"/>',
        'zap' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>',
        'history' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>',
    ];

    public static function icon(string $name, string $class = 'w-[18px] h-[18px]'): string
    {
        $path = self::ICON_PATHS[$name] ?? '';
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" '
            . 'stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . htmlspecialchars($class) . '">'
            . $path . '</svg>';
    }

    private function filtrerParRole(array $items): array
    {
        return array_values(array_filter(
            $items,
            fn(array $item) => in_array($_SESSION['user_role'], $item['roles'], true)
        ));
    }

    /**
     * Capture le contenu d'une vue puis l'injecte dans le layout partagé (sidebar + topbar)
     */
    protected function render(string $contentFile, array $vars = []): void
    {
        extract($vars);

        ob_start();
        require $contentFile;
        $content = ob_get_clean();

        $menuGroups = [];
        foreach (static::MENU_GROUPS as $group) {
            $items = $this->filtrerParRole($group['items']);
            if (!empty($items)) {
                $menuGroups[] = ['heading' => $group['heading'], 'items' => $items];
            }
        }
        $bottomItems = $this->filtrerParRole(static::BOTTOM_ITEMS);

        $currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($basePath !== '' && str_starts_with($currentRoute, $basePath)) {
            $currentRoute = substr($currentRoute, strlen($basePath));
        }

        $unreadMessagesCount = (new messageModel())->nombreNonLus($_SESSION['user_id']);

        require BASE_PATH . '/apps/views/dashboard/layout.php';
    }
}
