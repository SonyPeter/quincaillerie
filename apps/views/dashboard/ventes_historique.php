<?php /** @var array $ventes */

$methodLabels = ['cash' => 'Cash', 'moncash' => 'MonCash', 'natcash' => 'NatCash'];
?>

<div class="flex items-center justify-between mb-6">
    <div>
        <a href="<?= BASE_URL ?>/ventes" class="text-sm text-white/50 hover:underline">&larr; Retour à la saisie</a>
        <h1 class="text-2xl font-bold text-white mt-1">
            <?= Auth::isAdmin() ? 'Historique des ventes' : 'Mes ventes' ?>
        </h1>
    </div>
</div>

<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/50 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">N°</th>
                <th class="px-4 py-3 text-left">Date</th>
                <th class="px-4 py-3 text-left">Client</th>
                <?php if (Auth::isAdmin()): ?>
                    <th class="px-4 py-3 text-left">Vendeur</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-left">Paiement</th>
                <th class="px-4 py-3 text-left">Statut</th>
                <th class="px-4 py-3 text-right">Total</th>
                <th class="px-4 py-3 text-right">Fiche</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            <?php if (empty($ventes)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-white/40">Aucune vente enregistrée pour l'instant.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($ventes as $v): ?>
                <?php $estPayee = $v['payment_status'] === 'paye'; ?>
                <tr>
                    <td class="px-4 py-3 text-white/60">#<?= (int) $v['sale_number'] ?></td>
                    <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($v['created_at']))) ?></td>
                    <td class="px-4 py-3 text-white"><?= htmlspecialchars($v['customer_name'] ?: '—') ?></td>
                    <?php if (Auth::isAdmin()): ?>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($v['seller_name']) ?></td>
                    <?php endif; ?>
                    <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($methodLabels[$v['payment_method']] ?? $v['payment_method']) ?></td>
                    <td class="px-4 py-3">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium <?= $estPayee ? 'bg-green-500/20 text-green-300' : 'bg-yellow-500/20 text-yellow-200' ?>">
                            <?= $estPayee ? 'Payée' : 'En attente' ?>
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-white"><?= number_format((float) $v['total_amount'], 2) ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="<?= BASE_URL ?>/ventes?fiche=<?= urlencode($v['id']) ?>" class="text-blue-400 hover:underline">Voir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
