<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quincaillerie de la Liberté</title>
    <link href="<?= BASE_URL ?>/public/css/output.css?v=<?= filemtime(BASE_PATH . '/public/css/output.css') ?>" rel="stylesheet">

    <!-- ✅ SECOURS Tailwind CDN : génère les nouvelles classes -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
                    },
                    colors: {
                        navy: {
                            900: '#0f172a',
                            950: '#020617'
                        }
                    },
                    backgroundImage: {
                        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))'
                    }
                }
            }
        };
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
        }

        #accueil,
        #map-section {
            scroll-margin-top: 64px;
        }

        /* ============================================================
           ✅ FOND HERO — portage du composant "elegant-dark-pattern"
        ============================================================ */
        .hero-bg {
            position: absolute;
            inset: 0;
            background: radial-gradient(100% 100% at 0% 0%, rgb(46, 46, 46) 0%, rgb(0, 0, 0) 100%);
            -webkit-mask-image: radial-gradient(125% 100% at 0% 0%, rgb(0, 0, 0) 0%, rgba(0, 0, 0, 0.224) 88.3%, rgba(0, 0, 0, 0) 100%);
            mask-image: radial-gradient(125% 100% at 0% 0%, rgb(0, 0, 0) 0%, rgba(0, 0, 0, 0.224) 88.3%, rgba(0, 0, 0, 0) 100%);
        }

        .hero-streak {
            position: absolute;
            inset: 0;
            opacity: 0.2;
            background: linear-gradient(rgb(0, 207, 255) 0%, rgba(0, 207, 255, 0) 100%);
            transform: skewX(45deg);
        }

        .hero-streak-1 {
            -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 0%, #000 20%, rgba(0, 0, 0, 0) 36%, #000 55%, rgba(0, 0, 0, .13) 67%, #000 78%, rgba(0, 0, 0, 0) 97%);
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 0%, #000 20%, rgba(0, 0, 0, 0) 36%, #000 55%, rgba(0, 0, 0, .13) 67%, #000 78%, rgba(0, 0, 0, 0) 97%);
        }

        .hero-streak-2 {
            -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 11%, #000 25%, rgba(0, 0, 0, .55) 41%, rgba(0, 0, 0, .13) 67%, #000 78%, rgba(0, 0, 0, 0) 97%);
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 11%, #000 25%, rgba(0, 0, 0, .55) 41%, rgba(0, 0, 0, .13) 67%, #000 78%, rgba(0, 0, 0, 0) 97%);
        }

        .hero-streak-3 {
            -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 9%, #000 20%, rgba(0, 0, 0, .55) 28%, rgba(0, 0, 0, .424) 40%, #000 48%, rgba(0, 0, 0, .267) 54%, rgba(0, 0, 0, .13) 78%, #000 88%, rgba(0, 0, 0, 0) 97%);
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 9%, #000 20%, rgba(0, 0, 0, .55) 28%, rgba(0, 0, 0, .424) 40%, #000 48%, rgba(0, 0, 0, .267) 54%, rgba(0, 0, 0, .13) 78%, #000 88%, rgba(0, 0, 0, 0) 97%);
        }

        .hero-streak-4 {
            -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 0%, #000 17%, rgba(0, 0, 0, .55) 26%, #000 35%, rgba(0, 0, 0, 0) 47%, rgba(0, 0, 0, .13) 69%, #000 79%, rgba(0, 0, 0, 0) 97%);
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 0%, #000 17%, rgba(0, 0, 0, .55) 26%, #000 35%, rgba(0, 0, 0, 0) 47%, rgba(0, 0, 0, .13) 69%, #000 79%, rgba(0, 0, 0, 0) 97%);
        }

        .hero-streak-5 {
            -webkit-mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 0%, #000 20%, rgba(0, 0, 0, .55) 27%, #000 42%, rgba(0, 0, 0, 0) 48%, rgba(0, 0, 0, .13) 67%, #000 74%, #000 82%, rgba(0, 0, 0, .47) 88%, rgba(0, 0, 0, 0) 97%);
            mask-image: linear-gradient(90deg, rgba(0, 0, 0, 0) 0%, #000 20%, rgba(0, 0, 0, .55) 27%, #000 42%, rgba(0, 0, 0, 0) 48%, rgba(0, 0, 0, .13) 67%, #000 74%, #000 82%, rgba(0, 0, 0, .47) 88%, rgba(0, 0, 0, 0) 97%);
        }

        .hero-texture {
            position: absolute;
            inset: 0;
            opacity: 0.05;
            background-image: url("https://cdn.21st.dev/assets/mirror/f5/f55dfc553c100e6da0ad95258a042b4100f0ff4bb03a5313d1f541984275e262.png");
            background-size: 149.76px;
            background-repeat: repeat;
        }

        .hero-dots {
            position: absolute;
            inset: 0;
            opacity: 0.2;
            background-image: radial-gradient(circle at 1px 1px, rgba(255, 255, 255, 0.5) 1px, transparent 0);
            background-size: 20px 20px;
        }

        /* ============================================================
           GOOGLE MAPS
        ============================================================ */
        #gmap-frame {
            width: 100%;
            height: 480px;
            border: 0;
            display: block;
        }

        @media (max-width: 640px) {
            #gmap-frame {
                height: 340px;
            }
        }

        #map-route-btn {
            transition: all 0.25s ease;
        }

        #map-route-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249, 115, 22, 0.4);
        }

        #map-route-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .map-card {
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12);
        }

        .map-pulse {
            animation: pulse-ring 2s ease-out infinite;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(249, 115, 22, 0.5);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(249, 115, 22, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(249, 115, 22, 0);
            }
        }

        .line-clamp-1 {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <!-- ============================================================
         NAVBAR
    ============================================================ -->
    <header class="relative bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= BASE_URL ?>/" class="flex items-center gap-2 font-bold text-lg text-gray-900">
                    <img src="<?= BASE_URL ?>/public/images/logo.png" alt="Quincaillerie de la Liberté"
                        class="w-14 h-14 object-contain" style="width:56px;height:56px;object-fit:contain;flex-shrink:0;">
                    <span class="hidden sm:inline">Quincaillerie de la Liberté</span>
                    <span class="sm:hidden">Q. Liberté</span>
                </a>

                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-gray-600">
                    <a href="#accueil" class="hover:text-orange-600">Accueil</a>
                    <a href="#produits" class="hover:text-orange-600">Nos produits</a>
                    <a href="#apropos" class="hover:text-orange-600">À propos</a>
                    <a href="#contact" class="hover:text-orange-600">Contact</a>
                    <a href="#map-section" class="hover:text-orange-600">Nous trouver</a>
                </nav>

                <div class="hidden md:block">
                    <a href="<?= BASE_URL ?>/login"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2 rounded-lg transition">
                        Connexion
                    </a>
                </div>

                <button id="menu-toggle" aria-label="Ouvrir le menu"
                    class="md:hidden inline-flex items-center justify-center p-2 rounded text-gray-600 hover:bg-gray-100">
                    <svg id="icon-open" width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close" width="24" height="24" class="hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Menu mobile -->
        <div id="mobile-menu" class="hidden md:hidden absolute right-4 top-full mt-2 w-64 rounded-xl border border-gray-200 bg-white shadow-xl z-50">
            <div class="p-3 space-y-1 text-sm font-medium text-gray-600">
                <a href="#accueil" class="block px-3 py-2 rounded-lg hover:bg-gray-100">Accueil</a>
                <a href="#produits" class="block px-3 py-2 rounded-lg hover:bg-gray-100">Nos produits</a>
                <a href="#apropos" class="block px-3 py-2 rounded-lg hover:bg-gray-100">À propos</a>
                <a href="#contact" class="block px-3 py-2 rounded-lg hover:bg-gray-100">Contact</a>
                <a href="#map-section" class="block px-3 py-2 rounded-lg hover:bg-gray-100">Nous trouver</a>
                <a href="<?= BASE_URL ?>/login"
                    class="block text-center mt-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2 rounded-lg transition">
                    Connexion
                </a>
            </div>
        </div>
    </header>

    <!-- ============================================================
         ✅ HERO — fond "elegant-dark-pattern" | sans localisation
         ni produits | responsive mobile → desktop
    ============================================================ -->
    <section id="accueil" class="relative overflow-hidden bg-black text-white">

        <!-- ✅ Fond : dégradé sombre + reflets bleus inclinés -->
        <div class="absolute inset-0 pointer-events-none" aria-hidden="true">
            <div class="hero-bg">
                <div class="hero-streak hero-streak-1"></div>
                <div class="hero-streak hero-streak-2"></div>
                <div class="hero-streak hero-streak-3"></div>
                <div class="hero-streak hero-streak-4"></div>
                <div class="hero-streak hero-streak-5"></div>
            </div>
            <div class="hero-texture"></div>
            <div class="hero-dots"></div>
            <div class="absolute inset-0 bg-gradient-radial from-slate-800/20 via-transparent to-transparent"></div>
        </div>

        <!-- Halos orange décoratifs -->
        <div class="absolute -top-24 -right-24 w-72 h-72 sm:w-96 sm:h-96 bg-orange-500/15 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-24 w-64 h-64 sm:w-80 sm:h-80 bg-orange-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- ✅ Contenu centré, pleine hauteur (navbar 4rem déduite) -->
        <div class="relative z-10 flex min-h-[calc(100vh-4rem)] items-center">
            <div class="w-full max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20 lg:py-24 text-center">

                <!-- Badge (sans localisation) -->
                <span class="inline-flex items-center gap-2 bg-orange-500/15 border border-orange-500/30 text-orange-400 text-xs font-bold px-4 py-1.5 rounded-full mb-6">
                    <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span>
                    Depuis 2010
                </span>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-extrabold leading-tight mb-6">
                    Tout pour la construction,
                    <span class="text-orange-500">au même endroit</span>
                </h1>

                <p class="text-gray-300 text-base sm:text-lg mb-8 sm:mb-10 max-w-2xl mx-auto">
                    Matériaux, outillage, plomberie, électricité, peinture... la Quincaillerie de la Liberté
                    accompagne particuliers et professionnels avec des produits de qualité et des prix justes.
                </p>

                <!-- ✅ Boutons : empilés pleine largeur sur mobile -->
                <div class="flex flex-col sm:flex-row justify-center gap-3 sm:gap-4 mb-12 sm:mb-16">
                    <a href="#produits"
                        class="w-full sm:w-auto bg-orange-500 hover:bg-orange-600 text-white font-semibold px-8 py-3.5 sm:py-3 rounded-xl transition shadow-lg shadow-orange-500/25">
                        Découvrir nos produits
                    </a>
                    <a href="#contact"
                        class="w-full sm:w-auto border border-gray-500 hover:border-white text-white font-semibold px-8 py-3.5 sm:py-3 rounded-xl transition">
                        Nous contacter
                    </a>
                </div>

                <!-- ✅ Stats : 2 colonnes mobile, 4 à partir de md -->
                <dl class="grid grid-cols-2 md:grid-cols-4 gap-6 sm:gap-8 pt-8 sm:pt-10 border-t border-white/10">
                    <div>
                        <dt class="text-2xl sm:text-3xl font-extrabold text-orange-500">+500</dt>
                        <dd class="text-gray-400 text-xs sm:text-sm mt-1">Références produits</dd>
                    </div>
                    <div>
                        <dt class="text-2xl sm:text-3xl font-extrabold text-orange-500">15+</dt>
                        <dd class="text-gray-400 text-xs sm:text-sm mt-1">Années d'expérience</dd>
                    </div>
                    <div>
                        <dt class="text-2xl sm:text-3xl font-extrabold text-orange-500">1000+</dt>
                        <dd class="text-gray-400 text-xs sm:text-sm mt-1">Clients satisfaits</dd>
                    </div>
                    <div>
                        <dt class="text-2xl sm:text-3xl font-extrabold text-orange-500">6j/7</dt>
                        <dd class="text-gray-400 text-xs sm:text-sm mt-1">Ouvert toute la semaine</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <!-- ============================================================
         PRODUITS (carrousel)
    ============================================================ -->
    <section id="produits" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">

            <div>
                <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 gap-4">
                    <div>
                        <span class="text-orange-500 font-semibold text-sm uppercase tracking-wider block mb-1">Nos Produits en Démonstration</span>
                        <h2 class="text-3xl font-bold text-gray-900">Produits en vedette sur le site</h2>
                        <p class="text-gray-500 text-sm mt-1">Cliquez sur une image pour accéder à la page catalogue correspondante.</p>
                    </div>
                    <div>
                        <a href="<?= BASE_URL ?>/catalogue"
                            class="inline-flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl text-sm transition shadow-sm hover:shadow-md">
                            Voir tout le catalogue ➔
                        </a>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="p-12 text-center bg-gray-50 rounded-2xl border border-gray-200 text-gray-400">
                        Aucun produit n'est publié actuellement dans le catalogue.
                    </div>
                <?php else: ?>
                    <div id="modern-carousel" class="relative group max-w-full">
                        <div class="overflow-hidden rounded-2xl py-2 px-1">
                            <div id="carousel-track" class="flex transition-transform duration-500 ease-out gap-6">
                                <?php foreach ($products as $p): ?>
                                    <?php
                                    $imageUrl = !empty($p['image'])
                                        ? BASE_URL . '/public/uploads/produits/' . $p['image']
                                        : null;
                                    ?>
                                    <div class="carousel-card shrink-0 w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)] bg-white border border-gray-200 hover:border-orange-400 rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                                        <div>
                                            <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                                class="relative block h-56 bg-gray-100 overflow-hidden group/img">
                                                <?php if ($imageUrl): ?>
                                                    <img src="<?= $imageUrl ?>"
                                                        alt="<?= htmlspecialchars($p['name']) ?>"
                                                        class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-500">
                                                <?php else: ?>
                                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-50">
                                                        <svg width="56" height="56" class="stroke-1 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                        </svg>
                                                        <span class="text-xs text-gray-400">Photo non disponible</span>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover/img:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                                    <span class="bg-orange-500 text-white font-bold text-xs px-4 py-2 rounded-full shadow-lg transform translate-y-2 group-hover/img:translate-y-0 transition-transform duration-300 flex items-center gap-1.5">
                                                        <span>Ouvrir dans le catalogue</span>
                                                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                                        </svg>
                                                    </span>
                                                </div>

                                                <div class="absolute top-3 left-3">
                                                    <span class="px-3 py-1 rounded-full bg-white/95 backdrop-blur-md text-gray-800 text-[11px] font-bold shadow-md">
                                                        <?= htmlspecialchars($p['category_name']) ?>
                                                    </span>
                                                </div>
                                            </a>

                                            <div class="p-5 space-y-2">
                                                <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                                    class="font-bold text-gray-900 hover:text-orange-600 transition-colors line-clamp-1 block text-lg">
                                                    <?= htmlspecialchars($p['name']) ?>
                                                </a>
                                                <p class="text-xs text-gray-500 line-clamp-2 min-h-[32px]">
                                                    <?= !empty($p['description']) ? htmlspecialchars($p['description']) : 'Produit disponible au magasin de la Quincaillerie.' ?>
                                                </p>
                                            </div>
                                        </div>

                                        <div class="px-5 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                                            <div>
                                                <span class="text-[10px] text-gray-400 uppercase tracking-wider block">Prix unitaire</span>
                                                <span class="text-lg font-black text-orange-600">
                                                    <?= number_format($p['unit_price'], 2, ',', ' ') ?> HTG
                                                </span>
                                            </div>
                                            <a href="<?= BASE_URL ?>/catalogue?id=<?= urlencode($p['id']) ?>"
                                                class="px-3.5 py-1.5 rounded-lg bg-orange-100 hover:bg-orange-500 text-orange-600 hover:text-white font-semibold text-xs transition">
                                                Voir ➔
                                            </a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <button id="carousel-prev" aria-label="Slide précédent"
                            class="absolute top-1/2 -left-4 md:-left-6 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/90 hover:bg-orange-500 text-gray-700 hover:text-white shadow-xl backdrop-blur-md flex items-center justify-center transition-all duration-300 border border-gray-200">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                            </svg>
                        </button>

                        <button id="carousel-next" aria-label="Slide suivant"
                            class="absolute top-1/2 -right-4 md:-right-6 -translate-y-1/2 z-10 w-12 h-12 rounded-full bg-white/90 hover:bg-orange-500 text-gray-700 hover:text-white shadow-xl backdrop-blur-md flex items-center justify-center transition-all duration-300 border border-gray-200">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>

                        <div id="carousel-dots" class="flex items-center justify-center gap-2 mt-6"></div>
                    </div>

                    <script>
                        (function() {
                            const track = document.getElementById('carousel-track');
                            const prevBtn = document.getElementById('carousel-prev');
                            const nextBtn = document.getElementById('carousel-next');
                            const dotsContainer = document.getElementById('carousel-dots');
                            const cards = track ? track.querySelectorAll('.carousel-card') : [];
                            if (!track || cards.length === 0) return;

                            let currentIndex = 0;

                            function getVisibleCardsCount() {
                                if (window.innerWidth >= 1024) return 3;
                                if (window.innerWidth >= 640) return 2;
                                return 1;
                            }

                            function getMaxIndex() {
                                return Math.max(0, cards.length - getVisibleCardsCount());
                            }

                            function updateDots() {
                                const maxIndex = getMaxIndex();
                                dotsContainer.innerHTML = '';
                                for (let i = 0; i <= maxIndex; i++) {
                                    const dot = document.createElement('button');
                                    dot.type = 'button';
                                    dot.ariaLabel = 'Aller au slide ' + (i + 1);
                                    dot.className = i === currentIndex ?
                                        'w-8 h-2.5 rounded-full bg-orange-500 transition-all duration-300' :
                                        'w-2.5 h-2.5 rounded-full bg-gray-300 hover:bg-orange-300 transition-all duration-300';
                                    dot.addEventListener('click', () => {
                                        currentIndex = i;
                                        moveCarousel();
                                    });
                                    dotsContainer.appendChild(dot);
                                }
                            }

                            function moveCarousel() {
                                const maxIndex = getMaxIndex();
                                if (currentIndex > maxIndex) currentIndex = maxIndex;
                                if (currentIndex < 0) currentIndex = 0;
                                const cardWidth = cards[0].offsetWidth + 24;
                                track.style.transform = `translateX(-${currentIndex * cardWidth}px)`;
                                updateDots();
                            }

                            prevBtn.addEventListener('click', () => {
                                currentIndex = currentIndex > 0 ? currentIndex - 1 : getMaxIndex();
                                moveCarousel();
                            });
                            nextBtn.addEventListener('click', () => {
                                currentIndex = currentIndex < getMaxIndex() ? currentIndex + 1 : 0;
                                moveCarousel();
                            });
                            window.addEventListener('resize', moveCarousel);

                            let autoPlayTimer = setInterval(() => {
                                currentIndex = currentIndex < getMaxIndex() ? currentIndex + 1 : 0;
                                moveCarousel();
                            }, 4000);
                            const container = document.getElementById('modern-carousel');
                            container.addEventListener('mouseenter', () => clearInterval(autoPlayTimer));
                            container.addEventListener('mouseleave', () => {
                                autoPlayTimer = setInterval(() => {
                                    currentIndex = currentIndex < getMaxIndex() ? currentIndex + 1 : 0;
                                    moveCarousel();
                                }, 4000);
                            });

                            let startX = 0,
                                isDragging = false;
                            track.addEventListener('touchstart', (e) => {
                                startX = e.touches[0].clientX;
                                isDragging = true;
                            }, {
                                passive: true
                            });
                            track.addEventListener('touchend', (e) => {
                                if (!isDragging) return;
                                const diffX = startX - e.changedTouches[0].clientX;
                                if (diffX > 50) currentIndex = currentIndex < getMaxIndex() ? currentIndex + 1 : 0;
                                else if (diffX < -50) currentIndex = currentIndex > 0 ? currentIndex - 1 : getMaxIndex();
                                moveCarousel();
                                isDragging = false;
                            }, {
                                passive: true
                            });

                            updateDots();
                        })();
                    </script>
                <?php endif; ?>
            </div>

            <?php
            /* ============================================================
               ✅ Icônes modernes (style Lucide) pour les catégories
               - Détection auto par mots-clés du nom de la catégorie
               - Sinon attribution cyclique dans l'ordre du tableau
            ============================================================ */
            if (!function_exists('ql_icon_svg')) {
                function ql_icon_svg(string $paths, string $class = 'w-6 h-6'): string
                {
                    return '<svg class="' . $class . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' . $paths . '</svg>';
                }
            }

            if (!function_exists('ql_categorie_icon')) {
                function ql_categorie_icon(string $name, array $icones, int $fallbackIndex): string
                {
                    $n = function_exists('mb_strtolower') ? mb_strtolower($name) : strtolower($name);
                    foreach ($icones as $icone) {
                        foreach ($icone['keywords'] as $kw) {
                            if (strpos($n, $kw) !== false) {
                                return ql_icon_svg($icone['svg']);
                            }
                        }
                    }
                    return ql_icon_svg($icones[$fallbackIndex % count($icones)]['svg']);
                }
            }

            $categorieIcones = [
                // Matériaux / construction
                [
                    'keywords' => ['materia', 'construction', 'ciment', 'beton', 'béton', 'briqu', 'agglo', 'sable', 'gravier', 'pierre'],
                    'svg' => '<path d="M12 2 2 7l10 5 10-5-10-5Z"/><path d="m2 12 10 5 10-5"/><path d="m2 17 10 5 10-5"/>'
                ],

                // Outillage
                [
                    'keywords' => ['outil', 'outill', 'visse', 'perce', 'marteau', 'scie', 'cliqu', 'tournevis'],
                    'svg' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>'
                ],

                // Plomberie / sanitaire
                [
                    'keywords' => ['plomb', 'tuyau', 'robin', 'sanitair', 'eau', 'douche', 'wc', 'evacuation', 'évacuation', 'pompe'],
                    'svg' => '<path d="M12 22a7 7 0 0 0 7-7c0-2-1-3.9-3-5.5s-3.5-4-4-6.5c-.5 2.5-2 4.9-4 6.5C6 11.1 5 13 5 15a7 7 0 0 0 7 7z"/>'
                ],

                // Électricité / éclairage
                [
                    'keywords' => ['electri', 'électri', 'luminaire', 'ampoul', 'cable', 'câble', 'prise', 'interrupt', 'led', 'neon', 'néon', 'solaire', 'courant'],
                    'svg' => '<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'
                ],

                // Peinture
                [
                    'keywords' => ['peint', 'pinceau', 'vernis', 'enduit', 'couleur', 'teinte'],
                    'svg' => '<rect width="16" height="6" x="2" y="2" rx="2"/><path d="M10 16v-2a2 2 0 0 1 2-2h8a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect width="4" height="6" x="8" y="16" rx="1"/>'
                ],

                // Bois
                [
                    'keywords' => ['bois', 'planche', 'panneau', 'contreplaqu', 'madrier', 'chevron', 'lame'],
                    'svg' => '<path d="m14 12-8.5 8.5a2.12 2.12 0 1 1-3-3L11 9"/><path d="M15 13 9 7l4-4 6 6h3a8 8 0 0 1-7 7z"/>'
                ],

                // Carrelage / revêtement
                [
                    'keywords' => ['carrel', 'dalle', 'mosaique', 'mosaïque', 'revet', 'revêt', 'tuile', 'faience', 'faïence'],
                    'svg' => '<rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/>'
                ],

                // Visserie / quincaillerie / fixations
                [
                    'keywords' => ['quincaill', 'visserie', 'fixation', 'charni', 'serrur', 'cadenas', 'verrou', 'vis', 'clou', 'boulon', 'ecrou', 'écrou'],
                    'svg' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>'
                ],

                // Sécurité / EPI
                [
                    'keywords' => ['securit', 'sécurit', 'protection', 'casque', 'gant', 'epi', 'equipement', 'équipement', 'chauss'],
                    'svg' => '<path d="M2 18a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1Z"/><path d="M10 10V5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5"/><path d="M4 15v-3a6 6 0 0 1 6-6"/><path d="M14 6a6 6 0 0 1 6 6v3"/>'
                ],

                // Jardin / extérieur
                [
                    'keywords' => ['jardin', 'plant', 'pelouse', 'graine', 'arros', 'potager', 'exterieur', 'extérieur', 'terreau'],
                    'svg' => '<path d="M7 20h10"/><path d="M10 20c5.5-2.5.8-6.4 3-10"/><path d="M9.5 9.4c1.1.8 1.8 2.2 2.3 3.7-2 .4-3.5.4-4.8-.3-1.2-.6-2.3-1.9-3-4.2 2.8-.5 4.4 0 5.5.8z"/><path d="M14.1 6a7 7 0 0 0-1.1 4c1.9-.1 3.3-.6 4.3-1.4 1-1.4 1.6-3.1 1.7-4.6-2.7.1-4 1-4.9 2z"/>'
                ],

                // Rangement / stockage
                [
                    'keywords' => ['rangement', 'coffre', 'etagere', 'étagère', 'boite', 'boîte', 'stockage', 'poubelle'],
                    'svg' => '<rect width="20" height="5" x="2" y="3" rx="1"/><path d="M4 8v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>'
                ],

                // Livraison / transport
                [
                    'keywords' => ['livraison', 'transport', 'camion', 'location'],
                    'svg' => '<path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.62l-3.48-4.35a1 1 0 0 0-.78-.38H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/>'
                ],

                // Mesure / nivellement
                [
                    'keywords' => ['mesure', 'metre', 'mètre', 'niveau', 'laser', 'regle', 'règle'],
                    'svg' => '<path d="M21.3 15.3a2.4 2.4 0 0 1 0 3.4l-2.6 2.6a2.4 2.4 0 0 1-3.4 0L2.7 8.7a2.41 2.41 0 0 1 0-3.4l2.6-2.6a2.41 2.41 0 0 1 3.4 0Z"/><path d="m14.5 12.5 2-2"/><path d="m11.5 9.5 2-2"/><path d="m8.5 6.5 2-2"/><path d="m17.5 15.5 2-2"/>'
                ],

                // Maçonnerie
                [
                    'keywords' => ['maconn', 'maçonn', 'truelle', 'mortier'],
                    'svg' => '<path d="m15 12-8.373 8.373a1 1 0 1 1-3-3L12 9"/><path d="m18 15 4-4"/><path d="m21.5 11.5-1.914-1.914A2 2 0 0 1 19 8.172V7l-2.26-2.26a6 6 0 0 0-4.202-1.756L9 2.96l.92.82A6.18 6.18 0 0 1 12 8.4V10l2 2h1.172a2 2 0 0 1 1.414.586L18.5 14.5"/>'
                ],

                // Divers / accessoires (fallback courant)
                [
                    'keywords' => ['divers', 'accessoire', 'autre'],
                    'svg' => '<path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/>'
                ],
            ];
            ?>

            <!-- ============================================================
                 ✅ CATÉGORIES — icônes modernes auto-détectées
            ============================================================ -->
            <div class="pt-8 border-t border-gray-100">
                <div class="text-center max-w-2xl mx-auto mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Parcourir par catégories</h2>
                    <p class="text-gray-500 text-sm">Découvrez nos rayons spécialisés pour tous vos besoins de quincaillerie.</p>
                </div>
                <?php if (empty($categories)): ?>
                    <p class="text-center text-gray-400">Les catégories de produits seront affichées ici prochainement.</p>
                <?php else: ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php foreach ($categories as $i => $cat): ?>
                            <a href="<?= BASE_URL ?>/catalogue"
                                class="group relative bg-white border border-gray-200 rounded-2xl p-6 hover:border-orange-400 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden">

                                <!-- Liseré dégradé orange au survol -->
                                <span class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-orange-400 to-orange-600 scale-x-0 group-hover:scale-x-100 origin-left transition-transform duration-300 rounded-t-2xl"></span>

                                <!-- ✅ Tuile icône : dégradé + halo ring -->
                                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-orange-600 text-white flex items-center justify-center mb-5 shadow-lg shadow-orange-500/30 ring-4 ring-orange-100 group-hover:ring-orange-200 group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                    <?= ql_categorie_icon((string)($cat['name'] ?? ''), $categorieIcones, $i) ?>
                                </div>

                                <h3 class="font-bold text-gray-900 mb-1 group-hover:text-orange-600 transition-colors"><?= htmlspecialchars($cat['name']) ?></h3>
                                <p class="text-xs text-gray-500 leading-relaxed">
                                    <?= !empty($cat['description'])
                                        ? htmlspecialchars($cat['description'])
                                        : $cat['product_count'] . ' produit' . ($cat['product_count'] > 1 ? 's' : '') . ' disponible' . ($cat['product_count'] > 1 ? 's' : '') ?>
                                </p>

                                <!-- Lien "Explorer" révélé au survol -->
                                <span class="mt-4 inline-flex items-center gap-1 text-xs font-bold text-orange-500 opacity-0 -translate-x-1 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300">
                                    Explorer
                                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" />
                                    </svg>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </section>

    <!-- ============================================================
         À PROPOS — LOGO placé dans la forme orange
    ============================================================ -->
    <section id="apropos" class="py-20 bg-gray-50 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">

            <!-- Texte -->
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Pourquoi choisir notre quincaillerie ?</h2>
                <p class="text-gray-600 mb-6">
                    Nous mettons un point d'honneur à offrir des produits de qualité, des conseils adaptés
                    à chaque projet, et un service rapide, que vous soyez particulier ou professionnel du bâtiment.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span class="text-gray-700">Produits de qualité, prix compétitifs</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span class="text-gray-700">Équipe disponible et à l'écoute</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="w-6 h-6 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </span>
                        <span class="text-gray-700">Large stock disponible en permanence</span>
                    </li>
                </ul>
            </div>

            <!-- Forme orange AVEC LE LOGO -->
            <div class="relative mb-6 md:mb-8">
                <div class="relative bg-gradient-to-br from-orange-400 via-orange-500 to-orange-600 rounded-[2.5rem] h-72 md:h-96 flex items-center justify-center overflow-visible shadow-2xl shadow-orange-500/30">

                    <!-- Cercles décoratifs -->
                    <div class="absolute -top-12 -right-12 w-44 h-44 bg-white/10 rounded-full pointer-events-none"></div>
                    <div class="absolute -bottom-16 -left-10 w-56 h-56 bg-black/10 rounded-full pointer-events-none"></div>
                    <div class="absolute top-8 left-8 w-3 h-3 bg-white/40 rounded-full pointer-events-none"></div>
                    <div class="absolute bottom-10 right-10 w-2 h-2 bg-white/40 rounded-full pointer-events-none"></div>

                    <!-- LE LOGO dans une carte blanche inclinée -->
                    <div class="relative bg-white rounded-[2rem] p-6 shadow-2xl -rotate-3 hover:rotate-0 hover:scale-105 transition-transform duration-500">
                        <img src="<?= BASE_URL ?>/public/images/logo.png"
                            alt="Logo Quincaillerie de la Liberté"
                            class="w-40 h-40 md:w-52 md:h-52 object-contain"
                            style="object-fit:contain;">
                    </div>

                    <!-- Badge flottant "+15 ans" -->
                    <div class="absolute -top-4 -right-3 sm:-right-6 bg-white rounded-2xl shadow-xl px-4 py-2.5 border border-gray-100 text-center">
                        <p class="text-lg font-black text-orange-500 leading-none">15+</p>
                        <p class="text-[10px] font-bold text-gray-500 mt-0.5">ans d'expérience</p>
                    </div>
                </div>

                <!-- Chip flottante sous la forme -->
                <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-white rounded-full shadow-lg border border-gray-100 px-5 py-2.5 flex items-center gap-2 whitespace-nowrap">
                    <span class="w-2.5 h-2.5 bg-orange-500 rounded-full"></span>
                    <span class="text-xs font-bold text-gray-900">Votre confiance, notre fierté</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         CONTACT
    ============================================================ -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-8 text-center md:text-left">
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Adresse</h3>
                <p class="text-gray-500">Terre Blanche, Haïti</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Téléphone</h3>
                <p class="text-gray-500"><a href="tel:+50931234567" class="hover:text-orange-600 transition">+509 31 23 45 67</a></p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Horaires</h3>
                <p class="text-gray-500">Lun - Sam : 7h30 - 18h00<br>Dimanche : fermé</p>
            </div>
        </div>
    </section>

    <!-- ============================================================
         LOCALISATION — GOOGLE MAPS (iframe, sans clé API)
    ============================================================ -->
    <section id="map-section" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- En-tête -->
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 bg-orange-100 text-orange-600 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Nous trouver facilement
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Venez nous rendre visite</h2>
                <p class="text-gray-500">Cliquez sur « Obtenir l'itinéraire » : Google Maps s'ouvre avec le trajet depuis votre position jusqu'à la quincaillerie.</p>
            </div>

            <!-- Carte Card -->
            <div class="map-card rounded-2xl overflow-hidden border border-gray-200 bg-white">

                <!-- Bandeau supérieur -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <div class="map-pulse w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center flex-shrink-0">
                            <svg width="24" height="24" class="text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Quincaillerie de la Liberté</h3>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Terre Blanche, Haïti
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <!-- Bouton itinéraire → Google Maps -->
                        <button id="map-route-btn" onclick="openItinerary()"
                            class="flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl w-full sm:w-auto">
                            <svg id="btn-icon-nav" width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <svg id="btn-icon-spin" width="20" height="20" class="hidden animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span id="btn-label">Obtenir l'itinéraire</span>
                        </button>

                        <!-- Lien direct Google Maps -->
                        <a href="https://www.google.com/maps/search/?api=1&query=18.722538,-72.192672" id="gmaps-view-link" target="_blank" rel="noopener"
                            title="Ouvrir dans Google Maps"
                            class="flex-shrink-0 w-11 h-11 flex items-center justify-center rounded-xl border border-gray-200 hover:bg-orange-50 hover:border-orange-300 text-gray-500 hover:text-orange-500 transition">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Message d'état -->
                <div id="map-status" class="hidden px-6 py-3 text-sm font-medium border-b border-gray-100"></div>

                <!-- CARTE GOOGLE MAPS (iframe intégrée, aucune clé API requise) -->
                <iframe id="gmap-frame"
                    src="https://www.google.com/maps?q=18.722538,-72.192672&hl=fr&z=16&output=embed"
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    allowfullscreen
                    title="Carte — Quincaillerie de la Liberté, Terre Blanche, Haïti"></iframe>

            </div><!-- /map-card -->

        </div>
    </section>

    <!-- ============================================================
         FOOTER
    ============================================================ -->
    <footer class="bg-navy-950 border-t border-white/10 text-gray-400 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <p>© <?= date('Y') ?> Quincaillerie de la Liberté. Tous droits réservés.</p>
            <a href="<?= BASE_URL ?>/login" class="text-orange-400 hover:text-orange-300 font-medium">Espace employé →</a>
        </div>
    </footer>

    <script>
        /* ============================================================
           GOOGLE MAPS — Itinéraire
           Coordonnées du magasin : Terre Blanche, Haïti
        ============================================================ */
        const STORE_LAT = 18.722538;
        const STORE_LNG = -72.192672;

        // Lien "Ouvrir dans Google Maps" (fiche du magasin)
        document.getElementById('gmaps-view-link').href =
            `https://www.google.com/maps/search/?api=1&query=${STORE_LAT},${STORE_LNG}`;

        function setStatus(msg, type) {
            const el = document.getElementById('map-status');
            el.className = 'px-6 py-3 text-sm font-medium border-b border-gray-100 ' +
                (type === 'error' ? 'bg-red-50 text-red-600' :
                    type === 'success' ? 'bg-green-50 text-green-700' :
                    'bg-blue-50 text-blue-700');
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        function hideStatus() {
            document.getElementById('map-status').classList.add('hidden');
        }

        function setBtnLoading(loading) {
            const iconNav = document.getElementById('btn-icon-nav');
            const iconSpin = document.getElementById('btn-icon-spin');
            const label = document.getElementById('btn-label');
            document.getElementById('map-route-btn').disabled = loading;
            iconNav.classList.toggle('hidden', loading);
            iconSpin.classList.toggle('hidden', !loading);
            label.textContent = loading ? 'Localisation en cours…' : "Obtenir l'itinéraire";
        }

        function openItinerary() {
            setBtnLoading(true);
            hideStatus();

            // Fallback si la géolocalisation n'est pas disponible
            if (!navigator.geolocation) {
                setBtnLoading(false);
                setStatus("Géolocalisation non supportée : itinéraire ouvert sans point de départ.", 'error');
                window.open(`https://www.google.com/maps/dir/?api=1&destination=${STORE_LAT},${STORE_LNG}`, '_blank');
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (pos) => {
                    const {
                        latitude,
                        longitude
                    } = pos.coords;
                    setBtnLoading(false);
                    setStatus("Itinéraire ouvert dans un nouvel onglet ✅", 'success');
                    window.open(
                        `https://www.google.com/maps/dir/?api=1&origin=${latitude},${longitude}&destination=${STORE_LAT},${STORE_LNG}`,
                        '_blank'
                    );
                },
                (err) => {
                    setBtnLoading(false);
                    let msg = "Impossible d'obtenir votre position.";
                    if (err.code === err.PERMISSION_DENIED) msg = "Permission de localisation refusée.";
                    else if (err.code === err.TIMEOUT) msg = "Délai dépassé pour obtenir votre position.";
                    setStatus(msg + " Itinéraire ouvert sans point de départ.", 'error');
                    window.open(
                        `https://www.google.com/maps/dir/?api=1&destination=${STORE_LAT},${STORE_LNG}`,
                        '_blank'
                    );
                }, {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 60000
                }
            );
        }

        /* ============================================================
           MENU MOBILE
        ============================================================ */
        (function() {
            const menuToggle = document.getElementById('menu-toggle');
            const mobileMenu = document.getElementById('mobile-menu');
            const iconOpen = document.getElementById('icon-open');
            const iconClose = document.getElementById('icon-close');
            if (!menuToggle || !mobileMenu) return;

            function closeMenu() {
                mobileMenu.classList.add('hidden');
                iconOpen.classList.remove('hidden');
                iconClose.classList.add('hidden');
            }

            menuToggle.addEventListener('click', () => {
                const isHidden = mobileMenu.classList.toggle('hidden');
                iconOpen.classList.toggle('hidden', !isHidden);
                iconClose.classList.toggle('hidden', isHidden);
            });

            // Ferme le menu après clic sur un lien
            mobileMenu.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', closeMenu);
            });

            // Ferme le menu si clic en dehors
            document.addEventListener('click', (e) => {
                if (!mobileMenu.classList.contains('hidden') &&
                    !mobileMenu.contains(e.target) &&
                    !menuToggle.contains(e.target)) {
                    closeMenu();
                }
            });
        })();
    </script>

</body>

</html>