<?php

/** @var array $produits */
/** @var array $produitsEnAttente */
/** @var array $categories */
/** @var string $csrfToken */ ?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Produits</h1>
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

<?php if (Auth::isAdmin() && !empty($produitsEnAttente)): ?>
    <div class="bg-white/10 backdrop-blur-md border border-white/20 border-l-4 border-l-orange-500 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-white mb-1">Produits en attente de publication</h2>
        <p class="text-sm text-white/60 mb-4">
            Créés depuis un achat, sans prix de vente. Ajoutez-les au catalogue pour les rendre vendables.
        </p>
        <div class="space-y-3">
            <?php foreach ($produitsEnAttente as $p): ?>
                <div class="bg-white/5 border border-white/15 rounded-lg p-4 flex flex-wrap items-center gap-4">
                    <?php if (!empty($p['image'])): ?>
                        <img src="<?= BASE_URL ?>/public/uploads/produits/<?= htmlspecialchars($p['image']) ?>"
                            alt="<?= htmlspecialchars($p['name']) ?>" class="w-12 h-12 rounded object-cover border border-white/20">
                    <?php else: ?>
                        <div class="w-12 h-12 rounded bg-white/10 border border-white/20 flex items-center justify-center text-white/40"><?= DashboardPage::icon('package', 'w-5 h-5') ?></div>
                    <?php endif; ?>
                    <div class="flex-1 min-w-[160px]">
                        <p class="font-medium text-white"><?= htmlspecialchars($p['name']) ?></p>
                        <p class="text-sm text-white/60"><?= htmlspecialchars($p['category_name']) ?> · Quantité : <?= (int) $p['quantity'] ?></p>
                    </div>
                    <button type="button" onclick="document.getElementById('publier-<?= $p['id'] ?>').classList.toggle('hidden')"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition">
                        Ajouter au catalogue
                    </button>
                    <form id="publier-<?= $p['id'] ?>" method="POST" action="<?= BASE_URL ?>/produits" enctype="multipart/form-data"
                        class="hidden w-full grid sm:grid-cols-2 lg:grid-cols-4 gap-4 border-t border-white/15 pt-4 mt-2">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                        <input type="hidden" name="action" value="publier">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-white/70 mb-1">Description</label>
                            <input type="text" name="description" value="<?= htmlspecialchars($p['description'] ?? '') ?>"
                                class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/70 mb-1">Prix de vente</label>
                            <input type="number" name="unit_price" min="0" step="0.01" required
                                class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-white/70 mb-1">Seuil d'alerte</label>
                            <input type="number" name="low_stock_threshold" min="1" value="10"
                                class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                        </div>
                        <div class="lg:col-span-3">
                            <label class="block text-sm font-medium text-white/70 mb-1">Remplacer l'image (optionnel)</label>
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                                class="w-full bg-white/10 border border-white/20 text-white/80 rounded px-3 py-2">
                        </div>
                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded transition">
                                Confirmer
                            </button>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<?php if (Auth::isAdmin()): ?>
    <?php if (empty($categories)): ?>
        <div class="bg-yellow-500/15 border border-yellow-400/30 text-yellow-200 text-sm px-4 py-3 rounded mb-6">
            Créez d'abord une <a href="<?= BASE_URL ?>/categories" class="underline font-medium">catégorie</a> avant d'ajouter un produit.
        </div>
    <?php else: ?>
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
            <h2 class="font-semibold text-white mb-4">Ajouter un produit</h2>
            <form method="POST" action="<?= BASE_URL ?>/produits" enctype="multipart/form-data" class="grid sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="action" value="ajouter">
                <div class="lg:col-span-1">
                    <label class="block text-sm font-medium text-white/70 mb-1">Catégorie</label>
                    <select name="category_id" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['id']) ?>" class="text-gray-800"><?= htmlspecialchars($cat['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="lg:col-span-1">
                    <label class="block text-sm font-medium text-white/70 mb-1">Nom</label>
                    <input type="text" name="name" required class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-white/70 mb-1">Description</label>
                    <input type="text" name="description" class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-1">Quantité</label>
                    <input type="number" name="quantity" min="0" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-1">Prix unitaire</label>
                    <input type="number" name="unit_price" min="0" step="0.01" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/70 mb-1">Seuil d'alerte</label>
                    <input type="number" name="low_stock_threshold" min="1" value="10" class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                </div>
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-white/70 mb-1">Image (JPG, PNG ou WEBP, 2 Mo max)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                        class="w-full bg-white/10 border border-white/20 text-white/80 rounded px-3 py-2">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition">
                        Ajouter
                    </button>
                </div>
            </form>
        </div>
    <?php endif; ?>
<?php endif; ?>

<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/50 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Image</th>
                <th class="px-4 py-3 text-left">Produit</th>
                <th class="px-4 py-3 text-left">Catégorie</th>
                <th class="px-4 py-3 text-left">Description</th>
                <th class="px-4 py-3 text-right">Quantité</th>
                <?php if (Auth::isAdmin()): ?>
                    <th class="px-4 py-3 text-right">Prix d'achat</th>
                <?php endif; ?>
                <th class="px-4 py-3 text-right">Prix de vente</th>
                <?php if (Auth::isAdmin()): ?>
                    <th class="px-4 py-3 text-right">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            <?php if (empty($produits)): ?>
                <tr>
                    <td colspan="8" class="px-4 py-6 text-center text-white/40">Aucun produit pour l'instant.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($produits as $p): ?>
                <?php $lowStock = (int) $p['quantity'] <= (int) $p['low_stock_threshold']; ?>
                <tr class="<?= $lowStock ? 'bg-red-500/10' : '' ?>">
                    <td class="px-4 py-3">
                        <?php if (!empty($p['image'])): ?>
                            <img src="<?= BASE_URL ?>/public/uploads/produits/<?= htmlspecialchars($p['image']) ?>"
                                alt="<?= htmlspecialchars($p['name']) ?>" class="w-12 h-12 rounded object-cover border border-white/20">
                        <?php else: ?>
                            <div class="w-12 h-12 rounded bg-white/10 border border-white/20 flex items-center justify-center text-white/40"><?= DashboardPage::icon('package', 'w-5 h-5') ?></div>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 font-medium text-white">
                        <div class="flex items-center gap-1 max-w-[160px]">
                            <span class="truncate min-w-0 flex-1"><?= htmlspecialchars($p['name']) ?></span>
                            <button type="button" onclick="showTexte('Nom', <?= htmlspecialchars(json_encode($p['name']), ENT_QUOTES) ?>)"
                                class="shrink-0 text-white/40 hover:text-white text-xs" title="Voir plus">&#9662;</button>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($p['category_name']) ?></td>
                    <td class="px-4 py-3 text-white/60">
                        <div class="flex items-center gap-1 max-w-[200px]">
                            <span class="truncate min-w-0 flex-1"><?= htmlspecialchars($p['description'] ?? '') ?></span>
                            <button type="button" onclick="showTexte('Description', <?= htmlspecialchars(json_encode($p['description'] ?? ''), ENT_QUOTES) ?>)"
                                class="shrink-0 text-white/40 hover:text-white text-xs" title="Voir plus">&#9662;</button>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-semibold <?= $lowStock ? 'text-red-400' : 'text-white' ?>">
                        <?= (int) $p['quantity'] ?>
                        <?php if ($lowStock): ?>
                            <span class="ml-1 inline-block bg-red-600 text-white text-xs px-2 py-0.5 rounded-full align-middle">Stock faible</span>
                        <?php endif; ?>
                    </td>
                    <?php if (Auth::isAdmin()): ?>
                        <td class="px-4 py-3 text-right text-white/60">
                            <?= $p['cost_price'] !== null ? number_format((float) $p['cost_price'], 2) : '—' ?>
                        </td>
                    <?php endif; ?>
                    <td class="px-4 py-3 text-right text-white"><?= number_format((float) $p['unit_price'], 2) ?></td>
                    <?php if (Auth::isAdmin()): ?>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <button type="button" onclick="document.getElementById('edit-<?= $p['id'] ?>').classList.toggle('hidden')"
                                class="text-blue-400 hover:underline">Modifier</button>
                            <button type="button" onclick="document.getElementById('stock-<?= $p['id'] ?>').classList.toggle('hidden')"
                                class="text-orange-400 hover:underline">Ajuster stock</button>
                            <a href="<?= BASE_URL ?>/produits?historique=<?= urlencode($p['id']) ?>" class="text-white/60 hover:underline">Historique</a>
                            <form method="POST" action="<?= BASE_URL ?>/produits" class="inline"
                                onsubmit="return confirm('Supprimer ce produit ?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                                <button type="submit" class="text-red-400 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php if (Auth::isAdmin()): ?>
                    <tr id="stock-<?= $p['id'] ?>" class="hidden bg-orange-500/10">
                        <td colspan="8" class="px-4 py-4">
                            <form method="POST" action="<?= BASE_URL ?>/produits" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="ajuster_stock">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Type de mouvement</label>
                                    <select name="movement_type" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                        <option value="reapprovisionnement" class="text-gray-800">Réapprovisionnement</option>
                                        <option value="produit_defectueux" class="text-gray-800">Produit défectueux</option>
                                        <option value="ajustement" class="text-gray-800">Ajustement manuel</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Sens</label>
                                    <select name="sens" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                        <option value="ajouter" class="text-gray-800">Ajouter au stock</option>
                                        <option value="retirer" class="text-gray-800">Retirer du stock</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Quantité</label>
                                    <input type="number" name="movement_quantity" min="1" required
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                </div>
                                <div class="lg:col-span-1">
                                    <label class="block text-sm font-medium text-white/70 mb-1">Raison (optionnelle)</label>
                                    <input type="text" name="reason" placeholder="Ex : livraison fournisseur, produit cassé..."
                                        class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit"
                                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition">
                                        Valider
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
                <?php if (Auth::isAdmin()): ?>
                    <tr id="edit-<?= $p['id'] ?>" class="hidden bg-white/5">
                        <td colspan="8" class="px-4 py-4">
                            <form method="POST" action="<?= BASE_URL ?>/produits" enctype="multipart/form-data" class="grid sm:grid-cols-3 lg:grid-cols-6 gap-4">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="modifier">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($p['id']) ?>">
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Catégorie</label>
                                    <select name="category_id" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                        <?php foreach ($categories as $cat): ?>
                                            <option value="<?= htmlspecialchars($cat['id']) ?>" <?= $cat['id'] === $p['category_id'] ? 'selected' : '' ?> class="text-gray-800">
                                                <?= htmlspecialchars($cat['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Nom</label>
                                    <input type="text" name="name" required value="<?= htmlspecialchars($p['name']) ?>"
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-white/70 mb-1">Description</label>
                                    <input type="text" name="description" value="<?= htmlspecialchars($p['description'] ?? '') ?>"
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Quantité en stock</label>
                                    <p class="w-full bg-white/5 border border-white/15 rounded px-3 py-2 text-white/60">
                                        <?= (int) $p['quantity'] ?> (utilisez "Ajuster le stock" pour la changer)
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Prix unitaire</label>
                                    <input type="number" name="unit_price" min="0" step="0.01" required value="<?= (float) $p['unit_price'] ?>"
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Seuil d'alerte</label>
                                    <input type="number" name="low_stock_threshold" min="1" value="<?= (int) $p['low_stock_threshold'] ?>"
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                </div>
                                <div class="lg:col-span-2">
                                    <label class="block text-sm font-medium text-white/70 mb-1">Nouvelle image (laisser vide pour ne pas changer)</label>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp"
                                        class="w-full bg-white/10 border border-white/20 text-white/80 rounded px-3 py-2">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit"
                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded transition">
                                        Enregistrer
                                    </button>
                                </div>
                            </form>
                        </td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Boîte flottante pour afficher le texte complet -->
<div id="texte-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div id="texte-backdrop" class="absolute inset-0 bg-black/60"></div>
    <div class="relative bg-navy-900 border border-white/20 rounded-lg shadow-lg max-w-md w-full max-h-[80vh] overflow-y-auto p-5">
        <div class="flex items-center justify-between mb-3">
            <h3 id="texte-titre" class="font-semibold text-white"></h3>
            <button type="button" id="texte-close" class="text-white/50 hover:text-white text-lg leading-none">&times;</button>
        </div>
        <p id="texte-contenu" class="text-white/80 text-sm whitespace-pre-wrap"></p>
    </div>
</div>

<script>
    const texteModal = document.getElementById('texte-modal');
    const texteTitre = document.getElementById('texte-titre');
    const texteContenu = document.getElementById('texte-contenu');

    function showTexte(titre, texte) {
        texteTitre.textContent = titre;
        texteContenu.textContent = texte || '—';
        texteModal.classList.remove('hidden');
    }

    function hideTexte() {
        texteModal.classList.add('hidden');
    }

    document.getElementById('texte-close').addEventListener('click', hideTexte);
    document.getElementById('texte-backdrop').addEventListener('click', hideTexte);
</script>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>