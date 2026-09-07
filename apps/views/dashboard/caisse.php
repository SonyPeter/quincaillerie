<?php
/** @var string $csrfToken */
/** @var array|null $soldes */
/** @var array|null $demandesEnAttente */
/** @var array|null $historique */
/** @var array|null $mesDemandes */

$typeLabels = [
    'produit_retourne' => 'Produit retourné',
    'achat' => 'Retrait pour achat',
    'salaire_employe' => 'Paiement employé',
    'depense_diverse' => 'Dépense diverse',
];

$statutLabels = [
    'en_attente' => ['En attente', 'bg-yellow-500/20 text-yellow-200'],
    'valide' => ['Validé', 'bg-green-500/20 text-green-300'],
    'refuse' => ['Refusé', 'bg-red-500/20 text-red-300'],
];
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Caisse &amp; retraits</h1>
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

<?php if (Auth::isAdmin()): ?>

    <!-- Soldes des caisses -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
        <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
            <p class="text-sm text-white/50">Caisse Cash</p>
            <p class="text-3xl font-semibold text-white mt-2"><?= number_format($soldes['cash'], 2) ?></p>
        </div>
        <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
            <p class="text-sm text-white/50">Caisse NatCash</p>
            <p class="text-3xl font-semibold text-white mt-2"><?= number_format($soldes['natcash'], 2) ?></p>
        </div>
        <div class="bg-navy-900 border border-white/10 rounded-lg p-6">
            <p class="text-sm text-white/50">Caisse MonCash</p>
            <p class="text-3xl font-semibold text-white mt-2"><?= number_format($soldes['moncash'], 2) ?></p>
        </div>
    </div>

    <!-- Conversion NatCash / MonCash vers Cash -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-white mb-1">Convertir en Cash</h2>
        <p class="text-sm text-white/60 mb-4">Transfère un montant NatCash ou MonCash vers la caisse Cash.</p>
        <form method="POST" action="<?= BASE_URL ?>/caisse" class="grid sm:grid-cols-3 gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="convertir">
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Depuis</label>
                <select name="from_method" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                    <option value="natcash" class="text-gray-800">NatCash (<?= number_format($soldes['natcash'], 2) ?> disponible)</option>
                    <option value="moncash" class="text-gray-800">MonCash (<?= number_format($soldes['moncash'], 2) ?> disponible)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Montant à convertir</label>
                <input type="number" name="amount" min="0.01" step="0.01" required
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition">
                    Convertir vers Cash
                </button>
            </div>
        </form>
    </div>

    <!-- Demandes de retrait en attente -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-white mb-4">Demandes de retrait en attente (<?= count($demandesEnAttente) ?>)</h2>
        <?php if (empty($demandesEnAttente)): ?>
            <p class="text-white/40 text-sm">Aucune demande en attente.</p>
        <?php else: ?>
            <div class="space-y-3">
                <?php foreach ($demandesEnAttente as $d): ?>
                    <div class="bg-white/5 border border-white/15 rounded-lg p-4 flex flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[200px]">
                            <p class="text-white font-medium"><?= htmlspecialchars($typeLabels[$d['type']] ?? $d['type']) ?></p>
                            <p class="text-sm text-white/60">
                                Demandé par <?= htmlspecialchars($d['requested_by_name'] ?? '—') ?>
                                le <?= htmlspecialchars(date('d/m/Y H:i', strtotime($d['created_at']))) ?>
                            </p>
                            <?php if (!empty($d['description'])): ?>
                                <p class="text-sm text-white/50 mt-1">« <?= htmlspecialchars($d['description']) ?> »</p>
                            <?php endif; ?>
                        </div>
                        <p class="text-xl font-bold text-white"><?= number_format((float) $d['amount'], 2) ?></p>
                        <div class="flex gap-2">
                            <form method="POST" action="<?= BASE_URL ?>/caisse" onsubmit="return confirm('Valider ce retrait ?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="valider_retrait">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($d['id']) ?>">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded transition text-sm">
                                    Valider
                                </button>
                            </form>
                            <form method="POST" action="<?= BASE_URL ?>/caisse" onsubmit="return confirm('Refuser ce retrait ?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="refuser_retrait">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($d['id']) ?>">
                                <button type="submit" class="bg-red-500/20 hover:bg-red-500/30 text-red-300 font-semibold px-4 py-2 rounded transition text-sm">
                                    Refuser
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Historique complet -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5 text-white/50 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Raison</th>
                    <th class="px-4 py-3 text-left">Demandé par</th>
                    <th class="px-4 py-3 text-left">Traité par</th>
                    <th class="px-4 py-3 text-left">Statut</th>
                    <th class="px-4 py-3 text-right">Montant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                <?php if (empty($historique)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-white/40">Aucun retrait pour l'instant.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($historique as $d): ?>
                    <?php [$label, $badgeClass] = $statutLabels[$d['status']] ?? [$d['status'], 'bg-white/15 text-white/70']; ?>
                    <tr>
                        <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($d['created_at']))) ?></td>
                        <td class="px-4 py-3 text-white"><?= htmlspecialchars($typeLabels[$d['type']] ?? $d['type']) ?></td>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($d['description'] ?? '') ?></td>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($d['requested_by_name'] ?? '—') ?></td>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($d['approved_by_name'] ?? '—') ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>"><?= htmlspecialchars($label) ?></span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-white"><?= number_format((float) $d['amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>

    <!-- Vendeur : demander un retrait -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-white mb-4">Demander un retrait</h2>
        <form method="POST" action="<?= BASE_URL ?>/caisse" class="grid sm:grid-cols-2 gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="demander_retrait">
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Type de retrait</label>
                <select name="type" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                    <?php foreach ($typeLabels as $value => $label): ?>
                        <option value="<?= $value ?>" class="text-gray-800"><?= htmlspecialchars($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-white/70 mb-1">Montant</label>
                <input type="number" name="amount" min="0.01" step="0.01" required
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-white/70 mb-1">Raison / description</label>
                <input type="text" name="description" required
                    class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded transition">
                    Envoyer la demande
                </button>
            </div>
        </form>
    </div>

    <!-- Vendeur : mes demandes -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5 text-white/50 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Raison</th>
                    <th class="px-4 py-3 text-left">Statut</th>
                    <th class="px-4 py-3 text-right">Montant</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                <?php if (empty($mesDemandes)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-white/40">Vous n'avez fait aucune demande de retrait.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($mesDemandes as $d): ?>
                    <?php [$label, $badgeClass] = $statutLabels[$d['status']] ?? [$d['status'], 'bg-white/15 text-white/70']; ?>
                    <tr>
                        <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($d['created_at']))) ?></td>
                        <td class="px-4 py-3 text-white"><?= htmlspecialchars($typeLabels[$d['type']] ?? $d['type']) ?></td>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($d['description'] ?? '') ?></td>
                        <td class="px-4 py-3">
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium <?= $badgeClass ?>"><?= htmlspecialchars($label) ?></span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold text-white"><?= number_format((float) $d['amount'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
