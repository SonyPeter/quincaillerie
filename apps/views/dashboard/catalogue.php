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

<div class="space-y-6">

    <!-- Top Header & Breadcrumb -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-navy-900/60 p-6 rounded-2xl border border-white/10">
        <div>
            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-xs text-white/40 font-medium mb-1">
                <a href="<?= BASE_URL ?>/dashboard" class="hover:text-white">Tableau de bord</a>
                <span>/</span>
                <span class="text-white font-semibold">Catalogue</span>
            </div>

            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-white">Catalogue des Produits</h1>
                <span id="active-filter-badge" class="w-6 h-6 rounded-full bg-brand-orange text-white text-xs font-bold flex items-center justify-center shadow-sm">
                    <?= $totalProducts ?>
                </span>
            </div>
            <p class="text-white/60 text-xs mt-1">Consultez et filtrez le catalogue complet de la quincaillerie par catégorie, prix et disponibilité.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/produits"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold transition border border-white/10">
                <?= DashboardPage::icon('package', 'w-4 h-4') ?>
                Gestion du stock
            </a>
        </div>
    </div>

    <!-- Header Controls: Search, View Switcher, Sort -->
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 bg-navy-900/40 p-4 rounded-2xl border border-white/10">
        <!-- Search input -->
        <div class="relative w-full md:w-80">
            <input type="text"
                   id="search-input"
                   placeholder="Rechercher par nom ou description..."
                   class="w-full pl-9 pr-4 py-2 rounded-xl bg-navy-950 border border-white/15 text-white placeholder-white/40 text-xs font-medium focus:outline-none focus:border-brand-orange transition">
            <svg class="w-4 h-4 text-white/40 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>

        <div class="flex items-center gap-4 w-full md:w-auto justify-between md:justify-end">
            <!-- View Layout Switcher -->
            <div class="flex items-center bg-navy-950 border border-white/10 rounded-xl p-1">
                <button type="button" id="btn-grid-view" class="p-1.5 rounded-lg bg-brand-orange text-white transition" title="Vue Grille">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button type="button" id="btn-list-view" class="p-1.5 rounded-lg text-white/40 hover:text-white transition" title="Vue Liste">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            <!-- Sort Dropdown -->
            <div class="relative">
                <select id="sort-select" class="appearance-none bg-navy-950 border border-white/15 text-xs font-semibold text-white/90 py-2.5 pl-4 pr-9 rounded-xl focus:outline-none focus:border-brand-orange cursor-pointer">
                    <option value="popular">Plus populaires</option>
                    <option value="price-asc">Prix : Croissant</option>
                    <option value="price-desc">Prix : Décroissant</option>
                    <option value="name-asc">Nom : A à Z</option>
                </select>
                <svg class="w-3.5 h-3.5 text-white/40 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Layout 2 Colonnes -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 items-start">

        <!-- SIDEBAR FILTRES (Gauche) -->
        <aside class="bg-navy-900/60 rounded-2xl border border-white/10 p-6 space-y-6">

            <!-- Section Catégorie -->
            <div class="space-y-3">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleFilterGroup('group-category')">
                    <h3 class="text-sm font-bold text-white">Catégorie</h3>
                    <svg id="arrow-group-category" class="w-4 h-4 text-white/40 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="group-category" class="space-y-2 pt-1">
                    <label class="flex items-center gap-3 text-xs font-semibold text-white/90 cursor-pointer select-none hover:text-brand-orange transition">
                        <input type="checkbox" value="all" checked class="cat-checkbox w-4 h-4 rounded text-brand-orange bg-navy-950 border-white/20 focus:ring-brand-orange">
                        <span>Tous</span>
                    </label>

                    <?php foreach ($categories as $cat): ?>
                        <label class="flex items-center gap-3 text-xs font-medium text-white/70 cursor-pointer select-none hover:text-brand-orange transition">
                            <input type="checkbox" value="<?= htmlspecialchars($cat['id']) ?>" class="cat-checkbox w-4 h-4 rounded text-brand-orange bg-navy-950 border-white/20 focus:ring-brand-orange">
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <hr class="border-white/10">

            <!-- Section Tranche de Prix -->
            <div class="space-y-4">
                <div class="flex items-center justify-between cursor-pointer" onclick="toggleFilterGroup('group-price')">
                    <h3 class="text-sm font-bold text-white">Tranche de prix</h3>
                    <svg id="arrow-group-price" class="w-4 h-4 text-white/40 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>

                <div id="group-price" class="space-y-4 pt-1">
                    <div class="flex items-center justify-between gap-2">
                        <div class="w-1/2">
                            <span class="text-[10px] text-white/40 font-semibold uppercase block mb-1">Min (HTG)</span>
                            <input type="number" id="price-min-input" value="<?= $minPrice ?>" min="<?= $minPrice ?>" max="<?= $maxPrice ?>"
                                   class="w-full px-3 py-1.5 bg-navy-950 border border-white/15 rounded-xl text-xs font-bold text-white focus:outline-none focus:border-brand-orange">
                        </div>
                        <span class="text-white/30 font-bold self-end pb-1.5">—</span>
                        <div class="w-1/2">
                            <span class="text-[10px] text-white/40 font-semibold uppercase block mb-1">Max (HTG)</span>
                            <input type="number" id="price-max-input" value="<?= $maxPrice ?>" min="<?= $minPrice ?>" max="<?= $maxPrice ?>"
                                   class="w-full px-3 py-1.5 bg-navy-950 border border-white/15 rounded-xl text-xs font-bold text-white focus:outline-none focus:border-brand-orange">
                        </div>
                    </div>

                    <!-- Range Slider -->
                    <div class="space-y-2">
                        <input type="range" id="price-range-slider" min="<?= $minPrice ?>" max="<?= $maxPrice ?>" value="<?= $maxPrice ?>"
                               class="w-full h-1.5 bg-white/15 rounded-lg appearance-none cursor-pointer accent-brand-orange">
                    </div>
                </div>
            </div>

            <hr class="border-white/10">

            <!-- Section Stock uniquement -->
            <div class="flex items-center justify-between pt-1">
                <span class="text-xs font-bold text-white">Uniquement en stock</span>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" id="stock-only-toggle" class="sr-only peer">
                    <div class="w-9 h-5 bg-white/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-white/20 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-brand-orange"></div>
                </label>
            </div>

            <hr class="border-white/10">

            <!-- Boutons d'action Filtre -->
            <div class="flex items-center gap-2 pt-2">
                <button type="button" id="btn-apply-filter"
                        class="flex-1 bg-brand-orange hover:bg-orange-600 text-white font-bold text-xs py-3 px-4 rounded-xl transition text-center shadow-sm">
                    <span id="filtered-count-label"><?= $totalProducts ?> produits</span>
                </button>
                <button type="button" id="btn-reset-filter"
                        class="bg-white/10 hover:bg-white/20 text-white/70 font-semibold text-xs py-3 px-4 rounded-xl transition text-center">
                    Effacer
                </button>
            </div>

        </aside>

        <!-- GRILLE DES PRODUITS (Droite) -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Grille -->
            <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($products as $p): ?>
                    <?php
                        $isLow = $p['quantity'] <= $p['low_stock_threshold'] && $p['quantity'] > 0;
                        $isOut = $p['quantity'] == 0;
                        $imageUrl = !empty($p['image'])
                            ? BASE_URL . '/public/uploads/produits/' . $p['image']
                            : null;
                    ?>
                    <div class="product-item group bg-navy-900/70 border border-white/10 rounded-2xl p-4 transition-all duration-300 hover:border-brand-orange/50 hover:bg-navy-900 flex flex-col justify-between relative"
                         data-id="<?= htmlspecialchars($p['id']) ?>"
                         data-name="<?= htmlspecialchars(mb_strtolower($p['name'])) ?>"
                         data-cat-id="<?= htmlspecialchars($p['category_id']) ?>"
                         data-price="<?= (float)$p['unit_price'] ?>"
                         data-stock="<?= (int)$p['quantity'] ?>"
                         data-desc="<?= htmlspecialchars(mb_strtolower($p['description'] ?? '')) ?>">

                        <div>
                            <!-- Image Container -->
                            <div class="relative w-full h-52 bg-navy-950 rounded-xl overflow-hidden flex items-center justify-center p-4 mb-4 border border-white/5">
                                <!-- Badge Top Left -->
                                <div class="absolute top-3 left-3 z-10">
                                    <?php if ($isOut): ?>
                                        <span class="px-2.5 py-1 rounded-lg bg-red-500/90 text-white text-[10px] font-bold uppercase tracking-wider backdrop-blur-md">
                                            Rupture
                                        </span>
                                    <?php elseif ($isLow): ?>
                                        <span class="px-2.5 py-1 rounded-lg bg-amber-500/90 text-white text-[10px] font-bold uppercase tracking-wider backdrop-blur-md">
                                            Stock faible (<?= $p['quantity'] ?>)
                                        </span>
                                    <?php else: ?>
                                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/90 text-white text-[10px] font-bold uppercase tracking-wider backdrop-blur-md">
                                            En Stock (<?= $p['quantity'] ?>)
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Favorite Heart Icon -->
                                <button type="button" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white/60 hover:text-red-400 backdrop-blur-md flex items-center justify-center transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </button>

                                <!-- Product Image -->
                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>" class="w-full h-full flex items-center justify-center">
                                    <?php if ($imageUrl): ?>
                                        <img src="<?= $imageUrl ?>"
                                             alt="<?= htmlspecialchars($p['name']) ?>"
                                             class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-500">
                                    <?php else: ?>
                                        <div class="flex flex-col items-center gap-1.5 text-white/30">
                                            <svg class="w-12 h-12 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                            </svg>
                                            <span class="text-[11px] font-medium">Image</span>
                                        </div>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <!-- Product Info -->
                            <div class="space-y-1 mb-4">
                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>" class="font-bold text-white text-sm hover:text-brand-orange transition line-clamp-1 block">
                                    <?= htmlspecialchars($p['name']) ?>
                                </a>
                                <p class="text-xs text-white/40 font-medium">
                                    <?= htmlspecialchars($p['category_name']) ?>
                                </p>
                            </div>
                        </div>

                        <!-- Footer Price & Buy Button (Bouton Brand Orange) -->
                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-white/5">
                            <div>
                                <span class="text-sm font-extrabold text-brand-orange">
                                    <?= number_format($p['unit_price'], 2, '.', ' ') ?> HTG
                                </span>
                            </div>

                            <button type="button"
                                    onclick='openProductModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'
                                    class="bg-brand-orange hover:bg-orange-600 text-white font-bold text-xs px-4 py-2 rounded-xl transition shadow-sm flex items-center gap-1.5">
                                <span>Détails</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Empty State -->
            <div id="empty-state" class="hidden p-12 text-center bg-navy-900/60 rounded-2xl border border-white/10 text-white/40 space-y-3">
                <svg class="w-12 h-12 mx-auto text-white/20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-bold text-white/70">Aucun produit ne correspond à vos critères.</p>
                <button type="button" onclick="resetFilters()" class="text-xs text-brand-orange font-bold hover:underline">Réinitialiser les filtres</button>
            </div>

            <!-- Load More / Show More Button -->
            <div class="text-center pt-6">
                <button type="button" id="btn-show-more"
                        class="px-8 py-3 bg-navy-900/80 hover:bg-navy-900 border border-white/15 text-white/90 font-bold text-xs rounded-xl transition shadow-sm">
                    Afficher plus de produits
                </button>
            </div>

        </div>
    </div>
</div>

<!-- Modal Détails Produit -->
<div id="product-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-black/75 backdrop-blur-sm">
    <div class="bg-navy-900 border border-white/15 w-full max-w-lg rounded-2xl overflow-hidden shadow-2xl space-y-0">
        <div class="px-6 py-4 border-b border-white/10 flex items-center justify-between bg-navy-950">
            <h3 id="modal-title" class="text-base font-bold text-white">Détails du Produit</h3>
            <button type="button" onclick="closeProductModal()" class="p-1.5 rounded-lg text-white/40 hover:text-white hover:bg-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto">
            <div id="modal-img-container" class="h-60 bg-navy-950 rounded-xl overflow-hidden flex items-center justify-center p-4 border border-white/10"></div>

            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span id="modal-category" class="px-2.5 py-1 rounded-lg bg-brand-orange/20 text-brand-orange text-xs font-bold border border-brand-orange/30"></span>
                    <span id="modal-stock-badge" class="px-2.5 py-1 rounded-lg text-xs font-bold"></span>
                </div>

                <h4 id="modal-name" class="text-xl font-bold text-white"></h4>
                <p id="modal-desc" class="text-xs text-white/60 leading-relaxed"></p>

                <div class="p-4 rounded-xl bg-navy-950 border border-white/10 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-white/40 font-semibold uppercase block">Prix Unitaire</span>
                        <span id="modal-price" class="text-2xl font-extrabold text-brand-orange"></span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-white/40 font-semibold uppercase block">Stock en magasin</span>
                        <span id="modal-qty" class="text-lg font-bold text-white"></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 border-t border-white/10 bg-navy-950 flex justify-end">
            <button type="button" onclick="closeProductModal()" class="px-6 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition">
                Fermer
            </button>
        </div>
    </div>
</div>

<!-- JavaScript Filtres & Tri -->
<script>
    const searchInput = document.getElementById('search-input');
    const catCheckboxes = document.querySelectorAll('.cat-checkbox');
    const priceMinInput = document.getElementById('price-min-input');
    const priceMaxInput = document.getElementById('price-max-input');
    const priceSlider = document.getElementById('price-range-slider');
    const stockToggle = document.getElementById('stock-only-toggle');
    const sortSelect = document.getElementById('sort-select');
    const productGrid = document.getElementById('product-grid');
    const productItems = Array.from(document.querySelectorAll('.product-item'));
    const filterBadge = document.getElementById('active-filter-badge');
    const countLabel = document.getElementById('filtered-count-label');
    const emptyState = document.getElementById('empty-state');
    const btnReset = document.getElementById('btn-reset-filter');

    function toggleFilterGroup(groupId) {
        const el = document.getElementById(groupId);
        const arrow = document.getElementById('arrow-' + groupId);
        if (el) {
            el.classList.toggle('hidden');
            if (arrow) arrow.classList.toggle('rotate-180');
        }
    }

    catCheckboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            if (cb.value === 'all') {
                if (cb.checked) {
                    catCheckboxes.forEach(other => { if (other.value !== 'all') other.checked = false; });
                }
            } else {
                const allCb = document.querySelector('.cat-checkbox[value="all"]');
                if (allCb) allCb.checked = false;
            }
            applyAllFilters();
        });
    });

    priceSlider?.addEventListener('input', (e) => {
        priceMaxInput.value = e.target.value;
        applyAllFilters();
    });

    priceMaxInput?.addEventListener('input', (e) => {
        priceSlider.value = e.target.value;
        applyAllFilters();
    });

    priceMinInput?.addEventListener('input', applyAllFilters);
    searchInput?.addEventListener('input', applyAllFilters);
    stockToggle?.addEventListener('change', applyAllFilters);
    sortSelect?.addEventListener('change', applyAllFilters);

    function resetFilters() {
        searchInput.value = '';
        catCheckboxes.forEach(cb => cb.checked = (cb.value === 'all'));
        priceMinInput.value = <?= $minPrice ?>;
        priceMaxInput.value = <?= $maxPrice ?>;
        if (priceSlider) priceSlider.value = <?= $maxPrice ?>;
        stockToggle.checked = false;
        sortSelect.value = 'popular';
        applyAllFilters();
    }

    btnReset?.addEventListener('click', resetFilters);

    function applyAllFilters() {
        const query = searchInput.value.toLowerCase().trim();

        const selectedCats = Array.from(catCheckboxes)
            .filter(cb => cb.checked && cb.value !== 'all')
            .map(cb => cb.value);
        const isAllCatSelected = document.querySelector('.cat-checkbox[value="all"]')?.checked || selectedCats.length === 0;

        const minPrice = parseFloat(priceMinInput.value) || 0;
        const maxPrice = parseFloat(priceMaxInput.value) || 9999999;
        const onlyStock = stockToggle.checked;

        let visibleCount = 0;

        productItems.forEach(item => {
            const name = item.getAttribute('data-name');
            const desc = item.getAttribute('data-desc');
            const catId = item.getAttribute('data-cat-id');
            const price = parseFloat(item.getAttribute('data-price')) || 0;
            const stock = parseInt(item.getAttribute('data-stock')) || 0;

            const matchQuery = !query || name.includes(query) || desc.includes(query);
            const matchCat = isAllCatSelected || selectedCats.includes(catId);
            const matchPrice = price >= minPrice && price <= maxPrice;
            const matchStock = !onlyStock || stock > 0;

            if (matchQuery && matchCat && matchPrice && matchStock) {
                item.style.display = 'flex';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });

        // Sorting
        const sortVal = sortSelect.value;
        const visibleItems = productItems.filter(item => item.style.display !== 'none');

        visibleItems.sort((a, b) => {
            const priceA = parseFloat(a.getAttribute('data-price')) || 0;
            const priceB = parseFloat(b.getAttribute('data-price')) || 0;
            const nameA = a.getAttribute('data-name');
            const nameB = b.getAttribute('data-name');

            if (sortVal === 'price-asc') return priceA - priceB;
            if (sortVal === 'price-desc') return priceB - priceA;
            if (sortVal === 'name-asc') return nameA.localeCompare(nameB);
            return 0;
        });

        visibleItems.forEach(item => productGrid.appendChild(item));

        filterBadge.textContent = visibleCount;
        countLabel.textContent = visibleCount + ' produit' + (visibleCount > 1 ? 's' : '');

        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }

    // Modal
    const modal = document.getElementById('product-modal');

    function openProductModal(p) {
        document.getElementById('modal-name').textContent = p.name;
        document.getElementById('modal-category').textContent = p.category_name;
        document.getElementById('modal-desc').textContent = p.description || 'Aucune description spécifique renseignée pour ce produit.';
        document.getElementById('modal-price').textContent = Number(p.unit_price).toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + ' HTG';
        document.getElementById('modal-qty').textContent = p.quantity + ' unité' + (p.quantity > 1 ? 's' : '');

        const stockBadge = document.getElementById('modal-stock-badge');
        if (p.quantity == 0) {
            stockBadge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-red-500/90 text-white';
            stockBadge.textContent = 'Rupture de stock';
        } else if (p.quantity <= p.low_stock_threshold) {
            stockBadge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-500/90 text-white';
            stockBadge.textContent = 'Stock faible';
        } else {
            stockBadge.className = 'px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-500/90 text-white';
            stockBadge.textContent = 'En stock';
        }

        const imgContainer = document.getElementById('modal-img-container');
        if (p.image) {
            imgContainer.innerHTML = `<img src="<?= BASE_URL ?>/public/uploads/produits/${p.image}" class="max-h-full max-w-full object-contain">`;
        } else {
            imgContainer.innerHTML = `<div class="text-white/30 text-center"><svg class="w-16 h-16 mx-auto mb-2 stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg><span class="text-xs text-white/40">Pas d'image disponible</span></div>`;
        }

        modal.classList.remove('hidden');
    }

    function closeProductModal() {
        modal.classList.add('hidden');
    }

    const btnGrid = document.getElementById('btn-grid-view');
    const btnList = document.getElementById('btn-list-view');

    btnList?.addEventListener('click', () => {
        productGrid.className = 'grid grid-cols-1 gap-4';
        btnList.className = 'p-1.5 rounded-lg bg-brand-orange text-white transition';
        btnGrid.className = 'p-1.5 rounded-lg text-white/40 hover:text-white transition';
    });

    btnGrid?.addEventListener('click', () => {
        productGrid.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6';
        btnGrid.className = 'p-1.5 rounded-lg bg-brand-orange text-white transition';
        btnList.className = 'p-1.5 rounded-lg text-white/40 hover:text-white transition';
    });

    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const productId = urlParams.get('id');
        if (productId) {
            const item = document.querySelector(`.product-item[data-id="${productId}"]`);
            if (item) {
                item.scrollIntoView({ behavior: 'smooth', block: 'center' });
                item.classList.add('ring-2', 'ring-brand-orange');
            }
        }
    });
</script>
