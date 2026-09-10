<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Quincaillerie de la liberté</title>
    <link href="<?= BASE_URL ?>/public/css/output.css?v=<?= filemtime(BASE_PATH . '/public/css/output.css') ?>" rel="stylesheet">
</head>

<body class="bg-white min-h-screen overflow-x-hidden">

    <div class="md:grid md:grid-cols-2 min-h-screen ">
        <!-- Logo -->
        <div class="hidden md:flex items-center justify-center bg-white p-12 min-w-0">
            <img src="<?= BASE_URL ?>/public/images/logo-full.jpg" alt="Quincaillerie de la Liberté"
                class="w-full max-w-sm object-contain rounded-lg">
        </div>

        <!-- Formulaire -->
        <div class="flex items-center justify-center min-h-screen md:min-h-0 bg-white md:bg-navy-950 px-6 py-12 sm:p-12 min-w-0">
            <div class="w-full max-w-sm">
                <img src="<?= BASE_URL ?>/public/images/logo.png" alt="Quincaillerie de la Liberté"
                    class="w-20 h-20 object-contain mx-auto mb-4 md:hidden rounded-lg bg-white p-1">

                <h1 class="text-2xl font-bold text-center text-gray-800 md:text-white mb-1">Quincaillerie</h1>
                <p class="text-center text-gray-500 md:text-white/60 mb-6">Connectez-vous à votre compte</p>

                <?php if (!empty($error)): ?>
                    <div class="bg-red-100 text-red-700 text-sm px-4 py-2 rounded mb-4">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= BASE_URL ?>/login" class="space-y-4" autocomplete="off">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 md:text-white/80 mb-1">Identifiant</label>
                        <input type="text" name="username" required autofocus
                            value="<?= htmlspecialchars($username ?? '') ?>"
                            autocomplete="off"
                            class="w-full bg-white border border-gray-300 md:border-white/20 rounded px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 md:text-white/80 mb-1">Mot de passe</label>
                        <input type="password" name="password" required autocomplete="new-password"
                            class="w-full bg-white border border-gray-300 md:border-white/20 rounded px-3 py-2 text-gray-800 focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <button type="submit"
                        class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 rounded transition">
                        Se connecter
                    </button>
                </form>

                <a href="<?= BASE_URL ?>/" class="block text-center text-sm text-gray-400 md:text-white/40 hover:text-gray-600 md:hover:text-white/70 mt-6">
                    &larr; Retour à l'accueil
                </a>
            </div>
        </div>
    </div>

    <!-- Neutralise le bouton précédent : chaque retour est renvoyé vers l'avant -->
    <script>
        (function() {
            'use strict';

            // Page restaurée depuis le cache du navigateur (bfcache) -> rechargement forcé
            window.addEventListener('pageshow', function(e) {
                if (e.persisted) {
                    window.location.reload();
                }
            });

            // Entrée sentinelle devant le bouton précédent
            history.pushState(null, '', location.href);

            // Toute tentative de retour -> on force le retour AVANT (vers la sentinelle = login)
            window.addEventListener('popstate', function() {
                history.go(1);
            });
        })();
    </script>

</body>

</html>