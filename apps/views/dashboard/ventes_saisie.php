<?php /** @var array $produits */ /** @var string $csrfToken */ ?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <h1 class="text-2xl font-bold text-white">Ventes</h1>
    <a href="<?= BASE_URL ?>/ventes?historique=1"
        class="bg-white/10 hover:bg-white/20 backdrop-blur-md border border-white/20 text-white font-semibold px-4 py-2 rounded transition text-sm sm:text-base">
        Historique des ventes
    </a>
</div>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <div class="bg-red-500/15 border border-red-400/30 text-red-200 text-sm px-4 py-2 rounded mb-4">
        <?= htmlspecialchars($_SESSION['flash_error']) ?>
    </div>
    <?php unset($_SESSION['flash_error']); ?>
<?php endif; ?>

<?php if (empty($produits)): ?>
    <div class="bg-yellow-500/15 border border-yellow-400/30 text-yellow-200 text-sm px-4 py-3 rounded">
        Aucun produit disponible à la vente pour le moment.
    </div>
<?php else: ?>
    <form method="POST" action="<?= BASE_URL ?>/ventes"
        class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="hidden" name="action" value="enregistrer">

        <div class="grid sm:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Client (optionnel)</label>
                <input type="text" name="customer_name"
                    class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-white/70 mb-2">Moyen de paiement</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-1.5 text-sm text-white/80">
                        <input type="radio" name="payment_method" value="cash" checked required> Cash
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-white/80">
                        <input type="radio" name="payment_method" value="moncash" required> MonCash
                    </label>
                    <label class="flex items-center gap-1.5 text-sm text-white/80">
                        <input type="radio" name="payment_method" value="natcash" required> NatCash
                    </label>
                </div>
            </div>
        </div>

        <h2 class="font-semibold text-white mb-3">Produits vendus</h2>

        <div id="lignes" class="space-y-3 mb-4"></div>

        <button type="button" id="ajouter-ligne"
            class="text-orange-400 hover:underline text-sm font-medium mb-6">
            + Ajouter un produit
        </button>

        <div class="flex items-center justify-between border-t border-white/15 pt-4">
            <div>
                <p class="text-sm text-white/60">Total de la vente</p>
                <p id="total" class="text-2xl font-bold text-white">0.00</p>
            </div>
            <button type="submit"
                class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded transition">
                Enregistrer et imprimer la fiche
            </button>
        </div>
        <p class="text-xs text-white/40 mt-3">
            Cette étape ne fait que préparer la fiche (proforma) : le stock ne sera réduit et la vente
            comptabilisée qu'après validation du paiement sur la page suivante.
        </p>
    </form>
<?php endif; ?>

<template id="ligne-template">
    <div class="ligne grid sm:grid-cols-4 gap-3 items-end bg-white/5 border border-white/15 rounded-lg p-3">
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-white/70 mb-1">Produit</label>
            <select name="product_id[]" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2 produit-select">
                <option value="" class="text-gray-800">— Choisir —</option>
                <?php foreach ($produits as $p): ?>
                    <option value="<?= htmlspecialchars($p['id']) ?>" data-price="<?= (float) $p['unit_price'] ?>" data-stock="<?= (int) $p['quantity'] ?>" class="text-gray-800">
                        <?= htmlspecialchars($p['name']) ?> (<?= number_format((float) $p['unit_price'], 2) ?> · stock : <?= (int) $p['quantity'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-white/70 mb-1">Quantité</label>
            <input type="number" name="quantity[]" min="1" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2 qty-input">
        </div>
        <div class="flex items-center gap-2">
            <p class="flex-1 text-sm text-white/70 subtotal">0.00</p>
            <button type="button" class="supprimer-ligne text-red-400 hover:text-red-300 pb-2" aria-label="Retirer">✕</button>
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
            const select = ligne.querySelector('.produit-select');
            const price = parseFloat(select.selectedOptions[0]?.dataset.price) || 0;
            const qty = parseFloat(ligne.querySelector('.qty-input').value) || 0;
            const subtotal = price * qty;
            ligne.querySelector('.subtotal').textContent = subtotal.toFixed(2);
            total += subtotal;
        });
        totalEl.textContent = total.toFixed(2);
    }

    function ajouterLigne() {
        const clone = template.content.cloneNode(true);
        const ligne = clone.querySelector('.ligne');
        ligne.querySelector('.produit-select').addEventListener('change', recalculerTotal);
        ligne.querySelector('.qty-input').addEventListener('input', recalculerTotal);
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
