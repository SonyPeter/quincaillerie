<?php
/** @var array $transactions */
/** @var array $annees */
/** @var string $periode */
/** @var string $mois */
/** @var int $annee */

$typeLabels = [
    'vente' => ['Vente', 'bg-green-500/20 text-green-300'],
    'achat' => ['Achat', 'bg-orange-500/20 text-orange-300'],
    'stock' => ['Mouvement de stock', 'bg-blue-500/20 text-blue-300'],
];

$sousTypeLabels = [
    'reapprovisionnement' => 'Réapprovisionnement',
    'produit_defectueux' => 'Produit défectueux',
    'ajustement' => 'Ajustement manuel',
];
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">
        <?= Auth::isAdmin() ? 'Historique des transactions' : 'Mes transactions' ?>
    </h1>
</div>

<form method="GET" action="<?= BASE_URL ?>/transactions"
    class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-4 mb-6 flex flex-wrap items-end gap-4">
    <div>
        <label class="block text-xs font-medium text-white/70 mb-1">Période</label>
        <select name="periode" id="periode-select" onchange="this.form.submit()"
            class="bg-white/10 border border-white/20 text-white rounded px-3 py-2 text-sm">
            <option value="7j" class="text-gray-800" <?= $periode === '7j' ? 'selected' : '' ?>>7 derniers jours</option>
            <option value="mois_courant" class="text-gray-800" <?= $periode === 'mois_courant' ? 'selected' : '' ?>>Mois en cours</option>
            <option value="mois" class="text-gray-800" <?= $periode === 'mois' ? 'selected' : '' ?>>Mois précis</option>
            <option value="annee" class="text-gray-800" <?= $periode === 'annee' ? 'selected' : '' ?>>Année</option>
        </select>
    </div>

    <div id="champ-mois" class="<?= $periode === 'mois' ? '' : 'hidden' ?>">
        <label class="block text-xs font-medium text-white/70 mb-1">Mois</label>
        <input type="month" name="mois" value="<?= htmlspecialchars($mois) ?>" onchange="this.form.submit()"
            class="bg-white/10 border border-white/20 text-white rounded px-3 py-2 text-sm">
    </div>

    <div id="champ-annee" class="<?= $periode === 'annee' ? '' : 'hidden' ?>">
        <label class="block text-xs font-medium text-white/70 mb-1">Année</label>
        <select name="annee" onchange="this.form.submit()"
            class="bg-white/10 border border-white/20 text-white rounded px-3 py-2 text-sm">
            <?php foreach ($annees as $a): ?>
                <option value="<?= $a ?>" class="text-gray-800" <?= $annee === $a ? 'selected' : '' ?>><?= $a ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <noscript><button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded text-sm">Filtrer</button></noscript>
</form>

<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/50 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Type</th>
                <th class="px-4 py-3 text-left">Détail</th>
                <?php if (Auth::isAdmin()): ?>
                    <th class="px-4 py-3 text-left">Effectué par</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-right">Montant / Quantité</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            <?php if (empty($transactions)): ?>
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-white/40">Aucune transaction sur cette période.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($transactions as $t): ?>
                <?php [$label, $badgeClass] = $typeLabels[$t['type']] ?? [$t['type'], 'bg-white/15 text-white/70']; ?>
                <tr>
                    <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($t['dt']))) ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>">
                            <?= htmlspecialchars($label) ?>
                            <?= $t['type'] === 'stock' && !empty($t['sous_type']) ? ' — ' . htmlspecialchars($sousTypeLabels[$t['sous_type']] ?? $t['sous_type']) : '' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-white"><?= htmlspecialchars($t['detail']) ?></td>
                    <?php if (Auth::isAdmin()): ?>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($t['utilisateur']) ?></td>
                    <?php endif; ?>
                    <td class="px-4 py-3 text-right font-semibold <?= $t['type'] === 'stock' ? ((float) $t['montant'] >= 0 ? 'text-green-400' : 'text-red-400') : 'text-white' ?>">
                        <?php if ($t['type'] === 'stock'): ?>
                            <?= (float) $t['montant'] >= 0 ? '+' : '' ?><?= (int) $t['montant'] ?>
                        <?php else: ?>
                            <?= number_format((float) $t['montant'], 2) ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    const periodeSelect = document.getElementById('periode-select');
    const champMois = document.getElementById('champ-mois');
    const champAnnee = document.getElementById('champ-annee');

    periodeSelect.addEventListener('change', () => {
        champMois.classList.toggle('hidden', periodeSelect.value !== 'mois');
        champAnnee.classList.toggle('hidden', periodeSelect.value !== 'annee');
    });
</script>
