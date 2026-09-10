<?php

/** @var array $categories */
/** @var array $products */
/** @var float $minPrice */
/** @var float $maxPrice */

$minPrice = isset($minPrice) && is_numeric($minPrice) ? (float)$minPrice : 0;
$maxPrice = isset($maxPrice) && is_numeric($maxPrice) ? (float)$maxPrice : 10000;
$categories = $categories ?? [];
$products = $products ?? [];
$totalProducts = isset($products) ? count($products) : ($totalProducts ?? 0);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue — Quincaillerie de la Liberté</title>

    <!-- Ton CSS compilé -->
    <link href="<?= BASE_URL ?>/public/css/output.css" rel="stylesheet">

    <!-- ✅ SECOURS : Tailwind CDN (génère toutes les classes manquantes) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* ===== Double slider prix (orange) ===== */
        .range-wrap {
            position: relative;
            height: 18px;
        }

        .range-track,
        .range-fill {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            height: 4px;
            border-radius: 9999px;
        }

        .range-track {
            width: 100%;
            background: #E8EAF0;
        }

        .range-fill {
            background: #F97316;
        }

        .range-input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 18px;
            margin: 0;
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
            pointer-events: none;
        }

        .range-input::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            pointer-events: auto;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #F97316;
            border: 2.5px solid #fff;
            box-shadow: 0 1px 4px rgba(249, 115, 22, .5);
            cursor: grab;
        }

        .range-input::-moz-range-thumb {
            pointer-events: auto;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background: #F97316;
            border: 2.5px solid #fff;
            box-shadow: 0 1px 4px rgba(249, 115, 22, .5);
            cursor: grab;
        }

        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .favorite-btn.active svg {
            fill: #FF3366;
            stroke: #FF3366;
        }

        .favorite-btn.active {
            color: #FF3366;
        }
    </style>
</head>

<body class="bg-[#EEF2F6] text-gray-800 flex flex-col min-h-screen antialiased" style="font-family:'Inter',system-ui,sans-serif;">

    <!-- ================= NAVBAR ================= -->
    <header class="bg-white border-b border-gray-100 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <a href="<?= BASE_URL ?>/" class="flex items-center gap-2 font-bold text-lg text-gray-900">
                    <!-- ✅ logo : taille forcée en inline style -->
                    <img src="<?= BASE_URL ?>/public/images/logo.png" alt="Quincaillerie de la Liberté"
                        class="w-10 h-10 object-contain"
                        style="width:40px;height:40px;object-fit:contain;flex-shrink:0;"
                        onerror="this.style.display='none'">
                    <span class="hidden sm:inline">Quincaillerie de la Liberté</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-500">
                    <a href="<?= BASE_URL ?>/#accueil" class="hover:text-orange-500 transition">Accueil</a>
                    <a href="<?= BASE_URL ?>/#produits" class="hover:text-orange-500 transition">Nos produits</a>
                    <a href="<?= BASE_URL ?>/catalogue" class="text-orange-500 font-bold border-b-2 border-orange-500 py-5">Catalogue</a>
                    <a href="<?= BASE_URL ?>/#map-section" class="hover:text-orange-500 transition">Nous trouver</a>
                </nav>

                <a href="<?= BASE_URL ?>/login"
                    class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-5 py-2 rounded-lg transition shadow-sm">
                    Connexion
                </a>
            </div>
        </div>
    </header>

    <!-- ================= PAGE CATALOGUE ================= -->
    <main class="flex-grow w-full max-w-[1200px] mx-auto px-3 sm:px-6 py-6 sm:py-12">
        <div class="bg-white rounded-[24px] sm:rounded-[28px] shadow-sm px-5 py-7 sm:px-10 sm:py-10">

            <!-- ===== En-tête ===== -->
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8">
                <div>
                    <p class="text-[11px] font-medium text-gray-400 mb-1">
                        <a href="<?= BASE_URL ?>/" class="hover:text-gray-600 transition">Main</a>
                        <span class="mx-1">/</span>
                        <span class="text-gray-600">Catalog</span>
                    </p>
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-[26px] sm:text-3xl font-extrabold text-gray-900 tracking-tight leading-none">Catalog</h1>
                        <span id="filter-badge"
                            class="w-[22px] h-[22px] rounded-full bg-orange-500 text-white text-[11px] font-bold hidden items-center justify-center">0</span>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Icônes vue : width/height en dur -->
                    <div class="flex items-center gap-2.5">
                        <button type="button" id="btn-grid" title="Vue grille">
                            <svg width="18" height="18" class="text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button type="button" id="btn-list" title="Vue liste">
                            <svg width="18" height="18" class="text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>

                    <!-- Tri -->
                    <div class="relative">
                        <select id="sort-select"
                            class="appearance-none bg-transparent text-sm font-bold text-gray-800 pr-6 py-1 cursor-pointer focus:outline-none">
                            <option value="popular">Popular First</option>
                            <option value="price-asc">Price: Low to High</option>
                            <option value="price-desc">Price: High to Low</option>
                            <option value="name-asc">Name: A to Z</option>
                        </select>
                        <svg width="14" height="14" class="text-gray-400 absolute right-0 top-1.5 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- ===== Corps : sidebar + grille ===== -->
            <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-10 items-start">

                <!-- ============ SIDEBAR ============ -->
                <aside class="space-y-7">

                    <!-- Category -->
                    <section>
                        <div class="flex items-center justify-between mb-4 cursor-pointer select-none" onclick="toggleGroup('cat-body', this)">
                            <h3 class="text-[15px] font-bold text-gray-900">Category</h3>
                            <svg width="14" height="14" class="text-gray-400 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div id="cat-body" class="space-y-3">
                            <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" value="all" checked class="cat-check w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                <span>All</span>
                            </label>
                            <?php foreach ($categories as $cat): ?>
                                <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" value="<?= htmlspecialchars($cat['id']) ?>" class="cat-check w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                    <span><?= htmlspecialchars($cat['name']) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Price Range -->
                    <section>
                        <div class="flex items-center justify-between mb-4 cursor-pointer select-none" onclick="toggleGroup('price-body', this)">
                            <h3 class="text-[15px] font-bold text-gray-900">Price Range</h3>
                            <svg width="14" height="14" class="text-gray-400 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div id="price-body">
                            <div class="flex items-center gap-2.5 mb-5">
                                <input type="number" id="price-min" value="<?= (int)$minPrice ?>" min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>"
                                    class="w-full text-[13px] font-semibold text-gray-700 text-center border border-gray-200 rounded-lg py-2 focus:outline-none focus:border-orange-400 transition">
                                <span class="text-gray-300 text-xs font-bold">—</span>
                                <input type="number" id="price-max" value="<?= (int)$maxPrice ?>" min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>"
                                    class="w-full text-[13px] font-semibold text-gray-700 text-center border border-gray-200 rounded-lg py-2 focus:outline-none focus:border-orange-400 transition">
                            </div>
                            <div class="range-wrap">
                                <div class="range-track"></div>
                                <div class="range-fill" id="range-fill"></div>
                                <input type="range" id="range-min" class="range-input" min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>" value="<?= (int)$minPrice ?>">
                                <input type="range" id="range-max" class="range-input" min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>" value="<?= (int)$maxPrice ?>">
                            </div>
                        </div>
                    </section>

                    <!-- Brand -->
                    <section>
                        <div class="flex items-center justify-between mb-4 cursor-pointer select-none" onclick="toggleGroup('brand-body', this)">
                            <h3 class="text-[15px] font-bold text-gray-900">Brand</h3>
                            <svg width="14" height="14" class="text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                        <div id="brand-body" class="hidden space-y-3">
                            <?php foreach (['Toutes marques', 'Bosch', 'Stanley', 'DeWalt'] as $brand): ?>
                                <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                    <input type="checkbox" class="w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                    <span><?= htmlspecialchars($brand) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- Color -->
                    <section>
                        <div class="flex items-center justify-between mb-4 cursor-pointer select-none" onclick="toggleGroup('color-body', this)">
                            <h3 class="text-[15px] font-bold text-gray-900">Color</h3>
                            <svg width="14" height="14" class="text-gray-400 transition-transform duration-200 rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        <div id="color-body" class="space-y-3">
                            <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" checked class="w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                <span>All</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" class="w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                <span>White</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-[13px] font-semibold text-orange-500 cursor-pointer">
                                <input type="checkbox" checked class="w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                <span>Orange</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" class="w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                <span>Black</span>
                            </label>
                            <label class="flex items-center gap-2.5 text-[13px] font-medium text-gray-700 cursor-pointer">
                                <input type="checkbox" class="w-[15px] h-[15px] rounded accent-orange-500 focus:ring-0 border-gray-300">
                                <span>Silver</span>
                            </label>
                        </div>
                    </section>

                    <!-- Only in Stock -->
                    <div class="flex items-center justify-between !mt-8">
                        <span class="text-[13px] font-bold text-gray-900">Only in Stock</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="stock-toggle" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-orange-500
                                after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white
                                after:rounded-full after:h-4 after:w-4 after:transition-all
                                peer-checked:after:translate-x-full after:shadow-sm"></div>
                        </label>
                    </div>

                    <!-- Boutons -->
                    <div class="flex items-center gap-2.5 !mt-8">
                        <button type="button" id="btn-apply"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white text-[13px] font-bold py-2.5 rounded-lg transition shadow-sm shadow-orange-500/30">
                            <span id="count-label"><?= number_format($totalProducts, 0, '.', ' ') ?> items</span>
                        </button>
                        <button type="button" id="btn-clear"
                            class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-600 text-[13px] font-semibold rounded-lg transition">
                            Clear
                        </button>
                    </div>
                </aside>

                <!-- ============ GRILLE PRODUITS ============ -->
                <section>
                    <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

                        <?php foreach ($products as $i => $p):
                            $isOut    = (int)$p['quantity'] === 0;
                            $imageUrl = !empty($p['image']) ? BASE_URL . '/public/uploads/produits/' . $p['image'] : null;
                            $hasPromo = !empty($p['old_price']) && $p['old_price'] > $p['unit_price'];
                            $promoPct = $hasPromo ? round((1 - $p['unit_price'] / $p['old_price']) * 100) : 0;
                        ?>
                            <article class="product-item group relative bg-[#F7F8FA] hover:bg-[#F3F5F8] rounded-2xl p-4 pb-5 flex flex-col transition-all duration-300 hover:shadow-lg hover:shadow-gray-200/60 hover:-translate-y-1"
                                data-index="<?= $i ?>"
                                data-cat-id="<?= htmlspecialchars($p['category_id']) ?>"
                                data-price="<?= (float)$p['unit_price'] ?>"
                                data-stock="<?= (int)$p['quantity'] ?>">

                                <!-- Badge + Favori -->
                                <div class="flex items-start justify-between h-6">
                                    <div>
                                        <?php if ($hasPromo): ?>
                                            <span class="px-2 py-1 rounded-lg bg-[#FF3366] text-white text-[11px] font-bold">-<?= $promoPct ?>%</span>
                                        <?php elseif ($i === 5): ?>
                                            <span class="px-2 py-1 rounded-lg bg-[#FF3366] text-white text-[11px] font-bold">Special</span>
                                        <?php elseif ($isOut): ?>
                                            <span class="px-2 py-1 rounded-lg bg-gray-400 text-white text-[10px] font-bold">Out of stock</span>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="favorite-btn w-7 h-7 -mr-1 -mt-1 flex items-center justify-center text-gray-300 hover:text-[#FF3366] transition" aria-label="Favori">
                                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Image : taille limitée en inline style aussi -->
                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                    class="h-[150px] flex items-center justify-center p-2 my-3"
                                    style="height:150px;display:flex;align-items:center;justify-content:center;">
                                    <?php if ($imageUrl): ?>
                                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy"
                                            class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300"
                                            style="max-height:100%;max-width:100%;object-fit:contain;"
                                            onerror="this.style.display='none'">
                                    <?php else: ?>
                                        <svg width="48" height="48" class="text-gray-300" fill="none" stroke="currentColor" stroke-width="1" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                    <?php endif; ?>
                                </a>

                                <!-- Nom + catégorie -->
                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>" class="mt-auto">
                                    <h3 class="text-[13.5px] font-bold text-gray-900 leading-snug line-clamp-1 group-hover:text-orange-500 transition">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </h3>
                                </a>
                                <p class="text-[11.5px] text-gray-400 font-medium mt-0.5"><?= htmlspecialchars($p['category_name'] ?? '') ?></p>

                                <!-- Prix / Buy -->
                                <div class="mt-2.5 min-h-[34px] flex items-center">
                                    <div class="flex items-baseline gap-2 group-hover:hidden">
                                        <span class="text-[15px] font-extrabold text-gray-900">
                                            <?= number_format($p['unit_price'], 2, '.', ' ') ?>
                                        </span>
                                        <?php if ($hasPromo): ?>
                                            <span class="text-xs text-gray-400 line-through font-medium">
                                                <?= number_format($p['old_price'], 2, '.', ' ') ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="hidden group-hover:flex w-full items-center gap-2">
                                        <?php if ($isOut): ?>
                                            <span class="flex-1 text-center bg-gray-200 text-gray-400 text-xs font-bold py-2 rounded-lg cursor-not-allowed">Indisponible</span>
                                        <?php else: ?>
                                            <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                                class="flex-1 text-center bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-2 rounded-lg transition shadow-sm">
                                                Buy <?= number_format($p['unit_price'], 2, '.', ' ') ?>
                                            </a>
                                            <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                                class="w-9 h-8 flex items-center justify-center rounded-lg border border-gray-200 bg-white hover:border-orange-400 hover:text-orange-500 text-gray-500 transition">
                                                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <!-- État vide -->
                    <div id="empty-state" class="hidden py-16 text-center">
                        <p class="text-sm font-bold text-gray-500 mb-2">Aucun produit ne correspond à vos critères.</p>
                        <button type="button" onclick="clearFilters()" class="text-xs font-bold text-orange-500 hover:underline">Réinitialiser les filtres</button>
                    </div>

                    <!-- Show More -->
                    <div id="show-more-wrap" class="text-center mt-10">
                        <button type="button" id="btn-show-more"
                            class="px-7 py-2.5 rounded-lg bg-orange-50 hover:bg-orange-100 border border-orange-200 text-orange-500 text-xs font-bold transition">
                            Show More
                        </button>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-gray-100 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-gray-400">
            &copy; <?= date('Y') ?> Quincaillerie de la Liberté. Tous droits réservés.
        </div>
    </footer>

    <script>
        const MIN = <?= (float)$minPrice ?>;
        const MAX = <?= (float)$maxPrice ?>;
        const PAGE_SIZE = 6;

        const grid = document.getElementById('product-grid');
        const items = Array.from(document.querySelectorAll('.product-item'));
        const catChecks = Array.from(document.querySelectorAll('.cat-check'));
        const catAll = document.querySelector('.cat-check[value="all"]');
        const priceMin = document.getElementById('price-min');
        const priceMax = document.getElementById('price-max');
        const rangeMin = document.getElementById('range-min');
        const rangeMax = document.getElementById('range-max');
        const rangeFill = document.getElementById('range-fill');
        const stockToggle = document.getElementById('stock-toggle');
        const sortSelect = document.getElementById('sort-select');
        const countLabel = document.getElementById('count-label');
        const badge = document.getElementById('filter-badge');
        const emptyState = document.getElementById('empty-state');
        const moreWrap = document.getElementById('show-more-wrap');
        const btnMore = document.getElementById('btn-show-more');

        let page = 1;

        function toggleGroup(bodyId, header) {
            document.getElementById(bodyId).classList.toggle('hidden');
            header.querySelector('svg').classList.toggle('rotate-180');
        }

        document.querySelectorAll('.favorite-btn').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                btn.classList.toggle('active');
            });
        });

        /* ----- Double slider ----- */
        function updateFill() {
            const lo = +rangeMin.value,
                hi = +rangeMax.value;
            const span = (MAX - MIN) || 1;
            rangeFill.style.left = ((lo - MIN) / span * 100) + '%';
            rangeFill.style.width = ((hi - lo) / span * 100) + '%';
        }
        rangeMin.addEventListener('input', () => {
            if (+rangeMin.value > +rangeMax.value) rangeMin.value = rangeMax.value;
            priceMin.value = rangeMin.value;
            updateFill();
            apply();
        });
        rangeMax.addEventListener('input', () => {
            if (+rangeMax.value < +rangeMin.value) rangeMax.value = rangeMin.value;
            priceMax.value = rangeMax.value;
            updateFill();
            apply();
        });
        priceMin.addEventListener('change', () => {
            let v = parseInt(priceMin.value);
            if (isNaN(v)) v = MIN;
            v = Math.min(Math.max(v, MIN), +priceMax.value || MAX);
            priceMin.value = v;
            rangeMin.value = v;
            updateFill();
            apply();
        });
        priceMax.addEventListener('change', () => {
            let v = parseInt(priceMax.value);
            if (isNaN(v)) v = MAX;
            v = Math.max(Math.min(v, MAX), +priceMin.value || MIN);
            priceMax.value = v;
            rangeMax.value = v;
            updateFill();
            apply();
        });

        /* ----- Catégories ----- */
        catChecks.forEach(cb => cb.addEventListener('change', () => {
            if (cb.value === 'all' && cb.checked) {
                catChecks.forEach(o => {
                    if (o !== cb) o.checked = false;
                });
            } else if (cb.value !== 'all' && cb.checked && catAll) {
                catAll.checked = false;
            }
            apply();
        }));

        stockToggle.addEventListener('change', apply);
        sortSelect.addEventListener('change', apply);

        /* ----- Filtrage / tri / pagination ----- */
        function apply() {
            page = 1;
            const selected = catChecks.filter(c => c.checked && c.value !== 'all').map(c => c.value);
            const allCat = (catAll && catAll.checked) || selected.length === 0;
            const lo = parseInt(priceMin.value) || MIN;
            const hi = parseInt(priceMax.value) || MAX;
            const stockOnly = stockToggle.checked;

            const filtered = items.filter(it => {
                const price = +it.dataset.price,
                    stock = +it.dataset.stock;
                return (allCat || selected.includes(it.dataset.catId)) &&
                    price >= lo && price <= hi &&
                    (!stockOnly || stock > 0);
            });

            const s = sortSelect.value;
            filtered.sort((a, b) => {
                if (s === 'price-asc') return (+a.dataset.price) - (+b.dataset.price);
                if (s === 'price-desc') return (+b.dataset.price) - (+a.dataset.price);
                if (s === 'name-asc') return a.querySelector('h3').textContent.localeCompare(b.querySelector('h3').textContent);
                return (+a.dataset.index) - (+b.dataset.index);
            });

            items.forEach(it => it.classList.add('hidden'));
            filtered.slice(0, page * PAGE_SIZE).forEach(it => {
                it.classList.remove('hidden');
                grid.appendChild(it);
            });

            countLabel.textContent = filtered.length.toLocaleString('fr-FR') + ' items';

            let active = 0;
            if (!allCat) active++;
            if (lo > MIN || hi < MAX) active++;
            if (stockOnly) active++;
            if (active > 0) {
                badge.textContent = active;
                badge.classList.remove('hidden');
                badge.classList.add('inline-flex');
            } else badge.classList.add('hidden');

            const remaining = filtered.length - page * PAGE_SIZE;
            moreWrap.classList.toggle('hidden', remaining <= 0);
            if (remaining > 0) btnMore.textContent = 'Show ' + remaining.toLocaleString('fr-FR') + ' More';

            emptyState.classList.toggle('hidden', filtered.length > 0);
        }

        btnMore.addEventListener('click', () => {
            page++;
            apply();
        });

        function clearFilters() {
            catChecks.forEach(c => c.checked = (c.value === 'all'));
            priceMin.value = MIN;
            priceMax.value = MAX;
            rangeMin.value = MIN;
            rangeMax.value = MAX;
            updateFill();
            stockToggle.checked = false;
            sortSelect.value = 'popular';
            apply();
        }

        document.getElementById('btn-clear').addEventListener('click', clearFilters);
        document.getElementById('btn-apply').addEventListener('click', apply);

        /* ----- Vue grille / liste ----- */
        const iconGrid = document.getElementById('btn-grid').querySelector('svg');
        const iconList = document.getElementById('btn-list').querySelector('svg');
        document.getElementById('btn-grid').addEventListener('click', () => {
            grid.className = 'grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5';
            iconGrid.classList.replace('text-gray-300', 'text-orange-500');
            iconList.classList.replace('text-orange-500', 'text-gray-300');
        });
        document.getElementById('btn-list').addEventListener('click', () => {
            grid.className = 'grid grid-cols-1 gap-3';
            iconList.classList.replace('text-gray-300', 'text-orange-500');
            iconGrid.classList.replace('text-orange-500', 'text-gray-300');
        });

        updateFill();
        apply();
    </script>

</body>

</html>