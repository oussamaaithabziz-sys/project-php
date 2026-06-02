<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// قراءة الإعدادات من الكوكيز أو السيسيون (باش يقرا السمية من الفورم الجديد والقديم)
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

$prenom = '';
if (isset($_SESSION['user']['prenom'])) {
    $prenom = $_SESSION['user']['prenom'];
} elseif (isset($_COOKIE['prenom'])) {
    $prenom = $_COOKIE['prenom'];
} elseif (isset($_COOKIE['user_prenom'])) {
    $prenom = $_COOKIE['user_prenom'];
}

// تحديد المسار التلقائي على حسب موقع الملف الحالي (برا أو لداخل ف pages)
$is_subfolder = (basename(dirname($_SERVER['PHP_SELF'])) === 'pages');
$base_path = $is_subfolder ? '../' : '';
$pages_path = $is_subfolder ? '' : 'pages/';
?>
<!DOCTYPE html>
<html class="<?= htmlspecialchars($theme, ENT_QUOTES, 'UTF-8') ?>" lang="fr">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Mon Magasin</title>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

<script>
    function getCookie(name) {
        let match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
        if (match) return match[2];
        return 'light';
    }
    if (getCookie('theme') === 'dark') {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
</script>

<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class", 
        theme: {
          extend: {
            "colors": {
                    "primary-fixed-dim": "#b1c5ff",
                    "surface-container-low": "#f3f4f5",
                    "secondary": "#5b5f63",
                    "on-primary-container": "#ffffff",
                    "inverse-primary": "#b1c5ff",
                    "tertiary": "#006c40",
                    "tertiary-fixed-dim": "#77da9f",
                    "surface-container-lowest": "#ffffff",
                    "secondary-fixed": "#e0e3e8",
                    "surface-container": "#edeeef",
                    "on-primary": "#ffffff",
                    "surface-dim": "#d9dadb",
                    "on-secondary": "#ffffff",
                    "surface-bright": "#f8f9fa",
                    "on-tertiary-fixed": "#002110",
                    "outline-variant": "#c2c6d8",
                    "on-secondary-fixed": "#181c20",
                    "on-secondary-container": "#5f6368",
                    "on-primary-fixed": "#001946",
                    "primary-fixed": "#dae2ff",
                    "inverse-on-surface": "#f0f1f2",
                    "error": "#ba1a1a",
                    "on-surface-variant": "#424655",
                    "on-tertiary": "#ffffff",
                    "surface-variant": "#e1e3e4",
                    "tertiary-fixed": "#93f7ba",
                    "background": "#f8f9fa",
                    "primary": "#0057cd",
                    "surface-tint": "#0057ce",
                    "secondary-fixed-dim": "#c3c7cc",
                    "secondary-container": "#dde0e5",
                    "outline": "#727787",
                    "on-background": "#191c1d",
                    "surface": "#f8f9fa",
                    "primary-container": "#0d6efd",
                    "on-tertiary-fixed-variant": "#00522f",
                    "on-primary-fixed-variant": "#00419e",
                    "inverse-surface": "#2e3132",
                    "tertiary-container": "#198754",
                    "on-secondary-fixed-variant": "#43474c",
                    "surface-container-high": "#e7e8e9",
                    "surface-container-highest": "#e1e3e4",
                    "on-error-container": "#93000a",
                    "on-surface": "#191c1d",
                    "on-tertiary-container": "#ffffff",
                    "error-container": "#ffdad6",
                    "on-error": "#ffffff"
            },
            "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
            "spacing": { "stack-sm": "0.5rem", "container-max": "1320px", "stack-lg": "2rem", "stack-md": "1rem", "section-padding": "4rem", "gutter": "1.5rem" },
            "fontFamily": {
                    "body-md": ["Inter"], "body-lg": ["Inter"], "headline-lg-mobile": ["Inter"], "headline-lg": ["Inter"],
                    "label-md": ["Inter"], "nav-link-active": ["Inter"], "headline-md": ["Inter"], "headline-xl": ["Inter"]
            },
            "fontSize": {
                    "body-md": ["16px", {"lineHeight": "1.5"}], "body-lg": ["18px", {"lineHeight": "1.6"}],
                    "headline-lg": ["32px", {"lineHeight": "1.25"}], "label-md": ["14px", {"lineHeight": "1"}],
                    "headline-md": ["24px", {"lineHeight": "1.4"}]
            }
          },
        },
      }
    </script>
<style>
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; display: inline-block; vertical-align: middle; }
        .nav-shadow { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    </style>
</head>
<body class="bg-background text-on-background dark:bg-inverse-surface dark:text-inverse-on-surface font-body-md min-h-screen flex flex-col">
<header class="sticky top-0 z-50 bg-on-secondary-fixed dark:bg-on-background shadow-sm w-full">
<nav class="flex justify-between items-center w-full px-gutter max-w-container-max mx-auto h-16">
<div class="flex items-center gap-8">
<span class="font-headline-md text-headline-md font-bold text-on-primary">
    <a href="<?= $base_path ?>index.php" class="text-white no-underline">🏪 Mon Magasin</a>
</span>
<div class="hidden md:flex gap-6 items-center">
<a class="text-secondary-fixed-dim hover:text-on-primary-container no-underline" href="<?= $base_path ?>index.php">Accueil</a>
<a class="text-secondary-fixed-dim hover:text-on-primary-container no-underline" href="<?= $pages_path ?>menu.php">Menu</a>
<a class="text-secondary-fixed-dim hover:text-on-primary-container no-underline" href="<?= $pages_path ?>newsletter.php">Newsletter</a>
<a class="text-secondary-fixed-dim hover:text-on-primary-container no-underline" href="<?= $pages_path ?>contact.php">Contact</a>
<a class="text-secondary-fixed-dim hover:text-on-primary-container no-underline" href="<?= $pages_path ?>gagner.php">Gagner</a>
<a class="text-secondary-fixed-dim hover:text-on-primary-container no-underline" href="<?= $pages_path ?>panier.php">Panier</a>
</div>
</div>
<div class="flex items-center gap-4">

<?php if (!empty($prenom)): ?>
    <div class="relative inline-block">
        <a href="<?= $pages_path ?>compte.php" class="inline-flex items-center gap-1 text-tertiary-fixed font-label-md bg-white/5 px-3 py-1.5 rounded-lg no-underline hover:bg-white/10 transition-all">
            <span>👋 Bonjour, <strong><?= htmlspecialchars($prenom, ENT_QUOTES, 'UTF-8') ?></strong></span>
            <span class="material-symbols-outlined text-[16px]">arrow_drop_down</span>
        </a>
    </div>
<?php else: ?>
    <a href="<?= $pages_path ?>compte.php" class="text-secondary-fixed-dim hover:text-white text-sm flex items-center gap-1 no-underline border border-white/10 px-2 py-1 rounded">
        <span class="material-symbols-outlined">person</span> Me présenter
    </a>
<?php endif; ?>

<div class="flex gap-2 items-center">
    <a href="<?= $pages_path ?>panier.php" class="p-2 text-on-primary hover:bg-white/10 rounded-full flex items-center" title="Panier">
        <span class="material-symbols-outlined">shopping_cart</span>
    </a>
    <a href="<?= $pages_path ?>compte.php" class="p-2 text-on-primary hover:bg-white/10 rounded-full flex items-center" title="Mon Compte">
        <span class="material-symbols-outlined">person</span>
    </a>
</div>
</div>
</nav>
</header>

<?php if (basename($_SERVER['PHP_SELF']) === 'profil.php' && isset($_GET['updated'])): ?>
    <div class="max-w-container-max mx-auto px-gutter mt-4 space-y-2">
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span>Prénom « <strong><?= htmlspecialchars($prenom) ?></strong> » enregistré pour 1 an.</span>
        </div>
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span>Thème « <strong><?= htmlspecialchars($_COOKIE['theme'] ?? 'light') ?></strong> » enregistré.</span>
        </div>
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-lg flex items-center gap-2">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <span>Langue enregistrée pour 30 jours.</span>
        </div>
    </div>
<?php endif; ?>

<?php if (basename($_SERVER['PHP_SELF']) === 'index.php' && !empty($prenom)): ?>
    <div class="max-w-container-max mx-auto px-gutter mt-4">
        <div class="p-4 bg-green-50 border border-green-200 text-green-900 rounded-lg flex items-center gap-3">
            <span class="text-xl">👋</span>
            <div>
                <p class="font-bold">Bonjour, <?= htmlspecialchars($prenom) ?> ! Bienvenue sur votre magasin.</p>
                <p class="text-sm text-green-700">Vos preferences sont mémorisées. <a href="<?= $pages_path ?>compte.php" class="underline font-semibold">Modifier →</a></p>
            </div>
        </div>
    </div>
<?php endif; ?>



<main class="flex-grow container mx-auto max-w-container-max px-gutter py-stack-lg">

