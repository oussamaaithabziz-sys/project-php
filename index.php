<?php 
require_once 'includes/header.php'; 
?>

<div class="flex justify-between items-center bg-tertiary-container dark:bg-emerald-950/40 text-on-tertiary-container dark:text-emerald-400 p-4 rounded-lg mb-stack-lg border border-tertiary dark:border-emerald-800 shadow-sm transition-all duration-300" id="welcome-alert">
    <div class="flex items-center gap-3">
        <span class="material-symbols-outlined" data-icon="info">info</span>
        <p class="font-body-md">
            👋 Bonjour, <?= htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?> ! Bienvenue sur Mon site. Vos préférences sont mémorisées grâce aux cookies. 
            <a class="underline font-bold hover:text-tertiary-fixed dark:hover:text-emerald-300 transition-colors" href="pages/profil.php">Gérer mes préférences →</a>
        </p>
    </div>
    <button class="p-1 hover:bg-white/20 dark:hover:bg-white/10 rounded-full transition-all" onclick="document.getElementById('welcome-alert').style.display='none'">
        <span class="material-symbols-outlined" data-icon="close">close</span>
    </button>
</div>

<section class="bg-surface-container-low dark:bg-[#1a1a1a] rounded-xl border border-outline-variant dark:border-gray-800 p-10 mb-stack-lg hero-gradient relative overflow-hidden transition-colors duration-300">
    <div class="relative z-10 max-w-2xl">
        <h1 class="font-headline-xl text-headline-xl text-on-background dark:text-white mb-4">Bienvenue dans mon magasin !</h1>
        <p class="font-headline-md text-headline-md text-tertiary dark:text-emerald-400 mb-6">Nous sommes ravis de vous accueillir dans notre boutique en ligne.</p>
        <p class="font-body-lg text-body-lg text-on-surface-variant dark:text-gray-400 mb-8 leading-relaxed">
            Découvrez une sélection exclusive de produits haut de gamme, conçus pour allier durabilité et élégance. Notre engagement pour la qualité premium vous garantit une expérience d'achat exceptionnelle, centrée sur vos besoins et le respect de l'environnement.
        </p>
        <a href="pages/menu.php" class="inline-block bg-on-secondary-fixed dark:bg-[#0057cd] text-on-primary px-8 py-4 rounded-lg font-label-md hover:bg-primary-container dark:hover:bg-blue-600 transition-all shadow-md active:scale-95 no-underline">
            Voir notre catalogue
        </a>
    </div>
    <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-primary/5 dark:bg-blue-500/5 rounded-full blur-3xl"></div>
</section>

<section class="grid grid-cols-1 md:grid-cols-3 gap-gutter mt-stack-md mb-section-padding">
    
    <div class="bg-surface-container-lowest dark:bg-[#1e1e1e] p-8 rounded-lg border border-outline-variant dark:border-gray-800 shadow-sm hover:shadow-md dark:hover:border-gray-700 transition-all group">
        <div class="w-12 h-12 bg-primary-container dark:bg-blue-900/50 text-on-primary-container dark:text-blue-300 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined" data-icon="eco">eco</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-background dark:text-white mb-3">Commerce Équitable</h3>
        <p class="text-on-surface-variant dark:text-gray-400 font-label-md mb-4 uppercase tracking-wider text-xs">Durabilité &amp; Éthique</p>
        <p class="text-body-md text-on-surface-variant dark:text-gray-400">
            Chaque produit de notre catalogue est sélectionné pour son faible impact environnemental et ses conditions de production éthiques.
        </p>
    </div>

    <div class="bg-surface-container-lowest dark:bg-[#1e1e1e] p-8 rounded-lg border border-outline-variant dark:border-gray-800 shadow-sm hover:shadow-md dark:hover:border-gray-700 transition-all group">
        <div class="w-12 h-12 bg-tertiary-container dark:bg-emerald-950/50 text-on-tertiary-container dark:text-emerald-400 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined" data-icon="verified">verified</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-background dark:text-white mb-3">Qualité Certifiée</h3>
        <p class="text-on-surface-variant dark:text-gray-400 font-label-md mb-4 uppercase tracking-wider text-xs">Standard Premium</p>
        <p class="text-body-md text-on-surface-variant dark:text-gray-400">
            Nous collaborons avec les meilleurs artisans pour vous offrir des articles robustes qui traversent le temps sans perdre de leur superbe.
        </p>
    </div>

    <div class="bg-surface-container-lowest dark:bg-[#1e1e1e] p-8 rounded-lg border border-outline-variant dark:border-gray-800 shadow-sm hover:shadow-md dark:hover:border-gray-700 transition-all group">
        <div class="w-12 h-12 bg-on-secondary-fixed dark:bg-gray-800 text-on-primary dark:text-gray-200 rounded-lg flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined" data-icon="local_shipping">local_shipping</span>
        </div>
        <h3 class="font-headline-md text-headline-md text-on-background dark:text-white mb-3">Livraison Neutre</h3>
        <p class="text-on-surface-variant dark:text-gray-400 font-label-md mb-4 uppercase tracking-wider text-xs">Logistique Moderne</p>
        <p class="text-body-md text-on-surface-variant dark:text-gray-400">
            Notre système de logistique optimisé réduit les émissions de CO2 tout en garantissant une réception rapide à votre domicile.
        </p>
    </div>
    
</section>

<?php 
require_once 'includes/footer.php'; 
?>