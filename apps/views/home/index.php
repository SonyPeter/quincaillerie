<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quincaillerie de la Liberté</title>
    <link href="<?= BASE_URL ?>/public/css/output.css?v=<?= filemtime(BASE_PATH . '/public/css/output.css') ?>" rel="stylesheet">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #map-section { scroll-margin-top: 64px; }
        #store-map { height: 480px; width: 100%; border-radius: 0 0 1rem 1rem; z-index: 0; }
        .leaflet-routing-container { display: none !important; }
        #map-route-btn {
            transition: all 0.25s ease;
        }
        #map-route-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(249,115,22,0.4);
        }
        #map-route-btn:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        .map-card {
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }
        .map-pulse {
            animation: pulse-ring 2s ease-out infinite;
        }
        @keyframes pulse-ring {
            0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(249,115,22,0.5); }
            70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(249,115,22,0); }
            100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(249,115,22,0); }
        }
    </style>
</head>

<body class="bg-white text-gray-800">

    <!-- Navbar -->
    <header class="relative bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="<?= BASE_URL ?>/" class="flex items-center gap-2 font-bold text-lg text-gray-900">
                    <img src="<?= BASE_URL ?>/public/images/logo.png" alt="Quincaillerie de la Liberté" class="w-14 h-14 object-contain">
                    Quincaillerie de la Liberté
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
                    <svg id="icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile menu : petit panneau flottant, pas pleine page -->
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

    <!-- Hero -->
    <section id="accueil" class="relative overflow-hidden bg-navy-950 text-white">
        <canvas data-beams-background data-intensity="medium" class="absolute inset-0" style="filter: blur(15px);"></canvas>
        <div class="absolute inset-0 bg-navy-950/30"></div>

        <div class="relative z-10 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 grid md:grid-cols-2 gap-10 items-center">
            <div>
                <p class="text-orange-400 font-semibold mb-3">Depuis 2010</p>
                <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mb-5">
                    Tout pour la construction, <span class="text-orange-500">au même endroit</span>
                </h1>
                <p class="text-gray-300 text-lg mb-8">
                    Matériaux, outillage, plomberie, électricité, peinture... la Quincaillerie de la Liberté
                    accompagne particuliers et professionnels avec des produits de qualité et des prix justes.
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="#produits"
                        class="bg-orange-500 hover:bg-orange-600 text-white font-semibold px-6 py-3 rounded-lg transition">
                        Découvrir nos produits
                    </a>
                    <a href="#contact"
                        class="border border-gray-500 hover:border-white text-white font-semibold px-6 py-3 rounded-lg transition">
                        Nous contacter
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-navy-900 rounded-xl p-6">
                    <p class="text-3xl font-bold text-orange-500">+500</p>
                    <p class="text-gray-400 text-sm mt-1">Références produits</p>
                </div>
                <div class="bg-navy-900 rounded-xl p-6">
                    <p class="text-3xl font-bold text-orange-500">15+</p>
                    <p class="text-gray-400 text-sm mt-1">Années d'expérience</p>
                </div>
                <div class="bg-navy-900 rounded-xl p-6">
                    <p class="text-3xl font-bold text-orange-500">1000+</p>
                    <p class="text-gray-400 text-sm mt-1">Clients satisfaits</p>
                </div>
                <div class="bg-navy-900 rounded-xl p-6">
                    <p class="text-3xl font-bold text-orange-500">6j/7</p>
                    <p class="text-gray-400 text-sm mt-1">Ouvert toute la semaine</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Produits -->
    <section id="produits" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Nos catégories de produits</h2>
                <p class="text-gray-500">Un large choix de matériaux et d'équipements pour tous vos projets.</p>
            </div>
            <?php if (empty($categories)): ?>
                <p class="text-center text-gray-400">Les catégories de produits seront affichées ici prochainement.</p>
            <?php else: ?>
                <?php $icones = ['layers', 'wrench', 'droplet', 'zap', 'package', 'truck', 'receipt', 'wallet']; ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($categories as $i => $cat): ?>
                        <div class="border border-gray-200 rounded-xl p-6 hover:shadow-lg transition">
                            <div class="w-12 h-12 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center mb-4">
                                <?= DashboardPage::icon($icones[$i % count($icones)], 'w-6 h-6') ?>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1"><?= htmlspecialchars($cat['name']) ?></h3>
                            <p class="text-sm text-gray-500">
                                <?= !empty($cat['description'])
                                    ? htmlspecialchars($cat['description'])
                                    : $cat['product_count'] . ' produit' . ($cat['product_count'] > 1 ? 's' : '') . ' disponible' . ($cat['product_count'] > 1 ? 's' : '') ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- À propos -->
    <section id="apropos" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Pourquoi choisir notre quincaillerie ?</h2>
                <p class="text-gray-600 mb-6">
                    Nous mettons un point d'honneur à offrir des produits de qualité, des conseils adaptés
                    à chaque projet, et un service rapide, que vous soyez particulier ou professionnel du bâtiment.
                </p>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 font-bold">✔</span>
                        <span class="text-gray-700">Produits de qualité, prix compétitifs</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 font-bold">✔</span>
                        <span class="text-gray-700">Équipe disponible et à l'écoute</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-orange-500 font-bold">✔</span>
                        <span class="text-gray-700">Large stock disponible en permanence</span>
                    </li>
                </ul>
            </div>
            <div class="bg-orange-500 rounded-2xl h-72 md:h-80"></div>
        </div>
    </section>

    <!-- Contact -->
    <section id="contact" class="py-20 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-8 text-center md:text-left">
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Adresse</h3>
                <p class="text-gray-500">Avenue de la Liberté, Quartier Commercial<br>Ville, Pays</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Téléphone</h3>
                <p class="text-gray-500">+XXX XX XXX XXXX</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 mb-2">Horaires</h3>
                <p class="text-gray-500">Lun - Sam : 7h30 - 18h00<br>Dimanche : fermé</p>
            </div>
        </div>
    </section>

    <!-- Section Carte / Itinéraire -->
    <section id="map-section" class="py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- En-tête -->
            <div class="text-center max-w-2xl mx-auto mb-12">
                <div class="inline-flex items-center gap-2 bg-orange-100 text-orange-600 text-sm font-semibold px-4 py-1.5 rounded-full mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Nous trouver facilement
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-3">Venez nous rendre visite</h2>
                <p class="text-gray-500">Cliquez sur le bouton pour obtenir un itinéraire depuis votre position actuelle jusqu'à notre quincaillerie.</p>
            </div>

            <!-- Carte Card -->
            <div class="map-card rounded-2xl overflow-hidden border border-gray-200 bg-white">

                <!-- Bandeau supérieur -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 px-6 py-5 border-b border-gray-100">
                    <div class="flex items-center gap-4">
                        <!-- Icone pulsante -->
                        <div class="relative flex-shrink-0">
                            <div class="map-pulse w-12 h-12 rounded-full bg-orange-500 flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-2 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Quincaillerie de la Liberté</h3>
                            <p class="text-sm text-gray-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/></svg>
                                Terre Blanche, Haïti
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <button id="map-route-btn"
                            onclick="getItinerary()"
                            class="flex items-center justify-center gap-2 bg-orange-500 hover:bg-orange-600 text-white font-semibold px-5 py-2.5 rounded-xl w-full sm:w-auto">
                            <svg id="btn-icon-nav" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                            <svg id="btn-icon-spin" class="w-5 h-5 hidden animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span id="btn-label">Obtenir l'itinéraire</span>
                        </button>
                        <button id="map-reset-btn"
                            onclick="resetMap()"
                            title="Réinitialiser"
                            class="hidden flex-shrink-0 p-2.5 rounded-xl border border-gray-200 hover:bg-gray-100 text-gray-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Message d'état -->
                <div id="map-status" class="hidden px-6 py-3 text-sm font-medium border-b border-gray-100"></div>

                <!-- Carte -->
                <div id="store-map"></div>

                <!-- Infos itinéraire -->
                <div id="route-info" class="hidden flex items-center justify-around gap-4 px-6 py-4 bg-orange-50 border-t border-orange-100 flex-wrap">
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                        <span class="text-gray-500">Distance :</span>
                        <span id="route-distance" class="font-bold text-gray-900">—</span>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span class="text-gray-500">Durée estimée :</span>
                        <span id="route-duration" class="font-bold text-gray-900">—</span>
                    </div>
                    <a id="route-gmaps-link" href="#" target="_blank" rel="noopener"
                        class="flex items-center gap-1.5 text-sm font-semibold text-orange-600 hover:text-orange-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                        Ouvrir dans Google Maps
                    </a>
                </div>

            </div><!-- /map-card -->

        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-navy-950 border-t border-white/10 text-gray-400 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p>© <?= date('Y') ?> Quincaillerie de la Liberté. Tous droits réservés.</p>
            <a href="<?= BASE_URL ?>/login" class="text-orange-400 hover:text-orange-300 font-medium">Espace employé →</a>
        </div>
    </footer>

    <script src="<?= BASE_URL ?>/public/js/beams-background.js"></script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <!-- Leaflet Routing Machine -->
    <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

    <script>
        /* ============================================================
           Carte & Itinéraire – Quincaillerie de la Liberté
        ============================================================ */
        const STORE_LAT  = 18.722538;
        const STORE_LNG  = -72.192672;
        const STORE_NAME = 'Quincaillerie de la Liberté';

        // Initialisation de la carte
        const map = L.map('store-map', { zoomControl: true }).setView([STORE_LAT, STORE_LNG], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);

        // Marqueur de la quincaillerie
        const storeIcon = L.divIcon({
            className: '',
            html: `<div style="
                width:44px;height:44px;
                background:#f97316;
                border-radius:50% 50% 50% 0;
                transform:rotate(-45deg);
                border:4px solid #fff;
                box-shadow:0 4px 16px rgba(249,115,22,.5);
                display:flex;align-items:center;justify-content:center;
            "><span style="transform:rotate(45deg);font-size:18px;">🔧</span></div>`,
            iconSize: [44, 44],
            iconAnchor: [22, 44]
        });

        const storeMarker = L.marker([STORE_LAT, STORE_LNG], { icon: storeIcon })
            .addTo(map)
            .bindPopup(`
                <div style="font-family:sans-serif;min-width:180px">
                    <strong style="color:#f97316;font-size:14px">${STORE_NAME}</strong><br>
                    <span style="color:#6b7280;font-size:12px">Terre Blanche, Haïti</span><br><br>
                    <span style="font-size:12px">🕐 Lun–Sam : 7h30 – 18h00</span>
                </div>
            `, { offset: [0, -40] })
            .openPopup();

        let routingControl = null;
        let userMarker     = null;

        function setStatus(msg, type) {
            const el = document.getElementById('map-status');
            el.className = 'px-6 py-3 text-sm font-medium border-b border-gray-100 ' +
                (type === 'error'   ? 'bg-red-50 text-red-600' :
                 type === 'success' ? 'bg-green-50 text-green-700' :
                                     'bg-blue-50 text-blue-700');
            el.textContent = msg;
            el.classList.remove('hidden');
        }

        function setBtnLoading(loading) {
            const btn     = document.getElementById('map-route-btn');
            const iconNav = document.getElementById('btn-icon-nav');
            const iconSpin= document.getElementById('btn-icon-spin');
            const label   = document.getElementById('btn-label');
            btn.disabled  = loading;
            iconNav.classList.toggle('hidden', loading);
            iconSpin.classList.toggle('hidden', !loading);
            label.textContent = loading ? 'Localisation en cours…' : 'Obtenir l\'itinéraire';
        }

        function formatDistance(meters) {
            return meters >= 1000
                ? (meters / 1000).toFixed(1) + ' km'
                : Math.round(meters) + ' m';
        }

        function formatDuration(seconds) {
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            if (h > 0) return `${h}h ${m}min`;
            return `${m} min`;
        }

        function getItinerary() {
            if (!navigator.geolocation) {
                setStatus('⚠️ La géolocalisation n\'est pas supportée par votre navigateur.', 'error');
                return;
            }
            setBtnLoading(true);
            document.getElementById('map-status').classList.add('hidden');

            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    const userLat = pos.coords.latitude;
                    const userLng = pos.coords.longitude;

                    setBtnLoading(false);

                    // Supprimer l'ancien itinéraire si existant
                    if (routingControl) { map.removeControl(routingControl); routingControl = null; }
                    if (userMarker)     { map.removeLayer(userMarker);       userMarker = null; }

                    // Marqueur position utilisateur
                    const userIcon = L.divIcon({
                        className: '',
                        html: `<div style="
                            width:20px;height:20px;
                            background:#3b82f6;
                            border-radius:50%;
                            border:4px solid #fff;
                            box-shadow:0 0 0 3px rgba(59,130,246,.4);
                        "></div>`,
                        iconSize: [20, 20],
                        iconAnchor: [10, 10]
                    });
                    userMarker = L.marker([userLat, userLng], { icon: userIcon })
                        .addTo(map)
                        .bindPopup('<strong>📍 Votre position</strong>')
                        .openPopup();

                    // Tracé de l'itinéraire via OSRM (gratuit)
                    routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(userLat, userLng),
                            L.latLng(STORE_LAT, STORE_LNG)
                        ],
                        routeWhileDragging: false,
                        show: false,
                        addWaypoints: false,
                        fitSelectedRoutes: true,
                        lineOptions: {
                            styles: [{ color: '#f97316', weight: 5, opacity: 0.85 }]
                        },
                        createMarker: function() { return null; } // on utilise nos propres marqueurs
                    }).addTo(map);

                    routingControl.on('routesfound', function(e) {
                        const route = e.routes[0].summary;
                        document.getElementById('route-distance').textContent = formatDistance(route.totalDistance);
                        document.getElementById('route-duration').textContent = formatDuration(route.totalTime);
                        document.getElementById('route-gmaps-link').href =
                            `https://www.google.com/maps/dir/?api=1&origin=${userLat},${userLng}&destination=${STORE_LAT},${STORE_LNG}&travelmode=driving`;
                        document.getElementById('route-info').classList.remove('hidden');
                        document.getElementById('map-reset-btn').classList.remove('hidden');
                        setStatus('✅ Itinéraire trouvé ! Suivez le trajet orange sur la carte.', 'success');
                    });

                    routingControl.on('routingerror', function() {
                        setStatus('❌ Impossible de calculer l\'itinéraire. Essayez Google Maps.', 'error');
                    });
                },
                function(err) {
                    setBtnLoading(false);
                    const msgs = {
                        1: '🔒 Accès à la position refusé. Autorisez la géolocalisation dans votre navigateur.',
                        2: '📡 Position introuvable. Vérifiez votre connexion GPS.',
                        3: '⏱️ Délai dépassé lors de la localisation. Réessayez.'
                    };
                    setStatus(msgs[err.code] || '⚠️ Erreur de géolocalisation.', 'error');
                },
                { timeout: 10000, enableHighAccuracy: true }
            );
        }

        function resetMap() {
            if (routingControl) { map.removeControl(routingControl); routingControl = null; }
            if (userMarker)     { map.removeLayer(userMarker);       userMarker = null; }
            document.getElementById('route-info').classList.add('hidden');
            document.getElementById('map-status').classList.add('hidden');
            document.getElementById('map-reset-btn').classList.add('hidden');
            map.setView([STORE_LAT, STORE_LNG], 16);
            storeMarker.openPopup();
        }
    </script>

    <script>
        const toggleBtn = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('icon-open');
        const iconClose = document.getElementById('icon-close');

        function closeMobileMenu() {
            mobileMenu.classList.add('hidden');
            iconOpen.classList.remove('hidden');
            iconClose.classList.add('hidden');
        }

        toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            mobileMenu.classList.toggle('hidden');
            iconOpen.classList.toggle('hidden');
            iconClose.classList.toggle('hidden');
        });

        mobileMenu.querySelectorAll('a').forEach(link => link.addEventListener('click', closeMobileMenu));

        document.addEventListener('click', (e) => {
            if (!mobileMenu.classList.contains('hidden') && !mobileMenu.contains(e.target)) {
                closeMobileMenu();
            }
        });
    </script>

</body>

</html>
