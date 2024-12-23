<?php
session_start();
define('BASE_URL', '../');

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['username'])) {
    header("Location: " . BASE_URL . "connexion.php?error=session_expired");
    exit();
}

// Renouvelle l'ID de session après 30 minutes
if (!isset($_SESSION['session_created'])) {
    $_SESSION['session_created'] = time();
} elseif (time() - $_SESSION['session_created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['session_created'] = time();
}
?>