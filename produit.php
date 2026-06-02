<?php
// ============================================================
// TOUT LE PHP DE TRAITEMENT DOIT ÊTRE ICI, AVANT TOUT HTML
// ============================================================
session_start();

// 1. Lire l'ID du produit depuis l'URL
$id_demande = isset($_GET['id']) ? trim($_GET['id']) : '1';
$product = null;
$fichier_csv = 'data/produits.csv';

if (file_exists($fichier_csv)) {
    if (($handle = fopen($fichier_csv, "r")) !== FALSE) {
        $headers = fgetcsv($handle, 1000, ",");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            if (count($data) >= 6 && trim($data[0]) === $id_demande) {
                $stock = (int)$data[4];
                if ($stock === 0) {
                    $badge_text  = "Rupture de stock";
                    $badge_class = "bg-error text-on-error";
                    $icon_badge  = "cancel";
                } elseif ($stock <= 5) {
                    $badge_text  = "Stock limité";
                    $badge_class = "bg-tertiary-fixed text-on-tertiary-fixed-variant";
                    $icon_badge  = "warning";
                } else {
                    $badge_text  = "En stock";
                    $badge_class = "bg-tertiary text-on-tertiary";
                    $icon_badge  = "check_circle";
                }
                $product = [
                    "id"          => $data[0],
                    "titre"       => $data[1],
                    "categorie"   => $data[2],
                    "prix_brut"   => (float)$data[3],
                    "prix"        => number_format((float)$data[3], 2, ',', ' ') . " €",
                    "stock"       => $stock,
                    "badge"       => $badge_text,
                    "badge_class" => $badge_class,
                    "icon_badge"  => $icon_badge,
                    "icon"        => $data[5]
                ];
                break;
            }
        }
        fclose($handle);
    }
}

// Produit par défaut si introuvable
if (!$product) {
    $product = [
        "id" => "1", "titre" => "MacBook Pro M3", "categorie" => "Informatique",
        "prix_brut" => 1299.99, "prix" => "1 299,99 €", "stock" => 3,
        "badge" => "En stock", "badge_class" => "bg-tertiary text-on-tertiary",
        "icon_badge" => "check_circle", "icon" => "laptop_mac"
    ];
}

// 2. Traitement du formulaire "Ajouter au panier"
//    DOIT être fait AVANT require header.php (qui envoie du HTML)
$added_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_panier'])) {
    $id  = trim($_POST['product_id'] ?? $product['id']);
    $qty = max(1, (int)($_POST['quantity'] ?? 1));

    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    if (isset($_SESSION['panier'][$id])) {
        $_SESSION['panier'][$id]['qty'] += $qty;
    } else {
        $_SESSION['panier'][$id] = [
            'id'    => $id,
            'titre' => $_POST['product_title'] ?? $product['titre'],
            'prix'  => (float)($_POST['product_price'] ?? $product['prix_brut']),
            'qty'   => $qty,
            'icon'  => $_POST['product_icon'] ?? $product['icon']
        ];
    }

    // Redirection AVANT tout affichage HTML
    header("Location: produit.php?id=" . urlencode($id) . "&success=1");
    exit();
}

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $added_success = true;
}

// 3. Maintenant seulement on charge le header HTML
require_once 'includes/header.php';
?>

<div class="flex-grow w-full max-w-container-max mx-auto px-gutter py-stack-md">

    <nav aria-label="Breadcrumb" class="flex items-center space-x-2 py-4 mb-stack-md">
        <a class="text-on-surface-variant font-label-md hover:text-primary transition-colors" href="index.php">Accueil</a>
        <span class="material-symbols-outlined text-outline" style="font-size:16px;">chevron_right</span>
        <a class="text-on-surface-variant font-label-md hover:text-primary transition-colors" href="pages/menu.php">Produits</a>
        <span class="material-symbols-outlined text-outline" style="font-size:16px;">chevron_right</span>
        <span class="text-primary font-label-md"><?= htmlspecialchars($product['titre']) ?></span>
    </nav>

    <?php if ($added_success): ?>
    <div class="flex justify-between items-center bg-green-100 dark:bg-green-950/40 text-green-800 dark:text-green-400 p-4 rounded-lg mb-6 border border-green-300 dark:border-green-800 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="material-symbols-outlined text-green-600">check_circle</span>
            <p class="font-body-md">🛒 Produit ajouté au panier avec succès !
                <a href="pages/panier.php" class="underline font-bold hover:text-green-700">Voir mon panier →</a>
            </p>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter">

        <!-- Image / icône produit -->
        <div class="lg:col-span-7 space-y-stack-md">
            <div class="bg-white rounded-lg border border-outline-variant overflow-hidden shadow-sm aspect-video flex items-center justify-center relative">
                <span class="material-symbols-outlined text-primary text-9xl"><?= htmlspecialchars($product['icon']) ?></span>
                <div class="absolute top-4 left-4">
                    <span class="<?= $product['badge_class'] ?> px-3 py-1 rounded-full font-label-md flex items-center gap-1">
                        <span class="material-symbols-outlined" style="font-size:14px;"><?= $product['icon_badge'] ?></span>
                        <?= htmlspecialchars($product['badge']) ?>
                    </span>
                </div>
            </div>
            <button onclick="window.location.href='pages/menu.php'"
                class="flex items-center gap-2 px-6 py-3 rounded-lg border border-outline text-on-surface hover:bg-surface-container transition-colors font-label-md group">
                <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
                Retour au catalogue
            </button>
        </div>

        <!-- Infos produit -->
        <div class="lg:col-span-5 space-y-stack-lg">
            <div class="space-y-stack-sm">
                <span class="text-primary font-label-md uppercase tracking-wider block"><?= htmlspecialchars($product['categorie']) ?></span>
                <h1 class="font-headline-lg text-headline-lg text-on-surface"><?= htmlspecialchars($product['titre']) ?></h1>
                <p class="text-on-surface-variant font-body-md">
                    Découvrez les performances exceptionnelles de notre produit de la catégorie
                    <?= htmlspecialchars($product['categorie']) ?>, conçu pour répondre à vos besoins au quotidien.
                </p>
            </div>

            <div class="bg-surface-container-low p-stack-md rounded-lg border border-outline-variant">
                <div class="text-primary font-headline-xl text-headline-xl"><?= htmlspecialchars($product['prix']) ?></div>
                <div class="text-outline text-label-md mt-1 italic">TVA incluse, livraison gratuite</div>
            </div>

            <form action="" method="POST" class="space-y-4 mt-4">
                <input type="hidden" name="product_id"    value="<?= htmlspecialchars($product['id']) ?>">
                <input type="hidden" name="product_title" value="<?= htmlspecialchars($product['titre']) ?>">
                <input type="hidden" name="product_price" value="<?= $product['prix_brut'] ?>">
                <input type="hidden" name="product_icon"  value="<?= htmlspecialchars($product['icon']) ?>">

                <?php if ($product['stock'] > 0): ?>
                    <div class="flex flex-col gap-2">
                        <label for="quantity" class="font-label-md text-sm font-semibold text-on-surface">Quantité :</label>
                        <select name="quantity" id="quantity"
                            class="rounded-lg border border-outline-variant bg-white dark:bg-gray-800 text-on-surface p-2 w-full max-w-[120px] focus:ring-2 focus:ring-primary">
                            <?php for ($i = 1; $i <= min(10, $product['stock']); $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="pt-4">
                        <div class="flex justify-between items-center mb-4 text-sm font-medium">
                            <span class="text-on-surface-variant">Total estimé :</span>
                            <span id="total-price" class="text-xl font-bold text-primary"><?= $product['prix'] ?></span>
                        </div>
                        <button type="submit" name="ajouter_panier"
                            class="w-full py-4 bg-primary text-white font-bold rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined">shopping_cart</span>
                            Ajouter au panier
                        </button>
                    </div>
                <?php else: ?>
                    <div class="p-4 bg-red-50 text-red-700 rounded-lg font-medium text-center border border-red-200">
                        🚫 Ce produit est actuellement en rupture de stock.
                    </div>
                <?php endif; ?>
            </form>

            <div class="border-t border-outline-variant pt-6 space-y-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary">local_shipping</span>
                    <div>
                        <h4 class="font-bold text-sm text-on-surface">Livraison rapide</h4>
                        <p class="text-xs text-on-surface-variant">Chez vous en 2 à 4 jours ouvrés.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-primary">shield</span>
                    <div>
                        <h4 class="font-bold text-sm text-on-surface">Garantie Premium</h4>
                        <p class="text-xs text-on-surface-variant">Garantie complète avec support technique disponible.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const qtySelect    = document.getElementById('quantity');
    const totalDiv     = document.getElementById('total-price');
    const unitPrice    = <?= $product['prix_brut'] ?>;

    if (qtySelect) {
        qtySelect.addEventListener('change', (e) => {
            const total = (unitPrice * parseInt(e.target.value)).toLocaleString('fr-FR', {
                style: 'currency', currency: 'EUR', minimumFractionDigits: 2
            });
            totalDiv.textContent = total;
        });
    }
</script>

<?php require_once 'includes/footer.php'; ?>
