<?php
/** @var array $sale */
/** @var array $items */
/** @var string $csrfToken */
/** @var bool $peutValider */

$methodLabels = ['cash' => 'Cash', 'moncash' => 'MonCash', 'natcash' => 'NatCash'];
$estPayee = $sale['payment_status'] === 'paye';
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de vente #<?= (int) $sale['sale_number'] ?> — Quincaillerie de la Liberté</title>
    <link href="<?= BASE_URL ?>/public/css/output.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen py-8 px-4">

    <div class="no-print max-w-2xl mx-auto mb-4 flex items-center justify-between">
        <a href="<?= BASE_URL ?>/ventes" class="text-sm text-gray-600 hover:underline">&larr; Retour aux ventes</a>
        <div class="flex gap-2">
            <button type="button" onclick="window.print()"
                class="bg-gray-800 hover:bg-gray-900 text-white font-semibold px-4 py-2 rounded transition">
                Imprimer
            </button>
            <?php if ($peutValider): ?>
                <form method="POST" action="<?= BASE_URL ?>/ventes" onsubmit="return confirm('Confirmer que le paiement a été reçu ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="valider">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($sale['id']) ?>">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded transition">
                        Valider la vente (paiement reçu)
                    </button>
                </form>
                <form method="POST" action="<?= BASE_URL ?>/ventes" onsubmit="return confirm('Annuler cette fiche ?');">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="action" value="annuler">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($sale['id']) ?>">
                    <button type="submit"
                        class="bg-red-100 hover:bg-red-200 text-red-700 font-semibold px-4 py-2 rounded transition">
                        Annuler
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="no-print max-w-2xl mx-auto bg-green-100 text-green-700 text-sm px-4 py-2 rounded mb-4">
            <?= htmlspecialchars($_SESSION['flash_success']) ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
    <?php endif; ?>

    <div class="max-w-2xl mx-auto bg-white shadow rounded-lg p-8">
        <div class="flex items-start justify-between mb-6 border-b pb-6">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Quincaillerie de la Liberté</h1>
                <p class="text-sm text-gray-500 mt-1">
                    <?= $estPayee ? 'Reçu de vente' : 'Fiche proforma (non payée)' ?>
                </p>
            </div>
            <div class="text-right">
                <p class="font-semibold text-gray-800">Vente #<?= (int) $sale['sale_number'] ?></p>
                <p class="text-sm text-gray-500"><?= htmlspecialchars(date('d/m/Y H:i', strtotime($sale['created_at']))) ?></p>
                <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium <?= $estPayee ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                    <?= $estPayee ? 'Payée' : 'En attente de paiement' ?>
                </span>
            </div>
        </div>

        <div class="grid sm:grid-cols-3 gap-4 mb-6 text-sm">
            <div>
                <p class="text-gray-500">Client</p>
                <p class="font-medium text-gray-800"><?= htmlspecialchars($sale['customer_name'] ?: '—') ?></p>
            </div>
            <div>
                <p class="text-gray-500">Vendeur</p>
                <p class="font-medium text-gray-800"><?= htmlspecialchars($sale['seller_name']) ?></p>
            </div>
            <div>
                <p class="text-gray-500">Moyen de paiement</p>
                <p class="font-medium text-gray-800"><?= htmlspecialchars($methodLabels[$sale['payment_method']] ?? $sale['payment_method']) ?></p>
            </div>
        </div>

        <table class="w-full text-sm mb-6">
            <thead class="border-b border-gray-200 text-gray-500 uppercase text-xs">
                <tr>
                    <th class="text-left py-2">Produit</th>
                    <th class="text-right py-2">Quantité</th>
                    <th class="text-right py-2">Prix unitaire</th>
                    <th class="text-right py-2">Sous-total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td class="py-2 text-gray-800"><?= htmlspecialchars($item['product_name']) ?></td>
                        <td class="py-2 text-right text-gray-600"><?= (int) $item['quantity'] ?></td>
                        <td class="py-2 text-right text-gray-600"><?= number_format((float) $item['unit_price'], 2) ?></td>
                        <td class="py-2 text-right text-gray-800"><?= number_format((float) $item['subtotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="flex justify-end border-t pt-4">
            <div class="text-right">
                <p class="text-sm text-gray-500">Total</p>
                <p class="text-2xl font-bold text-gray-900"><?= number_format((float) $sale['total_amount'], 2) ?></p>
            </div>
        </div>

        <?php if (!$estPayee): ?>
            <p class="text-xs text-gray-400 mt-6 border-t pt-4">
                Ce document est une proforma. Il ne constitue pas une preuve de paiement tant que la vente
                n'a pas été validée après réception de l'argent.
            </p>
        <?php endif; ?>
    </div>

</body>

</html>
