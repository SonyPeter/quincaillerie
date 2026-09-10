<?php

/** @var array $categories */
/** @var array $products */
/** @var int $totalProducts */
/** @var float $minPrice */
/** @var float $maxPrice */

$minPrice = $minPrice ?? 0;
$maxPrice = $maxPrice ?? 10000;
$totalProducts = $totalProducts ?? 0;
$categories = $categories ?? [];
$products = $products ?? [];
$productsByCategory = $productsByCategory ?? [];
?>

<style>
    /* ⚠️ Ajustez cette valeur si votre brand-orange utilise une autre nuance */
    :root {
        --accent: #f97316;
    }

    .product-item {
        display: flex;
    }

    .is-hidden {
        display: none !important;
    }

    .line-clamp-1 {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* ===== Checkboxes carrées (style image) ===== */
    .cat-checkbox {
        appearance: none;
        -webkit-appearance: none;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        border: 1.5px solid #d1d5db;
        border-radius: 4px;
        background: #fff;
        cursor: pointer;
        position: relative;
        transition: all .15s ease;
    }

    .cat-checkbox:hover {
        border-color: var(--accent);
    }

    .cat-checkbox:checked {
        background: var(--accent);
        border-color: var(--accent);
    }

    .cat-checkbox:checked::after {
        content: '';
        position: absolute;
        left: 4px;
        top: 1.5px;
        width: 5px;
        height: 9px;
        border-right: 2px solid #fff;
        border-bottom: 2px solid #fff;
        transform: rotate(45deg);
    }

    .cat-checkbox:focus-visible {
        box-shadow: 0 0 0 3px rgba(249, 115, 22, .25);
        outline: none;
    }

    /* ===== Double slider de prix ===== */
    .range-wrap {
        position: relative;
        height: 20px;
    }

    .range-track {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 999px;
        background: #e5e7eb;
    }

    .range-fill {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        height: 4px;
        border-radius: 999px;
        background: var(--accent);
    }

    .range-wrap input[type="range"] {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 20px;
        margin: 0;
        background: transparent;
        -webkit-appearance: none;
        appearance: none;
        pointer-events: none;
    }

    .range-wrap input[type="range"]::-webkit-slider-runnable-track {
        height: 20px;
        background: transparent;
    }

    .range-wrap input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        pointer-events: auto;
        height: 16px;
        width: 16px;
        border-radius: 50%;
        margin-top: 2px;
        background: #fff;
        border: 2px solid var(--accent);
        box-shadow: 0 1px 3px rgba(15, 23, 42, .25);
        cursor: grab;
    }

    .range-wrap input[type="range"]::-moz-range-track {
        height: 20px;
        background: transparent;
    }

    .range-wrap input[type="range"]::-moz-range-thumb {
        pointer-events: auto;
        height: 14px;
        width: 14px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid var(--accent);
        box-shadow: 0 1px 3px rgba(15, 23, 42, .25);
        cursor: grab;
    }

    #range-min {
        z-index: 2;
    }

    #range-max {
        z-index: 3;
    }

    /* ===== Cœur favori ===== */
    .fav-btn.is-fav {
        color: #ef4444;
    }

    .fav-btn.is-fav svg {
        fill: #ef4444;
        stroke: #ef4444;
    }
</style>

<div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm shadow-gray-400/10 p-5 sm:p-7 space-y-6 text-gray-800">

    <!-- ============================================================
         EN-TÊTE : fil d'ariane + titre + contrôles
    ============================================================ -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-gray-400 font-medium mb-1">
                <a href="<?= BASE_URL ?>/dashboard" class="hover:text-gray-700 transition">Tableau de bord</a>
                <span>/</span>
                <span class="text-gray-700 font-semibold">Catalogue</span>
            </div>

            <div class="flex items-center gap-2.5">
                <h1 class="text-2xl font-bold text-gray-900">Catalogue</h1>
                <span id="active-filter-badge"
                    class="min-w-[1.5rem] h-6 px-1.5 rounded-full bg-brand-orange text-white text-xs font-bold inline-flex items-center justify-center shadow-sm">
                    <?= $totalProducts ?>
                </span>
            </div>
            <p class="text-gray-400 text-sm mt-1">Consultez et filtrez le catalogue complet par catégorie, prix et disponibilité.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <!-- Vue : grille / liste -->
            <div class="flex items-center bg-gray-50 border border-gray-200 rounded-xl p-1">
                <button type="button" id="btn-grid-view" title="Vue Grille"
                    class="p-1.5 rounded-lg bg-brand-orange text-white transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button type="button" id="btn-list-view" title="Vue Liste"
                    class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Tri -->
            <div class="relative flex items-center">
                <select id="sort-select"
                    class="appearance-none bg-transparent text-sm font-semibold text-gray-700 hover:text-gray-900 py-2 pl-1 pr-8 focus:outline-none cursor-pointer">
                    <option value="popular">Plus populaires</option>
                    <option value="price-asc">Prix : Croissant</option>
                    <option value="price-desc">Prix : Décroissant</option>
                    <option value="name-asc">Nom : A à Z</option>
                </select>
                <svg class="w-4 h-4 text-gray-400 absolute right-1 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            <!-- Gestion du stock -->
            <a href="<?= BASE_URL ?>/produits"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gray-50 hover:bg-gray-100 border border-gray-200 text-gray-700 text-xs font-semibold transition">
                <?= DashboardPage::icon('package', 'w-4 h-4') ?>
                Gestion du stock
            </a>
        </div>
    </div>

    <!-- Recherche -->
    <div class="relative w-full sm:max-w-xs">
        <input type="text" id="search-input" placeholder="Rechercher un produit..."
            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/60 text-sm font-medium text-gray-800 placeholder-gray-400 focus:outline-none focus:border-brand-orange focus:ring-2 focus:ring-brand-orange/10 transition">
        <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
    </div>

    <!-- ============================================================
         LAYOUT 2 COLONNES : filtres + produits
    ============================================================ -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">

        <!-- ==================== SIDEBAR FILTRES ==================== -->
        <aside class="space-y-1 lg:sticky lg:top-4">

            <!-- Catégorie -->
            <div class="pb-4">
                <button type="button" class="w-full flex items-center justify-between mb-3"
                    onclick="toggleFilterGroup('group-category')">
                    <h3 class="text-sm font-bold text-gray-900">Catégorie</h3>
                    <svg id="arrow-group-category" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="group-category" class="space-y-2.5">
                    <label class="flex items-center gap-3 text-[13px] cursor-pointer select-none">
                        <input type="checkbox" value="all" checked class="cat-checkbox">
                        <span class="font-semibold text-gray-900">Tous</span>
                    </label>

                    <?php foreach ($categories as $cat): ?>
                        <label class="flex items-center gap-3 text-[13px] font-medium text-gray-600 hover:text-gray-900 cursor-pointer select-none transition">
                            <input type="checkbox" value="<?= htmlspecialchars($cat['id']) ?>" class="cat-checkbox">
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </label>
                    <?php endforeach; ?>

                    <!-- Équivalent "Special Offers" de l'image -->
                    <label class="flex items-center gap-3 text-[13px] font-medium text-gray-600 hover:text-gray-900 cursor-pointer select-none transition">
                        <input type="checkbox" value="__promo" class="cat-checkbox">
                        <span>Offres spéciales</span>
                    </label>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Tranche de prix -->
            <div class="py-4">
                <button type="button" class="w-full flex items-center justify-between mb-4"
                    onclick="toggleFilterGroup('group-price')">
                    <h3 class="text-sm font-bold text-gray-900">Tranche de prix</h3>
                    <svg id="arrow-group-price" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div id="group-price" class="space-y-4">
                    <div class="flex items-center gap-2">
                        <input type="number" id="price-min-input" value="<?= (int)$minPrice ?>"
                            min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>" aria-label="Prix minimum"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-800 focus:outline-none focus:border-brand-orange transition">
                        <span class="text-gray-300 font-bold">—</span>
                        <input type="number" id="price-max-input" value="<?= (int)$maxPrice ?>"
                            min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>" aria-label="Prix maximum"
                            class="w-full px-3 py-2 rounded-xl border border-gray-200 bg-white text-sm font-semibold text-gray-800 focus:outline-none focus:border-brand-orange transition">
                    </div>

                    <!-- ✅ Double slider (2 poignées comme l'image) -->
                    <div class="range-wrap px-0.5">
                        <div class="range-track"></div>
                        <div class="range-fill" id="range-fill"></div>
                        <input type="range" id="range-min" min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>" value="<?= (int)$minPrice ?>" step="1">
                        <input type="range" id="range-max" min="<?= (int)$minPrice ?>" max="<?= (int)$maxPrice ?>" value="<?= (int)$maxPrice ?>" step="1">
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            <!-- Uniquement en stock -->
            <div class="flex items-center justify-between py-4">
                <span class="text-sm font-bold text-gray-900">Uniquement en stock</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="stock-only-toggle" class="sr-only peer">
                    <div class="w-9 h-5 bg-gray-200 rounded-full peer peer-checked:bg-brand-orange after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:shadow-sm after:transition-all peer-checked:after:translate-x-full transition-colors"></div>
                </label>
            </div>

            <hr class="border-gray-100">

            <!-- Boutons -->
            <div class="flex items-center gap-2 pt-3">
                <button type="button" id="btn-apply-filter"
                    class="flex-1 bg-brand-orange hover:bg-orange-600 text-white font-bold text-xs py-3 px-4 rounded-xl transition shadow-sm shadow-orange-500/25">
                    <span id="filtered-count-label"><?= $totalProducts ?> produits</span>
                </button>
                <button type="button" id="btn-reset-filter"
                    class="px-4 py-3 rounded-xl text-gray-500 hover:text-gray-800 hover:bg-gray-100 font-semibold text-xs transition">
                    Effacer
                </button>
            </div>
        </aside>

        <!-- ==================== GRILLE DES PRODUITS ==================== -->
        <div class="lg:col-span-3 space-y-6">

            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $p):
                    $isLow   = $p['quantity'] <= $p['low_stock_threshold'] && $p['quantity'] > 0;
                    $isOut   = $p['quantity'] == 0;
                    $isPromo = !empty($p['old_price']) && $p['old_price'] > $p['unit_price'];
                    $imageUrl = !empty($p['image']) ? BASE_URL . '/public/uploads/produits/' . $p['image'] : null;
                ?>
                    <div class="product-item group bg-white border border-gray-100 rounded-2xl p-4 transition-all duration-300 hover:shadow-xl hover:shadow-gray-200/70 hover:-translate-y-1 flex-col justify-between relative"
                        data-id="<?= htmlspecialchars($p['id']) ?>"
                        data-name="<?= htmlspecialchars(mb_strtolower($p['name'])) ?>"
                        data-cat-id="<?= htmlspecialchars($p['category_id']) ?>"
                        data-price="<?= (float)$p['unit_price'] ?>"
                        data-stock="<?= (int)$p['quantity'] ?>"
                        data-promo="<?= $isPromo ? '1' : '0' ?>"
                        data-desc="<?= htmlspecialchars(mb_strtolower($p['description'] ?? '')) ?>">

                        <div>
                            <!-- Zone image -->
                            <div class="relative w-full h-44 mb-4 flex items-center justify-center">
                                <!-- Badges haut gauche (promo / stock) -->
                                <div class="absolute top-0 left-0 z-10 flex flex-col items-start gap-1.5">
                                    <?php if ($isPromo): ?>
                                        <span class="px-2.5 py-1 rounded-lg bg-red-500 text-white text-[10px] font-bold backdrop-blur-md">
                                            -<?= round((1 - $p['unit_price'] / $p['old_price']) * 100) ?>%
                                        </span>
                                    <?php endif; ?>
                                    <?php if ($isOut): ?>
                                        <span class="px-2.5 py-1 rounded-lg bg-red-500/95 text-white text-[10px] font-bold backdrop-blur-md">Rupture</span>
                                    <?php elseif ($isLow): ?>
                                        <span class="px-2.5 py-1 rounded-lg bg-amber-400/95 text-amber-950 text-[10px] font-bold backdrop-blur-md">
                                            Stock faible (<?= $p['quantity'] ?>)
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Cœur favori -->
                                <button type="button" class="fav-btn absolute top-0 right-0 z-10 p-1 text-gray-300 hover:text-red-400 transition" aria-label="Ajouter aux favoris">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>

                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>" class="w-full h-full flex items-center justify-center">
                                    <?php if ($imageUrl): ?>
                                        <img src="<?= $imageUrl ?>" alt="<?= htmlspecialchars($p['name']) ?>"
                                            class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-500">
                                    <?php else: ?>
                                        <div class="w-full h-full flex flex-col items-center justify-center gap-1.5 text-gray-300 bg-gray-50 rounded-xl">
                                            <svg class="w-12 h-12 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                            <span class="text-[11px] font-medium">Image</span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <!-- Infos -->
                            <div class="space-y-0.5 mb-4 px-1">
                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                    class="font-semibold text-gray-900 text-sm hover:text-brand-orange transition line-clamp-1 block">
                                    <?= htmlspecialchars($p['name']) ?>
                                </a>
                                <p class="text-xs text-gray-400 font-medium">
                                    <?= htmlspecialchars($p['category_name']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Prix + actions -->
                        <div class="flex items-end justify-between gap-3 pt-3 border-t border-gray-50">
                            <div class="flex items-baseline gap-2 flex-wrap leading-tight">
                                <span class="text-base font-extrabold text-gray-900">
                                    <?= number_format($p['unit_price'], 2, ',', ' ') ?> HTG
                                </span>
                                <?php if ($isPromo): ?>
                                    <span class="text-xs text-gray-400 line-through font-semibold">
                                        <?= number_format($p['old_price'], 2, ',', ' ') ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button type="button"
                                    onclick='openProductModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                    class="bg-brand-orange hover:bg-orange-600 text-white font-bold text-xs px-4 py-2 rounded-lg transition shadow-sm shadow-orange-500/25">
                                    Détails
                                </button>
                                <button type="button"
                                    onclick='openProductModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                    class="w-9 h-9 rounded-lg border border-gray-200 text-gray-400 hover:text-brand-orange hover:border-brand-orange flex items-center justify-center transition" aria-label="Voir le produit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden p-12 text-center bg-gray-50 rounded-2xl border border-gray-100 text-gray-400 space-y-3">
                <svg class="w-12 h-12 mx-auto text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-bold text-gray-600">Aucun produit ne correspond à vos critères.</p>
                <button type="button" onclick="resetFilters()" class="text-xs text-brand-orange font-bold hover:underline">Réinitialiser les filtres</button>
            </div>

            <!-- Afficher plus (pagination fonctionnelle) -->
            <div class="text-center pt-2" id="show-more-wrap">
                <button type="button" id="btn-show-more"
                    class="px-8 py-3 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 hover:border-gray-300 text-gray-600 text-sm font-semibold transition shadow-sm">
                    <span id="show-more-label">Afficher plus de produits</span>
                </button>
            </div>

        </div>
    </div>
</div>

<!-- ==================== MODAL DÉTAILS PRODUIT (thème clair) ==================== -->
<div id="product-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
    <div class="bg-white border border-gray-100 w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 id="modal-title" class="text-base font-bold text-gray-900">Détails du Produit</h3>
            <button type="button" onclick="closeProductModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <div id="modal-img-container" class="h-60 bg-gray-50 rounded-xl overflow-hidden flex items-center justify-center p-4 border border-gray-100"></div>

            <div class="space-y-3">
                <div class="flex items-center justify-between gap-3 flex-wrap">
                    <span id="modal-category" class="px-2.5 py-1 rounded-lg bg-brand-orange/10 text-brand-orange text-xs font-bold border border-brand-orange/20"></span>
                    <span id="modal-stock-badge" class="px-2.5 py-1 rounded-lg text-xs font-bold"></span>
                </div>

                <h4 id="modal-name" class="text-xl font-bold text-gray-900"></h4>
                <p id="modal-desc" class="text-sm text-gray-500 leading-relaxed"></p>

                <div class="p-4 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-gray-400 font-semibold uppercase block">Prix Unitaire</span>
                        <span id="modal-price" class="text-2xl font-extrabold text-brand-orange"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-gray-400 font-semibold uppercase block">Stock en magasin</span>
                        <span id="modal-qty" class="text-lg font-bold text-gray-900"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button type="button" onclick="closeProductModal()"
                class="px-6 py-2.5 rounded-xl bg-white border border-gray-200 hover:bg-gray-100 text-gray-700 font-bold text-xs transition">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- ==================== JAVASCRIPT ==================== -->
<script>
    const PRICE_MIN = <?= (float)$minPrice ?>;
    const PRICE_MAX = <?= (float)$maxPrice ?>;
    const PAGE_SIZE = 9;

    const searchInput = document.getElementById('search-input');
    const catCheckboxes = document.querySelectorAll('.cat-checkbox');
    const priceMinInput = document.getElementById('price-min-input');
    const priceMaxInput = document.getElementById('price-max-input');
    const rangeMin = document.getElementById('range-min');
    const rangeMax = document.getElementById('range-max');
    const rangeFill = document.getElementById('range-fill');
    const stockToggle = document.getElementById('stock-only-toggle');
    const sortSelect = document.getElementById('sort-select');
    const productGrid = document.getElementById('product-grid');
    const productItems = Array.from(document.querySelectorAll('.product-item'));
    const filterBadge = document.getElementById('active-filter-badge');
    const countLabel = document.getElementById('filtered-count-label');
    const emptyState = document.getElementById('empty-state');
    const showMoreWrap = document.getElementById('show-more-wrap');
    const showMoreLabel = document.getElementById('show-more-label');

    let pageLimit = PAGE_SIZE;

    /* ----- Sections repliables (chevron vers la droite quand replié) ----- */
    function toggleFilterGroup(groupId) {
        const el = document.getElementById(groupId);
        const arrow = document.getElementById('arrow-' + groupId);
        if (el) el.classList.toggle('hidden');
        if (arrow) arrow.classList.toggle('-rotate-90');
    }

    /* ----- Double slider ----- */
    function paintRange() {
        const min = +rangeMin.min,
            max = +rangeMin.max;
        let lo = +rangeMin.value,
            hi = +rangeMax.value;
        if (lo > hi) {
            const t = lo;
            lo = hi;
            hi = t;
        }
        const span = (max - min) || 1;
        rangeFill.style.left = ((lo - min) / span * 100) + '%';
        rangeFill.style.width = ((hi - lo) / span * 100) + '%';
    }

    function rangesToInputs() {
        let lo = +rangeMin.value,
            hi = +rangeMax.value;
        if (lo > hi) {
            [lo, hi] = [hi, lo];
            rangeMin.value = lo;
            rangeMax.value = hi;
        }
        priceMinInput.value = lo;
        priceMaxInput.value = hi;
        paintRange();
    }

    function inputsToRanges() {
        let lo = parseFloat(priceMinInput.value),
            hi = parseFloat(priceMaxInput.value);
        if (isNaN(lo)) lo = PRICE_MIN;
        if (isNaN(hi)) hi = PRICE_MAX;
        lo = Math.min(Math.max(lo, PRICE_MIN), PRICE_MAX);
        hi = Math.min(Math.max(hi, PRICE_MIN), PRICE_MAX);
        if (lo > hi) hi = lo;
        rangeMin.value = lo;
        rangeMax.value = hi;
        paintRange();
    }

    /* ----- Logique centrale ----- */
    function onFiltersChanged() {
        pageLimit = PAGE_SIZE;
        render();
    }

    function render() {
        const query = (searchInput.value || '').toLowerCase().trim();
        const selectedCats = Array.from(catCheckboxes)
            .filter(cb => cb.checked && cb.value !== 'all' && cb.value !== '__promo')
            .map(cb => cb.value);
        const promoOnly = document.querySelector('.cat-checkbox[value="__promo"]')?.checked || false;
        const allChecked = document.querySelector('.cat-checkbox[value="all"]')?.checked || selectedCats.length === 0;

        let lo = parseFloat(priceMinInput.value);
        if (isNaN(lo)) lo = PRICE_MIN;
        let hi = parseFloat(priceMaxInput.value);
        if (isNaN(hi)) hi = PRICE_MAX;
        const onlyStock = stockToggle.checked;
        const sortVal = sortSelect.value;

        const matched = productItems.filter(item => {
            const d = item.dataset;
            const price = parseFloat(d.price) || 0;
            const stock = parseInt(d.stock, 10) || 0;
            return (!query || d.name.includes(query) || d.desc.includes(query)) &&
                (allChecked || selectedCats.includes(d.catId)) &&
                (!promoOnly || d.promo === '1') &&
                price >= lo && price <= hi &&
                (!onlyStock || stock > 0);
        });

        matched.sort((a, b) => {
            if (sortVal === 'price-asc') return (parseFloat(a.dataset.price) || 0) - (parseFloat(b.dataset.price) || 0);
            if (sortVal === 'price-desc') return (parseFloat(b.dataset.price) || 0) - (parseFloat(a.dataset.price) || 0);
            if (sortVal === 'name-asc') return a.dataset.name.localeCompare(b.dataset.name);
            return 0;
        });

        const shown = matched.slice(0, pageLimit);
        productItems.forEach(it => it.classList.add('is-hidden'));
        shown.forEach(it => it.classList.remove('is-hidden'));

        const matchedSet = new Set(matched);
        matched.forEach(it => productGrid.appendChild(it));
        productItems.forEach(it => {
            if (!matchedSet.has(it)) productGrid.appendChild(it);
        });

        filterBadge.textContent = matched.length;
        countLabel.textContent = matched.length + ' produit' + (matched.length > 1 ? 's' : '');

        if (matched.length === 0) emptyState.classList.remove('hidden');
        else emptyState.classList.add('hidden');

        const remaining = matched.length - shown.length;
        if (remaining > 0) {
            showMoreWrap.classList.remove('hidden');
            showMoreLabel.textContent = 'Afficher ' + remaining + ' produit' + (remaining > 1 ? 's' : '') + ' de plus';
        } else {
            showMoreWrap.classList.add('hidden');
        }
    }

    /* ----- Événements ----- */
    catCheckboxes.forEach(cb => cb.addEventListener('change', () => {
        if (cb.value === 'all') {
            if (cb.checked) {
                catCheckboxes.forEach(o => {
                    if (o.value !== 'all') o.checked = false;
                });
            } else {
                const anyOther = Array.from(catCheckboxes).some(o => o.value !== 'all' && o.checked);
                if (!anyOther) cb.checked = true;
            }
        } else {
            const allCb = document.querySelector('.cat-checkbox[value="all"]');
            if (allCb) allCb.checked = false;
        }
        onFiltersChanged();
    }));

    rangeMin.addEventListener('input', () => {
        rangesToInputs();
        onFiltersChanged();
    });
    rangeMax.addEventListener('input', () => {
        rangesToInputs();
        onFiltersChanged();
    });
    priceMinInput.addEventListener('input', onFiltersChanged);
    priceMaxInput.addEventListener('input', onFiltersChanged);
    priceMinInput.addEventListener('change', inputsToRanges);
    priceMaxInput.addEventListener('change', inputsToRanges);

    searchInput.addEventListener('input', onFiltersChanged);
    stockToggle.addEventListener('change', onFiltersChanged);
    sortSelect.addEventListener('change', onFiltersChanged);

    function resetFilters() {
        searchInput.value = '';
        catCheckboxes.forEach(cb => cb.checked = (cb.value === 'all'));
        priceMinInput.value = PRICE_MIN;
        priceMaxInput.value = PRICE_MAX;
        rangeMin.value = PRICE_MIN;
        rangeMax.value = PRICE_MAX;
        paintRange();
        stockToggle.checked = false;
        sortSelect.value = 'popular';
        pageLimit = PAGE_SIZE;
        render();
    }
    document.getElementById('btn-reset-filter').addEventListener('click', resetFilters);

    document.getElementById('btn-apply-filter').addEventListener('click', () => {
        productGrid.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    });

    document.getElementById('btn-show-more').addEventListener('click', () => {
        pageLimit += PAGE_SIZE;
        render();
    });

    /* ----- Vue grille / liste ----- */
    const btnGrid = document.getElementById('btn-grid-view');
    const btnList = document.getElementById('btn-list-view');
    const ACTIVE = 'p-1.5 rounded-lg bg-brand-orange text-white transition shadow-sm';
    const INACTIVE = 'p-1.5 rounded-lg text-gray-400 hover:text-gray-700 transition';

    btnList.addEventListener('click', () => {
        productGrid.className = 'grid grid-cols-1 gap-4';
        btnList.className = ACTIVE;
        btnGrid.className = INACTIVE;
    });
    btnGrid.addEventListener('click', () => {
        productGrid.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6';
        btnGrid.className = ACTIVE;
        btnList.className = INACTIVE;
    });

    /* ----- Favoris (visuel) ----- */
    document.querySelectorAll('.fav-btn').forEach(b => b.addEventListener('click', () => b.classList.toggle('is-fav')));

    /* ----- Modal ----- */
    const modal = document.getElementById('product-modal');

    function openProductModal(p) {
        document.getElementById('modal-name').textContent = p.name;
        document.getElementById('modal-category').textContent = p.category_name;
        document.getElementById('modal-desc').textContent = p.description || 'Aucune description spécifique renseignée pour ce produit.';
        document.getElementById('modal-price').textContent = Number(p.unit_price).toLocaleString('fr-FR', {
            minimumFractionDigits: 2
        }) + ' HTG';
        document.getElementById('modal-qty').textContent = p.quantity + ' unité' + (p.quantity > 1 ? 's' : '');

        const stockBadge = document.getElementById('modal-stock-badge');
        if (p.quantity == 0) {
            stockBadge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-red-500 text-white';
            stockBadge.textContent = 'Rupture de stock';
        } else if (p.quantity <= p.low_stock_threshold) {
            stockBadge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-400 text-amber-950';
            stockBadge.textContent = 'Stock faible';
        } else {
            stockBadge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500 text-white';
            stockBadge.textContent = 'En stock';
        }

        const imgContainer = document.getElementById('modal-img-container');
        if (p.image) {
            imgContainer.innerHTML = `<img src="<?= BASE_URL ?>/public/uploads/produits/${p.image}" class="max-h-full max-w-full object-contain">`;
        } else {
            imgContainer.innerHTML = `<div class="text-gray-300 text-center"><svg class="w-16 h-16 mx-auto mb-2 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><span class="text-xs text-gray-400">Pas d'image disponible</span></div>`;
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeProductModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    modal.addEventListener('click', e => {
        if (e.target === modal) closeProductModal();
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeProductModal();
    });

    /* ----- Mise en évidence via ?id= ----- */
    window.addEventListener('DOMContentLoaded', () => {
        const productId = new URLSearchParams(window.location.search).get('id');
        if (productId) {
            const item = document.querySelector(`.product-item[data-id="${productId}"]`);
            if (item) {
                item.classList.add('ring-2', 'ring-brand-orange');
                item.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }
    });

    /* ----- Init ----- */
    inputsToRanges();
    render();
</script>