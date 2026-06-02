<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// جلب ملف الدوال الخاص بمشروعك
if (file_exists(__DIR__ . '/../includes/functions.php')) {
    require_once __DIR__ . '/../includes/functions.php';
}

if (!function_exists('e')) {
    function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}

// 1. توليد الرقم السري لأول مرة وتخزينه في الجلسة بأمان
if (!isset($_SESSION['secret_number'])) {
    $_SESSION['secret_number'] = rand(1, 10);
    $_SESSION['attempts'] = 0;
    $_SESSION['game_over'] = false;
}

$status_message = "";
$message_class = "text-on-surface-variant dark:text-gray-400";

// إعادة تشغيل اللعبة إذا ضغط على Recommencer
if (isset($_POST['reset_game'])) {
    $_SESSION['secret_number'] = rand(1, 10);
    $_SESSION['attempts'] = 0;
    $_SESSION['game_over'] = false;
    header("Location: gagner.php");
    exit();
}

// 2. معالجة التخمين المرسل من الفورم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['prediction']) && !$_SESSION['game_over']) {
    $prediction = trim($_POST['prediction']);
    
    if (is_numeric($prediction) && $prediction >= 1 && $prediction <= 10) {
        $_SESSION['attempts']++;
        $prediction = (int)$prediction;
        $secret = $_SESSION['secret_number'];

        if ($prediction < $secret) {
            $status_message = "Plus grand ! 📈";
            $message_class = "text-blue-600 dark:text-blue-400 font-bold animate-pulse";
        } elseif ($prediction > $secret) {
            $status_message = "Plus petit ! 📉";
            $message_class = "text-amber-600 dark:text-amber-400 font-bold animate-pulse";
        } else {
            // فاز المستخدم بالرقم الصحيح!
            $_SESSION['game_over'] = true;
            $att = $_SESSION['attempts'];

            // تحديد الكوبون والنسبة حسب عدد المحاولات المتوافقة مع الجدول
            if ($att <= 3) {
                $status_message = "🎉 BRAVO ! Vous avez trouvé en $att tentative(s). Code: <span class='underline'>BEST30</span> (-30%)";
            } elseif ($att <= 5) {
                $status_message = "🔥 GÉNIAL ! Vous avez trouvé en $att tentatives. Code: <span class='underline'>CODE25</span> (-25%)";
            } else {
                $status_message = "👍 Bien joué ! Vous avez trouvé en $att tentatives. Code: <span class='underline'>LAST10</span> (-10%)";
            }
            $message_class = "text-emerald-700 dark:text-emerald-400 font-bold text-lg";
        }
    } else {
        $status_message = "Veuillez entrer un nombre valide entre 1 et 10.";
        $message_class = "text-red-600 dark:text-red-400 font-bold";
    }
}

// جلب القيم الحالية للعرض بأمان لتفادي الـ Warnings
$current_attempts = isset($_SESSION['attempts']) ? $_SESSION['attempts'] : 0;

// تحديد "الكوبون الحالي" ديناميكياً ليظهر في الواجهة أثناء اللعب
$coupon_display = "-30% (BEST30)";
if ($current_attempts > 3 && $current_attempts <= 5) {
    $coupon_display = "-25% (CODE25)";
} elseif ($current_attempts > 5) {
    $coupon_display = "-10% (LAST10)";
}

// ✨ عيطنا للهيدر الموحد باش يتكلف بالـ Navbar الديناميكية والـ Dark Mode تلقائياً
require_once '../includes/header.php';
?>

<style>
    .slot-animation { animation: bounce 0.5s ease-in-out infinite alternate; }
    @keyframes bounce { from { transform: translateY(0); } to { transform: translateY(-10px); } }
</style>

<main class="flex-grow w-full max-w-container-max mx-auto px-gutter py-8">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        
        <section class="md:col-span-5 lg:col-span-4">
            <div class="bg-surface-container-lowest dark:bg-[#1e1e1e] border border-outline-variant dark:border-gray-800 rounded-lg overflow-hidden shadow-sm h-full flex flex-col transition-colors">
                <div class="bg-[#f2711c] px-4 py-3 flex items-center gap-2">
                    <span class="material-symbols-outlined text-white">military_tech</span>
                    <h2 class="text-white font-bold">Tableau des récompenses</h2>
                </div>
                
                <div class="p-4 space-y-4 flex-grow">
                    <div class="flex justify-between items-center pb-2 border-b border-outline-variant dark:border-gray-800 opacity-60 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <span>Tentatives</span>
                        <span>Code coupon</span>
                    </div>
                    
                    <div class="flex justify-between items-center p-2 rounded transition-colors <?= ($current_attempts <= 3 && $current_attempts > 0) ? 'bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-900' : 'hover:bg-gray-50 dark:hover:bg-[#2a2a2a]' ?>">
                        <span class="text-sm text-on-background dark:text-gray-300">1 - 3 tentatives</span>
                        <div class="flex gap-2">
                            <span class="bg-green-100 dark:bg-emerald-950/50 text-green-800 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold">-30%</span>
                            <span class="bg-gray-100 dark:bg-[#2a2a2a] text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full font-mono font-bold text-xs">BEST30</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-2 rounded transition-colors <?= ($current_attempts > 3 && $current_attempts <= 5) ? 'bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-900' : 'hover:bg-gray-50 dark:hover:bg-[#2a2a2a]' ?>">
                        <span class="text-sm text-on-background dark:text-gray-300">4 - 5 tentatives</span>
                        <div class="flex gap-2">
                            <span class="bg-green-100 dark:bg-emerald-950/50 text-green-800 dark:text-emerald-400 px-3 py-1 rounded-full text-xs font-bold">-25%</span>
                            <span class="bg-gray-100 dark:bg-[#2a2a2a] text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full font-mono font-bold text-xs">CODE25</span>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center p-2 rounded transition-colors <?= ($current_attempts > 5) ? 'bg-orange-50 dark:bg-orange-950/20 border border-orange-200 dark:border-orange-900' : 'hover:bg-gray-50 dark:hover:bg-[#2a2a2a]' ?>">
                        <span class="text-sm text-on-background dark:text-gray-300">5+ tentatives</span>
                        <div class="flex gap-2">
                            <span class="bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full text-xs font-bold">-10%</span>
                            <span class="bg-gray-100 dark:bg-[#2a2a2a] text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full font-mono font-bold text-xs">LAST10</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50 dark:bg-[#1a1a1a] border-t border-gray-100 dark:border-gray-800">
                    <p class="text-gray-500 dark:text-gray-400 text-xs">
                        * Devinez le nombre secret entre 1 et 10. Moins vous avez de tentatives, plus la réduction est grande !
                    </p>
                </div>
            </div>
        </section>

        <section class="md:col-span-7 lg:col-span-8">
            <div class="relative rounded-xl overflow-hidden bg-white dark:bg-[#1e1e1e] shadow-md border border-outline-variant dark:border-gray-800 flex flex-col items-center justify-center p-8 min-h-[500px] transition-colors">
                
                <div class="relative z-10 w-full max-w-md text-center space-y-6">
                    <div class="text-7xl mb-4 slot-animation select-none">🎰</div>
                    
                    <div class="space-y-2">
                        <h1 class="text-3xl font-bold text-blue-600 dark:text-[#b1c5ff]">Gagnez un coupon !</h1>
                        <div class="flex items-center justify-center gap-6 text-gray-600 dark:text-gray-400 text-sm">
                            <div>Tentatives : <span class="font-bold text-blue-600 dark:text-[#b1c5ff] text-xl" id="attempt-counter"><?= $current_attempts ?></span></div>
                            <div>Coupon actuel : <span class="font-bold text-gray-700 dark:text-gray-300 text-sm"><?= $coupon_display ?></span></div>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-[#2a2a2a] rounded-lg p-6 shadow-inner border border-gray-200 dark:border-gray-700 space-y-4 transition-colors">
                        <form method="POST" action="gagner.php" class="space-y-4">
                            <div class="flex flex-col gap-2">
                                <label class="text-left text-xs font-semibold text-gray-600 dark:text-gray-400 px-1" for="game-input">Votre proposition (1 à 10) :</label>
                                <input 
                                    class="w-full bg-white dark:bg-[#1e1e1e] border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-4 py-3 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all text-center text-2xl font-bold outline-none" 
                                    id="game-input" 
                                    name="prediction"
                                    placeholder="?" 
                                    type="number" 
                                    min="1" 
                                    max="10"
                                    required
                                    <?= (isset($_SESSION['game_over']) && $_SESSION['game_over']) ? 'disabled' : '' ?>
                                />
                            </div>
                            
                            <?php if (!isset($_SESSION['game_over']) || !$_SESSION['game_over']): ?>
                                <button class="w-full bg-blue-600 dark:bg-[#0057cd] text-white font-bold py-4 rounded-lg shadow-md hover:bg-blue-700 transition-all flex items-center justify-center gap-2 active:scale-[0.98]" type="submit">
                                    <span class="material-symbols-outlined">check_circle</span>
                                    Valider
                                </button>
                            <?php else: ?>
                                <button class="w-full bg-green-600 text-white font-bold py-4 rounded-lg shadow-md hover:bg-green-700 transition-all flex items-center justify-center gap-2 active:scale-[0.98]" name="reset_game" type="submit">
                                    <span class="material-symbols-outlined">refresh</span>
                                    Recommencer le jeu
                                </button>
                            <?php endif; ?>
                        </form>
                    </div>

                    <div class="h-6 text-sm transition-opacity <?= !empty($status_message) ? 'opacity-100' : 'opacity-0' ?> <?= $message_class ?>">
                        <?= $status_message ?>
                    </div>
                </div>

                <div class="absolute top-10 left-10 w-20 h-20 bg-blue-400 rounded-full blur-3xl opacity-10"></div>
                <div class="absolute bottom-10 right-10 w-32 h-32 bg-emerald-400 rounded-full blur-3xl opacity-10"></div>
            </div>
        </section>
    </div>
</main>

<footer class="bg-gray-100 dark:bg-[#1a1a1a] border-t border-gray-200 dark:border-gray-800 mt-auto transition-colors">
    <div class="flex flex-col items-center justify-center w-full py-6 text-center">
        <div class="text-sm font-bold text-blue-600 dark:text-[#b1c5ff] mb-2">Mon Magasin</div>
        <p class="text-gray-400 dark:text-gray-500 text-xs">© 2026 Mon site - cours de php 2026</p>
    </div>
</footer>

</body>
</html>