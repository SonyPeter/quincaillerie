<?php

/** @var string $userName */
/** @var array $finance */
/** @var int $lowStockCount */
/** @var int $pendingWithdrawalsCount */
/** @var array $salesLast7Days */
/** @var array $salesByCategory */
/** @var array $lowStockProducts */
/** @var array $paidSales */

$methodLabels = ['cash' => 'Cash', 'moncash' => 'MonCash', 'natcash' => 'NatCash'];

$ventes = $finance['ventes'];
$achats = $finance['achats'];
$retraits = $finance['retraits'];
$total = $ventes + $achats + $retraits;

if ($total > 0) {
    $pVentes = $ventes / $total * 100;
    $pAchats = $achats / $total * 100;
    $pRetraits = 100 - $pVentes - $pAchats;
} else {
    $pVentes = $pAchats = $pRetraits = 0;
}

$stop1 = $pVentes;
$stop2 = $pVentes + $pAchats;

$solde = $ventes - $achats - $retraits;

$maxJour = max(array_column($salesLast7Days, 'total')) ?: 1;
$maxCategorie = $salesByCategory ? max(array_column($salesByCategory, 'total')) : 1;
$maxCategorie = $maxCategorie ?: 1;
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-medium tracking-tight">Tableau de bord — Administrateur</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <button type="button" onclick="ouvrirModal('modal-ventes')"
        class="text-left bg-navy-900 border border-white/10 hover:border-orange-500/50 rounded-lg p-6 transition">
        <p class="text-sm text-white/50">Total des ventes</p>
        <p class="text-3xl font-semibold text-white mt-2"><?= number_format($ventes, 2) ?></p>
    </button>
    <button type="button" onclick="ouvrirModal('modal-stock-faible')"
        class="text-left bg-navy-900 border border-white/10 hover:border-orange-500/50 rounded-lg p-6 transition">
        <p class="text-sm text-white/50">Produits en stock faible</p>
        <p class="text-3xl font-semibold text-red-400 mt-2"><?= $lowStockCount ?></p>
    </button>
    <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
        <p class="text-sm text-white/50">Retraits en attente</p>
        <p class="text-3xl font-semibold text-white mt-2"><?= $pendingWithdrawalsCount ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Ventes des 7 derniers jours -->
    <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
        <h2 class="font-semibold mb-1">Ventes des 7 derniers jours</h2>
        <p class="text-sm text-white/50 mb-6">Total encaissé par jour (ventes payées)</p>

        <div class="flex items-end justify-between gap-3 h-40">
            <?php foreach ($salesLast7Days as $jour): ?>
                <?php $hauteur = $jour['total'] > 0 ? max(6, round($jour['total'] / $maxJour * 100)) : 2; ?>
                <div class="flex-1 flex flex-col items-center justify-end h-full gap-2">
                    <p class="text-xs text-white/60"><?= $jour['total'] > 0 ? number_format($jour['total'], 0) : '' ?></p>
                    <div class="w-full rounded-t-md bg-gradient-to-t from-orange-600 to-orange-400"
                        style="height: <?= $hauteur ?>%;" title="<?= number_format($jour['total'], 2) ?>"></div>
                    <p class="text-xs text-white/40"><?= htmlspecialchars($jour['label']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Ventes par catégorie -->
    <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
        <h2 class="font-semibold mb-1">Ventes par catégorie</h2>
        <p class="text-sm text-white/50 mb-6">Chiffre d'affaires par catégorie de produit</p>

        <?php if (empty($salesByCategory)): ?>
            <p class="text-sm text-white/40">Aucune vente payée pour l'instant.</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($salesByCategory as $cat): ?>
                    <div>
                        <div class="flex items-center justify-between text-sm mb-1">
                            <span class="text-white/80"><?= htmlspecialchars($cat['category']) ?></span>
                            <span class="font-medium text-white"><?= number_format($cat['total'], 2) ?></span>
                        </div>
                        <div class="w-full h-2 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full rounded-full bg-gradient-to-r from-orange-600 to-orange-400"
                                style="width: <?= max(4, round($cat['total'] / $maxCategorie * 100)) ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="bg-navy-900 border border-white/10 rounded-lg p-6">
    <h2 class="font-semibold mb-1">Aperçu financier</h2>
    <p class="text-sm text-white/50 mb-6">Argent entré (ventes), dépensé (achats) et retiré de la caisse</p>

    <div class="flex flex-col sm:flex-row items-center gap-8">
        <div class="relative w-48 h-48 shrink-0 rounded-full"
            style="background: <?= $total > 0
                                    ? "conic-gradient(#22c55e 0% {$stop1}%, #f97316 {$stop1}% {$stop2}%, #3b82f6 {$stop2}% 100%)"
                                    : 'rgba(255,255,255,0.08)' ?>;">
            <div class="absolute inset-4 bg-navy-900 rounded-full flex flex-col items-center justify-center">
                <p class="text-xs text-white/50">Solde</p>
                <p class="text-xl font-semibold <?= $solde >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                    <?= number_format($solde, 2) ?>
                </p>
            </div>
        </div>

        <div class="space-y-3 w-full">
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-sm text-white/70">
                    <span class="w-3 h-3 rounded-full bg-green-500 inline-block"></span>
                    Argent entré (ventes)
                </span>
                <span class="font-semibold text-white"><?= number_format($ventes, 2) ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-sm text-white/70">
                    <span class="w-3 h-3 rounded-full bg-orange-500 inline-block"></span>
                    Argent dépensé (achats)
                </span>
                <span class="font-semibold text-white"><?= number_format($achats, 2) ?></span>
            </div>
            <div class="flex items-center justify-between">
                <span class="flex items-center gap-2 text-sm text-white/70">
                    <span class="w-3 h-3 rounded-full bg-blue-500 inline-block"></span>
                    Retraits de caisse
                </span>
                <span class="font-semibold text-white"><?= number_format($retraits, 2) ?></span>
            </div>
            <?php if ($total === 0.0): ?>
                <p class="text-xs text-white/40 pt-2">Aucune donnée pour l'instant — le graphique se remplira avec les ventes, achats et retraits.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- <p class="text-white/50 mt-6">Bienvenue, <?= htmlspecialchars($userName) ?>. Les modules Caisse et Messages seront ajoutés aux prochaines étapes.</p> -->

<!-- Modal : liste des ventes payées -->
<div id="modal-ventes" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60" onclick="fermerModal('modal-ventes')"></div>
    <div class="relative bg-navy-900 border border-white/20 rounded-lg shadow-lg max-w-3xl w-full max-h-[80vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-white text-lg">Ventes payées (<?= count($paidSales) ?>)</h3>
            <button type="button" onclick="fermerModal('modal-ventes')" class="text-white/50 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <?php if (empty($paidSales)): ?>
            <p class="text-white/40 text-sm">Aucune vente payée pour l'instant.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="text-white/50 uppercase text-xs">
                        <tr>
                            <th class="px-3 py-2 text-left">N°</th>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Client</th>
                            <th class="px-3 py-2 text-left">Vendeur</th>
                            <th class="px-3 py-2 text-left">Paiement</th>
                            <th class="px-3 py-2 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        <?php foreach ($paidSales as $v): ?>
                            <tr>
                                <td class="px-3 py-2 text-white/60">#<?= (int) $v['sale_number'] ?></td>
                                <td class="px-3 py-2 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($v['created_at']))) ?></td>
                                <td class="px-3 py-2 text-white"><?= htmlspecialchars($v['customer_name'] ?: '—') ?></td>
                                <td class="px-3 py-2 text-white/60"><?= htmlspecialchars($v['seller_name']) ?></td>
                                <td class="px-3 py-2 text-white/60"><?= htmlspecialchars($methodLabels[$v['payment_method']] ?? $v['payment_method']) ?></td>
                                <td class="px-3 py-2 text-right font-semibold text-white"><?= number_format((float) $v['total_amount'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal : produits en stock faible -->
<div id="modal-stock-faible" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60" onclick="fermerModal('modal-stock-faible')"></div>
    <div class="relative bg-navy-900 border border-white/20 rounded-lg shadow-lg max-w-lg w-full max-h-[80vh] overflow-y-auto p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-white text-lg">Produits en stock faible (<?= count($lowStockProducts) ?>)</h3>
            <button type="button" onclick="fermerModal('modal-stock-faible')" class="text-white/50 hover:text-white text-xl leading-none">&times;</button>
        </div>
        <?php if (empty($lowStockProducts)): ?>
            <p class="text-white/40 text-sm">Aucun produit en stock faible pour l'instant.</p>
        <?php else: ?>
            <div class="space-y-2">
                <?php foreach ($lowStockProducts as $p): ?>
                    <div class="flex items-center justify-between bg-white/5 border border-white/10 rounded-lg px-4 py-2.5">
                        <div>
                            <p class="text-white font-medium"><?= htmlspecialchars($p['name']) ?></p>
                            <p class="text-xs text-white/40"><?= htmlspecialchars($p['category_name']) ?></p>
                        </div>
                        <span class="text-red-400 font-semibold">
                            <?= (int) $p['quantity'] ?> restant<?= (int) $p['quantity'] > 1 ? 's' : '' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function ouvrirModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function fermerModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>