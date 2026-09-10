<?php

/** @var array $categories */
/** @var string $csrfToken */ ?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Catégories de produits</h1>
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
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-white mb-4">Ajouter une catégorie</h2>
        <form method="POST" action="<?= BASE_URL ?>/categories" class="grid sm:grid-cols-3 gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="ajouter">
            <div class="sm:col-span-1">
                <label class="block text-sm font-medium text-white/70 mb-1">Nom</label>
                <input type="text" name="name" required
                    class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="sm:col-span-1">
                <label class="block text-sm font-medium text-white/70 mb-1">Description</label>
                <input type="text" name="description"
                    class="w-full bg-white/10 border border-white/20 text-white placeholder-white/40 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="sm:col-span-1 flex items-end">
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition">
                    Ajouter
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead class="bg-white/5 text-white/50 uppercase text-xs">
            <tr>
                <th class="px-4 py-3 text-left">Nom</th>
                <th class="px-4 py-3 text-left">Description</th>
                <?php if (Auth::isAdmin()): ?>
                    <th class="px-4 py-3 text-right">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            <?php if (empty($categories)): ?>
                <tr>
                    <td colspan="3" class="px-4 py-6 text-center text-white/40">Aucune catégorie pour l'instant.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td class="px-4 py-3 font-medium text-white"><?= htmlspecialchars($cat['name']) ?></td>
                    <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($cat['description'] ?? '') ?></td>
                    <?php if (Auth::isAdmin()): ?>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button type="button" onclick="document.getElementById('edit-<?= $cat['id'] ?>').classList.toggle('hidden')"
                                class="text-blue-400 hover:underline">Modifier</button>
                            <form method="POST" action="<?= BASE_URL ?>/categories" class="inline"
                                onsubmit="return confirm('Supprimer cette catégorie ?');">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="supprimer">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($cat['id']) ?>">
                                <button type="submit" class="text-red-400 hover:underline">Supprimer</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php if (Auth::isAdmin()): ?>
                    <tr id="edit-<?= $cat['id'] ?>" class="hidden bg-white/5">
                        <td colspan="3" class="px-4 py-4">
                            <form method="POST" action="<?= BASE_URL ?>/categories" class="grid sm:grid-cols-3 gap-4">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <input type="hidden" name="action" value="modifier">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($cat['id']) ?>">
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Nom</label>
                                    <input type="text" name="name" required value="<?= htmlspecialchars($cat['name']) ?>"
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-white/70 mb-1">Description</label>
                                    <input type="text" name="description" value="<?= htmlspecialchars($cat['description'] ?? '') ?>"
                                        class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
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
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>