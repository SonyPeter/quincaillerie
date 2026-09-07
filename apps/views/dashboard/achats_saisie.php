<?php /** @var array $produits */ /** @var array $categories */ /** @var string $csrfToken */ ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-bold text-white">Achats</h1>
    <a href="<?= BASE_URL ?>/achats?historique=1"
        class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-semibold px-4 py-2 rounded transition text-sm sm:text-base">
        Historique des achats
    </a>
</div>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="bg-red-500/15 border border-red-400/30 text-red-200 text-sm px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="bg-green-500/15 border border-green-400/30 text-green-200 text-sm px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if (empty($categories)): ?>
    <div class="bg-yellow-500/15 border border-yellow-400/30 text-yellow-200 text-sm px-4 py-3 rounded">
        Créez d'abord une <a href="<?= BASE_URL ?>/categories" class="underline font-medium">catégorie</a> avant d'enregistrer un achat.
    </div>
<?php else: ?>
    <form method="POST" action="<?= BASE_URL ?>/achats" enctype="multipart/form-data"
        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Fournisseur (optionnel)</label>
                <input type="text" name="supplier"
                    class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Note (optionnelle)</label>
                <input type="text" name="note"
                    class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
        </div>

        <h2 class="font-semibold text-white mb-3">Produits achetés</h2>

        <div id="lignes" class="space-y-4 mb-4"></div>

        <button type="button" id="ajouter-ligne"
            class="text-orange-400 hover:underline text-sm font-medium mb-6">
            + Ajouter un produit
        </button>

        <div class="flex items-center justify-between border-t border-white/15 pt-4">
            <div>
                <p class="text-sm text-white/60">Total de l'achat</p>
                <p id="total" class="text-2xl font-bold text-white">0.00</p>
            </div>
            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded transition">
                Enregistrer l'achat
            </button>
        </div>
    </form>
<?php endif; ?>

<template id="ligne-template">
    <div class="ligne bg-white/5 border border-white/15 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <div class="flex gap-4 text-sm text-white/80">
                <label class="flex items-center gap-1.5">
                    <input type="radio" name="mode[]" value="existant" class="mode-radio" checked>
                    Produit existant
                </label>
                <label class="flex items-center gap-1.5">
                    <input type="radio" name="mode[]" value="nouveau" class="mode-radio">
                    Nouveau produit
                </label>
            </div>
            <button type="button" class="supprimer-ligne text-red-400 hover:text-red-300 text-sm" aria-label="Retirer">✕ Retirer</button>
        </div>

        <div class="mode-existant grid sm:grid-cols-3 gap-3">
            <div class="sm:col-span-3">
                <label class="block text-xs font-medium text-white/70 mb-1">Produit</label>
                <select name="product_id[]" class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                    <option value="" class="text-gray-800">— Choisir —</option>
                    <?php foreach ($produits as $p): ?>
                        <option value="<?= htmlspecialchars($p['id']) ?>" class="text-gray-800"><?= htmlspecialchars($p['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="mode-nouveau hidden grid sm:grid-cols-2 gap-3 mt-3">
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Catégorie</label>
                <select name="category_id[]" class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['id']) ?>" class="text-gray-800"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Nom du produit</label>
                <input type="text" name="name[]" class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-white/70 mb-1">Description</label>
                <input type="text" name="description[]" class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-medium text-white/70 mb-1">Photo (optionnelle)</label>
                <input type="file" name="image[]" accept="image/jpeg,image/png,image/webp"
                    class="w-full bg-white/10 border border-white/20 text-white/80 rounded px-3 py-2">
            </div>
            <p class="sm:col-span-2 text-xs text-white/40">
                Ce produit sera créé sans prix de vente. Un administrateur devra l'ajouter au catalogue
                depuis la page Produits pour lui fixer un prix.
            </p>
        </div>

        <div class="grid sm:grid-cols-2 gap-3 mt-3">
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Quantité achetée</label>
                <input type="number" name="quantity[]" min="1" required
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2 qty-input">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Prix d'achat (unité)</label>
                <input type="number" name="unit_cost[]" min="0" step="0.01" required
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2 cost-input">
            </div>
        </div>
    </div>
</template>

<script>
    const lignesContainer = document.getElementById('lignes');
    const template = document.getElementById('ligne-template');
    const totalEl = document.getElementById('total');

    function recalculerTotal() {
        let total = 0;
        document.querySelectorAll('.ligne').forEach(ligne => {
            const qty = parseFloat(ligne.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(ligne.querySelector('.cost-input').value) || 0;
            total += qty * cost;
        });
        totalEl.textContent = total.toFixed(2);
    }

    function ajouterLigne() {
        const clone = template.content.cloneNode(true);
        const ligne = clone.querySelector('.ligne');
        const existantDiv = ligne.querySelector('.mode-existant');
        const nouveauDiv = ligne.querySelector('.mode-nouveau');

        ligne.querySelectorAll('input[type=number]').forEach(input => input.addEventListener('input', recalculerTotal));

        ligne.querySelectorAll('.mode-radio').forEach(radio => {
            radio.addEventListener('change', () => {
                const estNouveau = ligne.querySelector('.mode-radio[value="nouveau"]').checked;
                existantDiv.classList.toggle('hidden', estNouveau);
                nouveauDiv.classList.toggle('hidden', !estNouveau);

                existantDiv.querySelector('select').required = !estNouveau;
                nouveauDiv.querySelector('select').required = estNouveau;
                nouveauDiv.querySelector('input[name="name[]"]').required = estNouveau;
            });
        });

        ligne.querySelector('.supprimer-ligne').addEventListener('click', () => {
            ligne.remove();
            recalculerTotal();
        });

        lignesContainer.appendChild(clone);
    }

    document.getElementById('ajouter-ligne')?.addEventListener('click', ajouterLigne);

    if (lignesContainer) {
        ajouterLigne();
    }
</script>
