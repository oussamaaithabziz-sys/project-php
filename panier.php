<?php
// ============================================================
// TOUT LE PHP AVANT TOUT AFFICHAGE HTML
// ============================================================
if (session_status() === PHP_SESSION_NONE) session_start();

// --- Actions sur le panier ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Supprimer un article
    if (isset($_POST['remove_item'])) {
        $id = $_POST['remove_item'];
        unset($_SESSION['panier'][$id]);
        header("Location: panier.php");
        exit();
    }

    // Modifier la quantité
    if (isset($_POST['update_qty'])) {
        $id  = $_POST['update_qty'];
        $qty = max(1, (int)($_POST['qty_' . $id] ?? 1));
        if (isset($_SESSION['panier'][$id])) {
            $_SESSION['panier'][$id]['qty'] = $qty;
        }
        header("Location: panier.php");
        exit();
    }

    // Vider le panier
    if (isset($_POST['vider_panier'])) {
        $_SESSION['panier'] = [];
        header("Location: panier.php");
        exit();
    }

    // Valider le paiement
    if (isset($_POST['valider_paiement'])) {
        $prenom_client = htmlspecialchars($_POST['prenom_client'] ?? 'Client');
        $moyen_paiement = $_POST['moyen_paiement'] ?? 'carte';
        $_SESSION['commande_confirmee'] = [
            'prenom'  => $prenom_client,
            'moyen'   => $moyen_paiement,
            'total'   => $_POST['total_a_payer'] ?? '0',
            'date'    => date('d/m/Y à H:i'),
            'ref'     => strtoupper(substr(md5(uniqid()), 0, 8))
        ];
        $_SESSION['panier'] = [];
        header("Location: panier.php?commande=ok");
        exit();
    }
}

// --- Coupon ---
$coupon_code       = trim($_GET['coupon'] ?? $_SESSION['coupon'] ?? '');
$discount_pct      = 0;
$coupon_msg        = '';
$coupon_type       = ''; // success | error

$valid_coupons = ['BEST30'=>30,'CODE25'=>25,'WIN20'=>20,'TRY15'=>15,'LAST10'=>10];
if (!empty($coupon_code)) {
    $cup = strtoupper($coupon_code);
    if (array_key_exists($cup, $valid_coupons)) {
        $discount_pct    = $valid_coupons[$cup];
        $coupon_msg      = "Coupon « $cup » appliqué — $discount_pct% de réduction !";
        $coupon_type     = 'success';
        $_SESSION['coupon'] = $coupon_code;
    } else {
        $coupon_msg  = "Code coupon invalide.";
        $coupon_type = 'error';
        unset($_SESSION['coupon']);
    }
} else {
    unset($_SESSION['coupon']);
}

// --- Calcul du total ---
$panier      = $_SESSION['panier'] ?? [];
$total_brut  = 0;
foreach ($panier as $item) {
    $total_brut += $item['prix'] * $item['qty'];
}
$discount_amt  = $total_brut * ($discount_pct / 100);
$total_a_payer = $total_brut - $discount_amt;
$frais_livraison = $total_a_payer > 500 ? 0 : 9.99;
$total_final   = $total_a_payer + $frais_livraison;

// --- Chargement du header commun ---
require_once '../includes/header.php';
?>

<!-- CONFIRMATION DE COMMANDE -->
<?php if (isset($_GET['commande']) && $_GET['commande'] === 'ok' && isset($_SESSION['commande_confirmee'])): 
    $cmd = $_SESSION['commande_confirmee'];
    unset($_SESSION['commande_confirmee']);
?>
<div class="max-w-lg mx-auto my-12 text-center">
    <div class="bg-white dark:bg-[#1e1e1e] rounded-2xl border border-green-200 dark:border-green-800 shadow-lg p-10">
        <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
            <span class="material-symbols-outlined text-green-600 dark:text-green-400" style="font-size:48px;">check_circle</span>
        </div>
        <h1 class="text-2xl font-bold text-green-700 dark:text-green-400 mb-2">Commande confirmée !</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Merci <strong><?= htmlspecialchars($cmd['prenom']) ?></strong>, votre commande a bien été enregistrée.</p>
        <div class="bg-gray-50 dark:bg-[#252525] rounded-xl p-5 text-left space-y-2 text-sm mb-6">
            <div class="flex justify-between"><span class="text-gray-500">Référence</span><strong class="text-primary font-mono">#<?= $cmd['ref'] ?></strong></div>
            <div class="flex justify-between"><span class="text-gray-500">Montant</span><strong><?= number_format((float)$cmd['total'], 2, ',', ' ') ?> €</strong></div>
            <div class="flex justify-between"><span class="text-gray-500">Paiement</span><strong><?= htmlspecialchars(ucfirst($cmd['moyen'])) ?></strong></div>
            <div class="flex justify-between"><span class="text-gray-500">Date</span><span><?= $cmd['date'] ?></span></div>
        </div>
        <a href="../index.php" class="inline-block bg-primary text-white px-8 py-3 rounded-lg font-semibold hover:bg-blue-700 transition no-underline">
            ← Retour à l'accueil
        </a>
    </div>
</div>

<?php else: ?>

<div class="mb-6">
    <h1 class="text-3xl font-bold text-[#191c1d] dark:text-white mb-1">🛒 Mon Panier</h1>
    <p class="text-gray-500 dark:text-gray-400">
        <?= count($panier) === 0 ? 'Votre panier est vide.' : count($panier) . ' article(s) dans votre panier' ?>
    </p>
</div>

<?php if (empty($panier)): ?>
<!-- PANIER VIDE -->
<div class="flex flex-col items-center justify-center py-24 text-center">
    <span class="material-symbols-outlined text-gray-300 dark:text-gray-700 mb-6" style="font-size:96px;">shopping_cart</span>
    <h2 class="text-xl font-semibold text-gray-500 dark:text-gray-400 mb-4">Votre panier est vide</h2>
    <a href="menu.php" class="bg-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition no-underline">
        Voir le catalogue →
    </a>
</div>

<?php else: ?>
<!-- PANIER AVEC ARTICLES -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    <!-- LISTE DES ARTICLES -->
    <div class="lg:col-span-8 space-y-4">

        <?php foreach ($panier as $id => $item): ?>
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-5 flex items-center gap-5 group hover:border-primary/40 transition-all">
            
            <!-- Icône produit -->
            <div class="w-16 h-16 bg-blue-50 dark:bg-blue-950/30 rounded-xl flex items-center justify-center flex-shrink-0">
                <span class="material-symbols-outlined text-primary dark:text-blue-400" style="font-size:36px;"><?= htmlspecialchars($item['icon'] ?? 'inventory_2') ?></span>
            </div>

            <!-- Infos -->
            <div class="flex-grow min-w-0">
                <h3 class="font-bold text-gray-900 dark:text-white truncate"><?= htmlspecialchars($item['titre']) ?></h3>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">Prix unitaire : <strong class="text-primary"><?= number_format($item['prix'], 2, ',', ' ') ?> €</strong></p>
            </div>

            <!-- Quantité + total -->
            <div class="flex items-center gap-4 flex-shrink-0">
                <form method="POST" class="flex items-center gap-2">
                    <input type="hidden" name="update_qty" value="<?= $id ?>">
                    <label class="text-xs text-gray-400 sr-only">Qté</label>
                    <select name="qty_<?= $id ?>" onchange="this.form.submit()"
                        class="border border-gray-200 dark:border-gray-700 rounded-lg px-2 py-1.5 text-sm bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none">
                        <?php for ($q = 1; $q <= 10; $q++): ?>
                            <option value="<?= $q ?>" <?= $item['qty'] == $q ? 'selected' : '' ?>><?= $q ?></option>
                        <?php endfor; ?>
                    </select>
                </form>

                <div class="text-right min-w-[80px]">
                    <div class="font-bold text-lg text-primary"><?= number_format($item['prix'] * $item['qty'], 2, ',', ' ') ?> €</div>
                </div>

                <form method="POST">
                    <input type="hidden" name="remove_item" value="<?= $id ?>">
                    <button type="submit" class="p-2 rounded-full text-gray-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/30 transition-all" title="Supprimer">
                        <span class="material-symbols-outlined" style="font-size:20px;">delete</span>
                    </button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Vider panier -->
        <form method="POST" class="flex justify-end">
            <button type="submit" name="vider_panier"
                onclick="return confirm('Vider tout le panier ?')"
                class="text-sm text-red-500 hover:text-red-700 hover:underline flex items-center gap-1 transition">
                <span class="material-symbols-outlined" style="font-size:16px;">delete_sweep</span> Vider le panier
            </button>
        </form>

        <!-- Coupon -->
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-gray-200 dark:border-gray-800 p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary" style="font-size:20px;">local_offer</span>
                Code de réduction
            </h3>
            <form method="GET" class="flex gap-3">
                <input type="text" name="coupon"
                    value="<?= htmlspecialchars($coupon_code) ?>"
                    placeholder="Ex: BEST30, WIN20..."
                    class="flex-grow px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm">
                <button type="submit" class="px-5 py-2.5 bg-gray-700 dark:bg-gray-600 text-white rounded-lg text-sm font-semibold hover:bg-gray-800 transition">
                    Appliquer
                </button>
            </form>
            <?php if ($coupon_msg): ?>
                <p class="mt-2 text-sm font-semibold <?= $coupon_type === 'success' ? 'text-green-600' : 'text-red-500' ?>">
                    <?= $coupon_type === 'success' ? '✅' : '❌' ?> <?= htmlspecialchars($coupon_msg) ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- FORMULAIRE PAIEMENT -->
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm p-6" id="section-paiement">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-5 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">credit_card</span>
                Informations de paiement
            </h2>

            <form method="POST" class="space-y-5">
                <input type="hidden" name="valider_paiement" value="1">
                <input type="hidden" name="total_a_payer" value="<?= number_format($total_final, 2, '.', '') ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Prénom</label>
                        <input type="text" name="prenom_client" required placeholder="Votre prénom"
                            value="<?= isset($_SESSION['user']['prenom']) ? htmlspecialchars($_SESSION['user']['prenom']) : '' ?>"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Nom</label>
                        <input type="text" name="nom_client" placeholder="Votre nom"
                            value="<?= isset($_SESSION['user']['nom']) ? htmlspecialchars($_SESSION['user']['nom']) : '' ?>"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Email</label>
                    <input type="email" name="email_client" placeholder="votre@email.com"
                        value="<?= isset($_SESSION['user']['email']) ? htmlspecialchars($_SESSION['user']['email']) : '' ?>"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Moyen de paiement</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="moyen_paiement" value="carte" class="sr-only peer" checked>
                            <div class="flex flex-col items-center p-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all">
                                <span class="material-symbols-outlined text-gray-400 peer-checked:text-primary mb-1" style="font-size:28px;">credit_card</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Carte</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="moyen_paiement" value="paypal" class="sr-only peer">
                            <div class="flex flex-col items-center p-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all">
                                <span class="material-symbols-outlined text-gray-400 mb-1" style="font-size:28px;">account_balance_wallet</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">PayPal</span>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="moyen_paiement" value="virement" class="sr-only peer">
                            <div class="flex flex-col items-center p-3 border-2 border-gray-200 dark:border-gray-700 rounded-xl peer-checked:border-primary peer-checked:bg-blue-50 dark:peer-checked:bg-blue-950/30 transition-all">
                                <span class="material-symbols-outlined text-gray-400 mb-1" style="font-size:28px;">account_balance</span>
                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Virement</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Champs carte (affichés uniquement si carte sélectionnée) -->
                <div id="champs-carte" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Numéro de carte</label>
                        <input type="text" name="num_carte" placeholder="0000 0000 0000 0000" maxlength="19"
                            oninput="formatCard(this)"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Expiration</label>
                            <input type="text" name="expiration" placeholder="MM/AA" maxlength="5"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">CVV</label>
                            <input type="password" name="cvv" placeholder="•••" maxlength="3"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#2a2a2a] text-gray-800 dark:text-white focus:ring-2 focus:ring-primary outline-none text-sm font-mono">
                        </div>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-primary hover:bg-blue-700 text-white font-bold rounded-xl flex items-center justify-center gap-2 transition-all active:scale-[0.98] shadow-md mt-2">
                    <span class="material-symbols-outlined">lock</span>
                    Confirmer et payer — <?= number_format($total_final, 2, ',', ' ') ?> €
                </button>

                <p class="text-xs text-center text-gray-400 flex items-center justify-center gap-1 mt-1">
                    <span class="material-symbols-outlined" style="font-size:14px;">shield</span>
                    Paiement 100% sécurisé — Vos données sont protégées
                </p>
            </form>
        </div>
    </div>

    <!-- RÉCAPITULATIF STICKY -->
    <aside class="lg:col-span-4 sticky top-24">
        <div class="bg-white dark:bg-[#1e1e1e] rounded-xl border border-gray-200 dark:border-gray-800 shadow-sm overflow-hidden">
            <div class="bg-[#181c20] dark:bg-[#252525] p-4 text-white">
                <h3 class="font-bold text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined" style="font-size:20px;">receipt_long</span>
                    Récapitulatif
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <?php foreach ($panier as $item): ?>
                <div class="flex items-center gap-3 py-2 border-b border-gray-100 dark:border-gray-800 last:border-0">
                    <span class="material-symbols-outlined text-primary" style="font-size:20px;"><?= htmlspecialchars($item['icon'] ?? 'inventory_2') ?></span>
                    <div class="flex-grow min-w-0">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200 truncate"><?= htmlspecialchars($item['titre']) ?></p>
                        <p class="text-xs text-gray-400">x<?= $item['qty'] ?></p>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white flex-shrink-0">
                        <?= number_format($item['prix'] * $item['qty'], 2, ',', ' ') ?> €
                    </span>
                </div>
                <?php endforeach; ?>

                <div class="pt-2 space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Sous-total</span>
                        <span class="font-semibold text-gray-800 dark:text-white"><?= number_format($total_brut, 2, ',', ' ') ?> €</span>
                    </div>
                    <?php if ($discount_pct > 0): ?>
                    <div class="flex justify-between text-green-600 dark:text-green-400">
                        <span>Réduction (<?= $discount_pct ?>%)</span>
                        <span class="font-semibold">-<?= number_format($discount_amt, 2, ',', ' ') ?> €</span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between text-gray-500 dark:text-gray-400">
                        <span>Livraison</span>
                        <span class="font-semibold <?= $frais_livraison == 0 ? 'text-green-600' : '' ?>">
                            <?= $frais_livraison == 0 ? 'Gratuite 🎉' : number_format($frais_livraison, 2, ',', ' ') . ' €' ?>
                        </span>
                    </div>
                    <?php if ($frais_livraison > 0): ?>
                    <p class="text-xs text-gray-400 italic">Livraison gratuite dès 500 €</p>
                    <?php endif; ?>

                    <div class="flex justify-between items-center pt-3 border-t border-dashed border-gray-200 dark:border-gray-700">
                        <span class="font-bold text-gray-800 dark:text-white">Total</span>
                        <span class="text-2xl font-bold text-primary"><?= number_format($total_final, 2, ',', ' ') ?> €</span>
                    </div>
                </div>

                <a href="#section-paiement"
                    class="block w-full py-3 bg-primary hover:bg-blue-700 text-white text-center font-bold rounded-xl transition no-underline mt-2">
                    💳 Passer au paiement
                </a>
                <a href="menu.php" class="block text-center text-sm text-primary hover:underline mt-1 no-underline">← Continuer mes achats</a>
            </div>
        </div>
    </aside>
</div>

<?php endif; // fin panier non vide ?>
<?php endif; // fin commande ok ?>

<script>
function formatCard(input) {
    let val = input.value.replace(/\D/g, '').substring(0, 16);
    input.value = val.replace(/(.{4})/g, '$1 ').trim();
}

// Afficher/masquer les champs carte selon le moyen sélectionné
document.querySelectorAll('input[name="moyen_paiement"]').forEach(radio => {
    radio.addEventListener('change', () => {
        const champsEl = document.getElementById('champs-carte');
        if (champsEl) {
            champsEl.style.display = radio.value === 'carte' ? 'block' : 'none';
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
