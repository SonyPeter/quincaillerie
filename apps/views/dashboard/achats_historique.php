<?php

/** @var array $achats */ ?>

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= BASE_URL ?>/achats" class="text-sm text-white/50 hover:underline">&larr; Retour à la saisie</a>
        <h1 class="text-2xl font-bold text-white mt-1">Historique des achats</h1>
    </div>
</div>

<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/50 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Fournisseur</th>
                <th class="px-4 py-3 text-left">Produits</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-left">Enregistré par</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            <?php if (empty($achats)): ?>
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-white/40">Aucun achat enregistré pour l'instant.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($achats as $achat): ?>
                <tr>
                    <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($achat['created_at']))) ?></td>
                    <td class="px-4 py-3 text-white"><?= htmlspecialchars($achat['supplier'] ?: '—') ?></td>
                    <td class="px-4 py-3 text-white/60">
                        <?php
                        $labels = array_map(
                            fn($item) => htmlspecialchars($item['product_name']) . ' (' . (int) $item['quantity'] . ')',
                            $achat['items']
                        );
                        echo implode(', ', $labels);
                        ?>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-white"><?= number_format((float) $achat['total_amount'], 2) ?></td>
                    <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($achat['recorded_by_name']) ?></td>
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