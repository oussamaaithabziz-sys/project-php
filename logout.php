<?php
session_start();
session_destroy();

// Supprimer les cookies liés à l'utilisateur
setcookie('prenom',      '', time() - 3600, '/');
setcookie('user_prenom', '', time() - 3600, '/');

header("Location: login.php");
exit();
?>
