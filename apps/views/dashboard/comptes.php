<?php

/** @var array $moi */
/** @var array|null $vendeurs */
/** @var string $csrfToken */
?>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-white">Comptes</h1>
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

<?php if (Auth::mustChangePassword()): ?>
    <div class="bg-orange-500/15 border border-orange-400/30 text-orange-200 text-sm px-4 py-3 rounded mb-6">
        Votre mot de passe est temporaire. Vous devez le changer ci-dessous avant d'accéder au reste de la plateforme.
    </div>
<?php endif; ?>

<div class="grid lg:grid-cols-2 gap-6 mb-6">

    <!-- Mon profil -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6">
        <div class="flex items-center gap-4 mb-5">
            <div class="w-14 h-14 rounded-full bg-brand-orange text-white flex items-center justify-center text-xl font-bold shrink-0">
                <?= htmlspecialchars(mb_strtoupper(mb_substr($moi['name'], 0, 1))) ?>
            </div>
            <div class="min-w-0">
                <p class="text-white font-semibold truncate"><?= htmlspecialchars($moi['name']) ?></p>
                <p class="text-xs text-white/50"><?= Auth::isAdmin() ? 'Administrateur' : 'Vendeur' ?> · @<?= htmlspecialchars($moi['username']) ?></p>
            </div>
        </div>

        <h2 class="font-semibold text-white mb-3 text-sm">Mon profil</h2>
        <form method="POST" action="<?= BASE_URL ?>/utilisateurs" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="modifier_profil">
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Nom complet</label>
                <input type="text" name="name" required value="<?= htmlspecialchars($moi['name']) ?>"
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Email</label>
                <input type="email" name="email" required value="<?= htmlspecialchars($moi['email']) ?>"
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Téléphone</label>
                <input type="text" name="phone" value="<?= htmlspecialchars($moi['phone'] ?? '') ?>"
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition text-sm">
                Enregistrer le profil
            </button>
        </form>
    </div>

    <!-- Changer le mot de passe -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6">
        <h2 class="font-semibold text-white mb-3 text-sm">Changer le mot de passe</h2>
        <form method="POST" action="<?= BASE_URL ?>/utilisateurs" class="space-y-3">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="changer_mot_de_passe">
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Mot de passe actuel</label>
                <input type="password" name="current_password" required
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Nouveau mot de passe</label>
                <input type="password" name="new_password" required minlength="6"
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Confirmer le nouveau mot de passe</label>
                <input type="password" name="confirm_password" required minlength="6"
                    class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-4 py-2 rounded transition text-sm">
                Changer le mot de passe
            </button>
        </form>
    </div>
</div>

<?php if (Auth::isAdmin()): ?>

    <!-- Ajouter un vendeur -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg p-6 mb-6">
        <h2 class="font-semibold text-white mb-1">Ajouter un vendeur</h2>
        <p class="text-sm text-white/60 mb-4">
            Le mot de passe saisi est temporaire : le vendeur devra le changer dès sa première connexion.
        </p>
        <form method="POST" action="<?= BASE_URL ?>/utilisateurs" class="grid sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
            <input type="hidden" name="action" value="ajouter_vendeur">
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Nom complet</label>
                <input type="text" name="name" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Email</label>
                <input type="email" name="email" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Téléphone</label>
                <input type="text" name="phone" class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Identifiant</label>
                <input type="text" name="username" required class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div>
                <label class="block text-xs font-medium text-white/70 mb-1">Mot de passe temporaire</label>
                <input type="text" name="password" required minlength="6" class="w-full bg-white/10 border border-white/20 text-white rounded px-3 py-2">
            </div>
            <div class="lg:col-span-5">
                <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-2.5 rounded transition">
                    Créer le compte vendeur
                </button>
            </div>
        </form>
    </div>

    <!-- Liste des vendeurs -->
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg shadow-lg overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-white/5 text-white/50 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Nom</th>
                    <th class="px-4 py-3 text-left">Identifiant</th>
                    <th class="px-4 py-3 text-left">Email</th>
                    <th class="px-4 py-3 text-left">Téléphone</th>
                    <th class="px-4 py-3 text-left">Statut</th>
                    <th class="px-4 py-3 text-left">Créé le</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-white/10">
                <?php if (empty($vendeurs)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-white/40">Aucun vendeur pour l'instant.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($vendeurs as $v): ?>
                    <tr>
                        <td class="px-4 py-3 font-medium text-white"><?= htmlspecialchars($v['name']) ?></td>
                        <td class="px-4 py-3 text-white/60">@<?= htmlspecialchars($v['username']) ?></td>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($v['email']) ?></td>
                        <td class="px-4 py-3 text-white/60"><?= htmlspecialchars($v['phone'] ?? '—') ?></td>
                        <td class="px-4 py-3">
                            <?php if (!empty($v['must_change_password'])): ?>
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-500/20 text-yellow-200">Mot de passe temporaire</span>
                            <?php else: ?>
                                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-medium bg-green-500/20 text-green-300">Actif</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-white/60 whitespace-nowrap"><?= htmlspecialchars(date('d/m/Y', strtotime($v['created_at']))) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php endif; ?>
<script>
    window.addEventListener('pageshow', function(e) {
        if (e.persisted) {
            window.location.reload();
        }
    });
</script>