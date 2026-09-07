<?php
/** @var array $menuGroups */
/** @var array $bottomItems */
/** @var string $currentRoute */
/** @var string $content */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord — Quincaillerie de la Liberté</title>
    <link href="<?= BASE_URL ?>/public/css/output.css?v=<?= filemtime(BASE_PATH . '/public/css/output.css') ?>" rel="stylesheet">
</head>

<body class="bg-navy-950 text-white min-h-screen overflow-x-hidden">

    <!-- Topbar mobile -->
    <div class="md:hidden flex items-center justify-between bg-navy-950/90 backdrop-blur-md border-b border-white/10 text-white px-4 h-16 sticky top-0 z-30">
        <span class="flex items-center gap-2 font-semibold">
            <img src="<?= BASE_URL ?>/public/images/logo.png" alt="" class="w-11 h-11 object-contain rounded bg-white p-0.5">
            Quincaillerie
        </span>
        <div class="flex items-center gap-2">
            <a href="<?= BASE_URL ?>/transactions" aria-label="Transactions"
                class="p-2 rounded hover:bg-white/10 <?= $currentRoute === '/transactions' ? 'text-brand-orange' : '' ?>">
                <?= DashboardPage::icon('history', 'w-6 h-6') ?>
            </a>
            <a href="<?= BASE_URL ?>/messages" aria-label="Messages"
                class="relative p-2 rounded hover:bg-white/10 <?= $currentRoute === '/messages' ? 'text-brand-orange' : '' ?>">
                <?= DashboardPage::icon('message-circle', 'w-6 h-6') ?>
                <?php if ($unreadMessagesCount > 0): ?>
                    <span class="absolute top-1 right-1 min-w-[16px] h-4 px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
                        <?= $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount ?>
                    </span>
                <?php endif; ?>
            </a>
            <button id="menu-toggle" aria-label="Ouvrir le menu" class="p-2 rounded hover:bg-white/10">
                <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Navbar desktop -->
    <div class="hidden md:flex items-center justify-end md:ml-64 bg-navy-950/90 backdrop-blur-md border-b border-white/10 px-8 h-16 sticky top-0 z-20">
        <a href="<?= BASE_URL ?>/transactions"
            class="flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition <?= $currentRoute === '/transactions' ? 'bg-brand-orange text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
            <?= DashboardPage::icon('history', 'w-4 h-4') ?>
            Transactions
        </a>
        <a href="<?= BASE_URL ?>/messages"
            class="relative flex items-center gap-2 px-4 py-2 rounded-md text-sm font-medium transition <?= $currentRoute === '/messages' ? 'bg-brand-orange text-white' : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
            <?= DashboardPage::icon('message-circle', 'w-4 h-4') ?>
            Messages
            <?php if ($unreadMessagesCount > 0): ?>
                <span class="min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-red-500 text-white text-[10px] font-bold leading-none">
                    <?= $unreadMessagesCount > 9 ? '9+' : $unreadMessagesCount ?>
                </span>
            <?php endif; ?>
        </a>
    </div>

    <!-- Fond assombri derrière la sidebar mobile ouverte -->
    <div id="sidebar-backdrop" class="hidden fixed inset-0 bg-black/60 z-30 md:hidden"></div>

    <div>
        <aside id="sidebar"
            class="fixed inset-y-0 left-0 z-40 w-72 h-screen -translate-x-full transition-transform duration-300 ease-out
                   md:translate-x-0 md:w-64
                   flex flex-col bg-navy-950 border-r border-white/10 text-white shrink-0">
            <div class="px-4 py-4 border-b border-white/10 flex items-center justify-between gap-3">
                <a href="<?= BASE_URL ?>/dashboard" class="flex items-center gap-3 min-w-0">
                    <img src="<?= BASE_URL ?>/public/images/logo.png" alt="Quincaillerie de la Liberté"
                        class="w-12 h-12 shrink-0 object-contain rounded-lg bg-white p-1">
                    <span class="text-sm font-semibold leading-tight truncate">Quincaillerie<br><span class="text-[11px] font-normal text-white/50">de la Liberté</span></span>
                </a>
                <button id="sidebar-close" aria-label="Fermer le menu" class="md:hidden p-1 rounded hover:bg-white/10 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <nav class="px-3 py-3 space-y-3 text-sm overflow-y-auto">
                <?php foreach ($menuGroups as $group): ?>
                    <div>
                        <?php if ($group['heading']): ?>
                            <p class="px-3 text-[11px] uppercase tracking-wider text-white/40 mb-0.5">
                                <?= htmlspecialchars($group['heading']) ?>
                            </p>
                        <?php endif; ?>
                        <?php foreach ($group['items'] as $item): ?>
                            <?php $active = $currentRoute === $item['route']; ?>
                            <a href="<?= BASE_URL . $item['route'] ?>"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md transition <?= $active ? 'bg-brand-orange text-white font-medium' : 'hover:bg-white/10 text-white/70' ?>">
                                <span><?= DashboardPage::icon($item['icon'], 'w-4 h-4') ?></span>
                                <span><?= htmlspecialchars($item['label']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>

                <?php if (!empty($bottomItems)): ?>
                    <div class="pt-2 border-t border-white/10">
                        <?php foreach ($bottomItems as $item): ?>
                            <?php $active = $currentRoute === $item['route']; ?>
                            <a href="<?= BASE_URL . $item['route'] ?>"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-md transition <?= $active ? 'bg-brand-orange text-white font-medium' : 'hover:bg-white/10 text-white/70' ?>">
                                <span><?= DashboardPage::icon($item['icon'], 'w-4 h-4') ?></span>
                                <span><?= htmlspecialchars($item['label']) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </nav>

            <div class="px-3 py-3 border-t border-white/10">
                <p class="px-3 text-xs text-white/90"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></p>
                <p class="px-3 text-[11px] text-white/40 mb-2"><?= Auth::isAdmin() ? 'Administrateur' : 'Vendeur' ?></p>
                <form method="POST" action="<?= BASE_URL ?>/logout">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(generateCsrfToken()) ?>">
                    <button type="submit"
                        class="w-full text-left px-3 py-1.5 text-sm rounded-md hover:bg-white/10 text-red-400">
                        Déconnexion
                    </button>
                </form>
            </div>
        </aside>

        <main class="min-w-0 md:ml-64 p-4 md:p-8">
            <?= $content ?>
        </main>
    </div>

    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const closeBtn = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebar-backdrop');
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
            iconOpen.classList.add('hidden');
            iconClose.classList.remove('hidden');
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }

        toggleBtn?.addEventListener('click', () => {
            sidebar.classList.contains('-translate-x-full') ? openSidebar() : closeSidebar();
        });
        closeBtn?.addEventListener('click', closeSidebar);
        backdrop?.addEventListener('click', closeSidebar);
    </script>

</body>

</html>
