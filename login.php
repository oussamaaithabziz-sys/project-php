<?php
session_start();
require_once 'config.php';

// Si déjà connecté, rediriger
if (isset($_SESSION['user'])) {
    header("Location: pages/compte.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST["email"]    ?? '');
    $password = $_POST["password"] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {
        // Stocker la session sous forme de tableau (comme attendu par header.php)
        $_SESSION['user'] = [
            'id'     => $user['id'],
            'nom'    => $user['nom'],
            'prenom' => $user['prenom'],
            'email'  => $user['email'],
            'role'   => $user['role'] ?? 'Vendeur'
        ];

        // Cookie pour compatibilité avec header.php (1 an)
        setcookie('prenom', $user['prenom'], time() + 3600 * 24 * 365, '/');

        header("Location: pages/compte.php");
        exit();
    } else {
        $message = "Email ou mot de passe incorrect.";
    }
}
?>
<?php require_once 'includes/header.php'; ?>

<div class="max-w-md mx-auto mt-8 mb-16">
    <div class="bg-surface-container-lowest dark:bg-[#1e1e1e] rounded-xl border border-outline-variant dark:border-gray-800 shadow-sm p-8">
        <h2 class="font-headline-md text-headline-md text-on-background dark:text-white mb-6 text-center">
            Connexion
        </h2>

        <?php if ($message): ?>
            <div class="mb-4 p-4 rounded-lg text-sm bg-red-50 border border-red-200 text-red-700">
                <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-1">Email</label>
                <input type="email" name="email" required
                    value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                    placeholder="votre@email.com"
                    class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-800 dark:text-white dark:border-gray-700">
            </div>
            <div>
                <label class="block text-sm font-medium text-on-surface-variant mb-1">Mot de passe</label>
                <input type="password" name="password" required
                    placeholder="••••••••"
                    class="w-full px-4 py-2 border border-outline-variant rounded-lg focus:outline-none focus:ring-2 focus:ring-primary dark:bg-gray-800 dark:text-white dark:border-gray-700">
            </div>
            <button type="submit"
                class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:bg-primary-container transition-colors active:scale-95">
                Se connecter
            </button>
        </form>

        <p class="text-center text-sm text-on-surface-variant mt-6">
            Pas encore de compte ?
            <a href="register.php" class="text-primary font-semibold hover:underline">S'inscrire</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
