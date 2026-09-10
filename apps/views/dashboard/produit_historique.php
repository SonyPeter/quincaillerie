<?php

/** @var array $product */
/** @var array $movements */

$labels = [
    'reapprovisionnement' => ['Réapprovisionnement', 'bg-green-500/20 text-green-300'],
    'produit_defectueux' => ['Produit défectueux', 'bg-red-500/20 text-red-300'],
    'ajustement' => ['Ajustement manuel', 'bg-white/15 text-white/70'],
    'vente' => ['Vente', 'bg-blue-500/20 text-blue-300'],
];
?>

<div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div class="min-w-0">
        <a href="<?= BASE_URL ?>/produits" class="text-sm text-white/50 hover:underline">&larr; Retour aux produits</a>
        <h1 class="text-2xl font-bold text-white mt-1 truncate">Historique — <?= htmlspecialchars($product['name']) ?></h1>
    </div>
    <div class="text-right shrink-0">
        <p class="text-sm text-white/60">Quantité actuelle</p>
        <p class="text-2xl font-bold text-white"><?= (int) $product['quantity'] ?></p>
    </div>
</div>

<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/50 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Action</th>
                <th class="px-4 py-3 text-right">Variation</th>
                <th class="px-4 py-3 text-right">Stock résultant</th>
                <th class="px-4 py-3 text-left">Raison</th>
                <th class="px-4 py-3 text-left">Effectué par</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            <?php if (empty($movements)): ?>
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-white/40">Aucune action enregistrée pour ce produit.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($movements as $m): ?>
                <?php [$label, $badgeClass] = $labels[$m['type']] ?? [$m['type'], 'bg-white/15 text-white/70']; ?>
                <tr>
                    <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($m['created_at']))) ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>"><?= htmlspecialchars($label) ?></span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold <?= $m['quantity_change'] >= 0 ? 'text-green-400' : 'text-red-400' ?>">
                        <?= $m['quantity_change'] >= 0 ? '+' : '' ?><?= (int) $m['quantity_change'] ?>
                    </td>
                    <td class="px-4 py-3 text-right text-white"><?= (int) $m['quantity_after'] ?></td>
                    <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($m['reason'] ?? '') ?></td>
                    <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($m['user_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>