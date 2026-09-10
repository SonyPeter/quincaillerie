<?php

/** @var string $userName */
/** @var float $totalVentes */
/** @var float $totalVentesAujourdhui */
/** @var int $ventesEnAttente */
/** @var array $salesLast7Days */

$maxJour = max(array_column($salesLast7Days, 'total')) ?: 1;
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-medium tracking-tight">Tableau de bord — Vendeur</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
        <p class="text-sm text-white/50">Mes ventes totales</p>
        <p class="text-3xl font-semibold text-white mt-2"><?= number_format($totalVentes, 2) ?></p>
    </div>
    <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
        <p class="text-sm text-white/50">Ventes aujourd'hui</p>
        <p class="text-3xl font-semibold text-white mt-2"><?= number_format($totalVentesAujourdhui, 2) ?></p>
    </div>
    <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
        <p class="text-sm text-white/50">Fiches en attente de paiement</p>
        <p class="text-3xl font-semibold text-white mt-2"><?= $ventesEnAttente ?></p>
    </div>
</div>

<div class="bg-navy-900 border border-white/10 rounded-lg p-6 mb-6">
    <h2 class="font-semibold mb-1">Mes ventes des 7 derniers jours</h2>
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
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>

<!-- <p class="text-white/50 mt-6">Bienvenue, <?= htmlspecialchars($userName) ?>. Le module Messages sera ajouté à une prochaine étape.</p> -->