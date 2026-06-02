<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. قراءة الكتالوج من الملف الخارجي
$catalogue = [];
$catalog_file = __DIR__ . '/../data/catalogue.txt';

if (file_exists($catalog_file)) {
    $lines = file($catalog_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        $parts = explode('|', $line);
        if (count($parts) === 4) {
            $category = trim($parts[0]);
            $code = trim($parts[1]);
            $name = trim($parts[2]);
            $price = (float)trim($parts[3]);
            
            $catalogue[$category][$code] = [
                'name' => $name,
                'price' => $price
            ];
        }
    }
}

// 2. استقبال الاختيارات من الفورم (مع وضع قيم افتراضية لمنع الأخطاء)
$selected_cpu = $_POST['cpu'] ?? 'i5';
$selected_ram = $_POST['ram'] ?? [];
$selected_stockage = $_POST['stockage'] ?? [];
$selected_periph = $_POST['periph'] ?? [];
$selected_garantie = $_POST['garantie'] ?? 'standard';
$coupon_code = trim($_POST['coupon'] ?? '');

$receipt_items = [];
$total_brut = 0;

// حساب سعر المعالج المختار
if (isset($catalogue['cpu'][$selected_cpu])) {
    $item = $catalogue['cpu'][$selected_cpu];
    $receipt_items[] = ['cat' => 'Processeur', 'name' => $item['name'], 'price' => $item['price']];
    $total_brut += $item['price'];
}

// حساب الـ RAM المختارة
foreach ($selected_ram as $ram_code) {
    if (isset($catalogue['ram'][$ram_code])) {
        $item = $catalogue['ram'][$ram_code];
        $receipt_items[] = ['cat' => 'RAM', 'name' => $item['name'], 'price' => $item['price']];
        $total_brut += $item['price'];
    }
}

// حساب الـ Stockage المختار
foreach ($selected_stockage as $st_code) {
    if (isset($catalogue['stockage'][$st_code])) {
        $item = $catalogue['stockage'][$st_code];
        $receipt_items[] = ['cat' => 'Stockage', 'name' => $item['name'], 'price' => $item['price']];
        $total_brut += $item['price'];
    }
}

// حساب الأجهزة الطرفية المختارة
foreach ($selected_periph as $p_code) {
    if (isset($catalogue['periph'][$p_code])) {
        $item = $catalogue['periph'][$p_code];
        $receipt_items[] = ['cat' => 'Périphérique', 'name' => $item['name'], 'price' => $item['price']];
        $total_brut += $item['price'];
    }
}

// حساب الضمان المختار
if (isset($catalogue['garantie'][$selected_garantie])) {
    $item = $catalogue['garantie'][$selected_garantie];
    $receipt_items[] = ['cat' => 'Garantie', 'name' => $item['name'], 'price' => $item['price']];
    $total_brut += $item['price'];
}

// 3. منطق الكوبونات والخصم المكتسب من صفحة (gagner.php)
$discount_percentage = 0;
$coupon_error = "";
$coupon_success = "";

if (!empty($coupon_code)) {
    $valid_coupons = [
        'BEST30' => 30,
        'CODE25' => 25,
        'WIN20'  => 20,
        'TRY15'  => 15,
        'LAST10' => 10
    ];

    if (array_key_exists(strtoupper($coupon_code), $valid_coupons)) {
        $discount_percentage = $valid_coupons[strtoupper($coupon_code)];
        $coupon_success = "Coupon appliqué (-" . $discount_percentage . "%) !";
    } else {
        $coupon_error = "Code coupon invalide.";
    }
}

$discount_amount = $total_brut * ($discount_percentage / 100);
$total_a_payer = $total_brut - $discount_amount;

// ✨ عيطنا للهيدر الموحد باش يتكلف بالـ Navbar الديناميكية والـ Dark Mode تلقائياً
require_once '../includes/header.php';
?>

    <header class="mb-8">
        <h1 class="text-3xl font-bold text-[#191c1d] dark:text-white mb-2">Configurateur de Panier</h1>
        <p class="text-gray-600 dark:text-gray-400">Personnalisez votre station de travail avant de finaliser votre commande.</p>
    </header>

    <form method="POST" action="panier.php" id="cart-form">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <div class="lg:col-span-8 space-y-6">
                
                <section class="bg-white dark:bg-[#1e1e1e] p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 transition-colors">
                    <h2 class="text-xl font-bold text-[#191c1d] dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0057cd] dark:text-[#b1c5ff]">computer</span> Processeurs
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach (($catalogue['cpu'] ?? []) as $code => $item): ?>
                            <label class="flex items-center p-4 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-[#252525] transition-all <?= $selected_cpu === $code ? 'border-[#0057cd] dark:border-[#b1c5ff] bg-blue-50/50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' ?>">
                                <input class="text-[#0057cd] dark:text-[#b1c5ff] focus:ring-[#0057cd] h-5 w-5 mr-3 bg-white dark:bg-[#2a2a2a] border-gray-300 dark:border-gray-600" name="cpu" type="radio" value="<?= $code ?>" <?= $selected_cpu === $code ? 'checked' : '' ?> onchange="this.form.submit();"/>
                                <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="ml-auto font-bold text-[#0057cd] dark:text-[#b1c5ff] text-sm"><?= number_format($item['price'], 2) ?> €</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="bg-white dark:bg-[#1e1e1e] p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 transition-colors">
                    <h2 class="text-xl font-bold text-[#191c1d] dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0057cd] dark:text-[#b1c5ff]">memory</span> RAM & Stockage
                    </h2>
                    <div class="space-y-3">
                        <?php foreach (($catalogue['ram'] ?? []) as $code => $item): ?>
                            <label class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-[#252525] rounded-lg transition-colors cursor-pointer">
                                <input class="rounded border-gray-300 dark:border-gray-600 text-[#0057cd] dark:text-[#b1c5ff] focus:ring-[#0057cd] bg-white dark:bg-[#2a2a2a] h-5 w-5 mr-4" name="ram[]" type="checkbox" value="<?= $code ?>" <?= in_array($code, $selected_ram) ? 'checked' : '' ?> onchange="this.form.submit();"/>
                                <span class="flex-grow text-gray-700 dark:text-gray-300 text-sm"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="font-bold text-gray-600 dark:text-gray-400 text-sm">+<?= number_format($item['price'], 2) ?> €</span>
                            </label>
                        <?php endforeach; ?>

                        <?php foreach (($catalogue['stockage'] ?? []) as $code => $item): ?>
                            <label class="flex items-center p-3 hover:bg-gray-50 dark:hover:bg-[#252525] rounded-lg transition-colors cursor-pointer">
                                <input class="rounded border-gray-300 dark:border-gray-600 text-[#0057cd] dark:text-[#b1c5ff] focus:ring-[#0057cd] bg-white dark:bg-[#2a2a2a] h-5 w-5 mr-4" name="stockage[]" type="checkbox" value="<?= $code ?>" <?= in_array($code, $selected_stockage) ? 'checked' : '' ?> onchange="this.form.submit();"/>
                                <span class="flex-grow text-gray-700 dark:text-gray-300 text-sm"><?= htmlspecialchars($item['name']) ?></span>
                                <span class="font-bold text-gray-600 dark:text-gray-400 text-sm">+<?= number_format($item['price'], 2) ?> €</span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="bg-white dark:bg-[#1e1e1e] p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 transition-colors">
                    <h2 class="text-xl font-bold text-[#191c1d] dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0057cd] dark:text-[#b1c5ff]">mouse</span> Périphériques
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <?php 
                        $icons = ['clavier' => 'keyboard', 'souris' => 'mouse', 'casque' => 'headphones'];
                        foreach (($catalogue['periph'] ?? []) as $code => $item): 
                            $is_selected = in_array($code, $selected_periph);
                        ?>
                            <label class="p-4 border rounded-lg flex flex-col items-center text-center group cursor-pointer transition-all <?= $is_selected ? 'border-[#0057cd] dark:border-[#b1c5ff] bg-blue-50/30 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-[#0057cd] dark:hover:border-[#b1c5ff]' ?>">
                                <div class="w-14 h-14 mb-2 bg-gray-100 dark:bg-[#2a2a2a] rounded-full flex items-center justify-center transition-colors">
                                    <span class="material-symbols-outlined text-gray-600 dark:text-gray-300" style="font-size: 28px;"><?= $icons[$code] ?? 'devices' ?></span>
                                </div>
                                <input class="sr-only" name="periph[]" type="checkbox" value="<?= $code ?>" <?= $is_selected ? 'checked' : '' ?> onchange="this.form.submit();"/>
                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200"><?= htmlspecialchars($item['name']) ?></p>
                                <p class="text-[#0057cd] dark:text-[#b1c5ff] font-bold text-xs mt-1">+<?= number_format($item['price'], 2) ?> €</p>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <section class="bg-white dark:bg-[#1e1e1e] p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-800 transition-colors">
                    <h2 class="text-xl font-bold text-[#191c1d] dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#0057cd] dark:text-[#b1c5ff]">verified_user</span> Garantie
                    </h2>
                    <div class="space-y-3">
                        <?php foreach (($catalogue['garantie'] ?? []) as $code => $item): ?>
                            <label class="flex items-center justify-between p-4 border rounded-lg cursor-pointer transition-all <?= $selected_garantie === $code ? 'border-l-4 border-green-600 dark:border-l-4 dark:border-green-400 bg-gray-50 dark:bg-[#252525]' : 'border-gray-200 dark:border-gray-700 opacity-80' ?>">
                                <div class="flex items-center">
                                    <input class="text-[#0057cd] dark:text-[#b1c5ff] focus:ring-[#0057cd] h-5 w-5 mr-4 bg-white dark:bg-[#2a2a2a] border-gray-300 dark:border-gray-600" name="garantie" type="radio" value="<?= $code ?>" <?= $selected_garantie === $code ? 'checked' : '' ?> onchange="this.form.submit();"/>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200 text-sm"><?= htmlspecialchars($item['name']) ?></span>
                                </div>
                                <span class="<?= $item['price'] == 0 ? 'text-green-600 dark:text-green-400' : 'text-[#0057cd] dark:text-[#b1c5ff]' ?> font-bold text-sm">
                                    <?= $item['price'] == 0 ? 'Gratuit' : '+' . number_format($item['price'], 2) . ' €' ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </section>

                <div class="flex flex-col gap-2 pt-2">
                    <div class="flex gap-4">
                        <input class="flex-grow px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-[#1e1e1e] text-gray-800 dark:text-white focus:ring-2 focus:ring-[#0057cd] dark:focus:ring-[#b1c5ff] focus:border-[#0057cd] dark:focus:border-[#b1c5ff] outline-none text-sm" id="coupon" name="coupon" placeholder="Code de réduction (Ex: BEST30)" type="text" value="<?= htmlspecialchars($coupon_code) ?>"/>
                        <button type="submit" class="px-6 py-3 bg-gray-600 dark:bg-gray-700 text-white rounded-lg font-semibold hover:bg-gray-700 dark:hover:bg-gray-600 text-sm transition-colors">Appliquer</button>
                    </div>
                    <?php if (!empty($coupon_error)): ?>
                        <p class="text-red-600 dark:text-red-400 text-xs font-bold">❌ <?= $coupon_error ?></p>
                    <?php endif; ?>
                    <?php if (!empty($coupon_success)): ?>
                        <p class="text-green-600 dark:text-green-400 text-xs font-bold">✅ <?= $coupon_success ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="lg:col-span-4 sticky top-24 w-full">
                <div class="bg-white dark:bg-[#1e1e1e] rounded-xl shadow-md border border-gray-200 dark:border-gray-800 overflow-hidden transition-colors">
                    <div class="bg-[#181c20] dark:bg-[#252525] p-4 text-white">
                        <h3 class="text-lg font-bold">Récapitulatif de votre panier</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <table class="w-full text-left font-medium text-xs">
                            <thead class="border-b border-gray-200 dark:border-gray-700 text-gray-400 dark:text-gray-500 uppercase tracking-wider">
                                <tr>
                                    <th class="pb-2">Catégorie</th>
                                    <th class="pb-2">Sélection</th>
                                    <th class="pb-2 text-right">Prix</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-gray-700 dark:text-gray-300">
                                <?php foreach ($receipt_items as $ri): ?>
                                    <tr>
                                        <td class="py-3 font-semibold text-gray-400 dark:text-gray-500"><?= htmlspecialchars($ri['cat']) ?></td>
                                        <td class="py-3 max-w-[140px] truncate text-gray-800 dark:text-gray-200" title="<?= htmlspecialchars($ri['name']) ?>"><?= htmlspecialchars($ri['name']) ?></td>
                                        <td class="py-3 text-right font-bold text-gray-900 dark:text-white"><?= number_format($ri['price'], 2) ?> €</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <div class="space-y-2 pt-4 border-t-2 border-dashed border-gray-200 dark:border-gray-700 text-sm">
                            <div class="flex justify-between items-center text-gray-500 dark:text-gray-400">
                                <span>Total brut</span>
                                <span class="font-bold text-gray-800 dark:text-white"><?= number_format($total_brut, 2) ?> €</span>
                            </div>
                            
                            <?php if ($discount_percentage > 0): ?>
                                <div class="flex justify-between items-center text-green-600 dark:text-green-400">
                                    <span>Remise (<?= $discount_percentage ?>%)</span>
                                    <span class="font-bold">-<?= number_format($discount_amount, 2) ?> €</span>
                                </div>
                            <?php endif; ?>

                            <div class="flex justify-between items-center py-2 bg-blue-50/50 dark:bg-blue-950/20 px-3 rounded-lg mt-2">
                                <span class="font-bold text-gray-800 dark:text-gray-200">Total à payer</span>
                                <span class="text-xl font-bold text-[#0057cd] dark:text-[#b1c5ff]"><?= number_format($total_a_payer, 2) ?> €</span>
                            </div>
                        </div>

                        <button type="button" onclick="alert('Commande validée pour <?= number_format($total_a_payer, 2) ?> € !')" class="w-full py-3.5 bg-[#0057cd] dark:bg-[#b1c5ff] text-white dark:text-[#121212] rounded-lg font-bold flex items-center justify-center gap-2 hover:bg-blue-700 dark:hover:bg-[#92aeff] transition-all">
                            <span>💳</span> Procéder au paiement
                        </button>
                    </div>
                </div>
            </aside>
            
        </div>
    </form>
</main>

<footer class="bg-gray-100 dark:bg-[#1a1a1a] border-t border-gray-200 dark:border-gray-800 mt-auto py-6 transition-colors">
    <div class="text-center text-xs text-gray-500 dark:text-gray-400">
        © 2026 Mon site - cours de php 2026
    </div>
</footer>

</body>
</html>