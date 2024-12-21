<?php
// Définir une URL de base pour les redirections
define('BASE_URL', '../'); // Assurez-vous que le chemin est correct pour votre projet
// Démarrage de la session
session_start();

// Détruit toutes les variables de session
$_SESSION = [];

// Détruit la session elle-même
session_unset();
session_destroy();

// Redirige l'utilisateur vers la page de connexion avec un message de déconnexion
header("Location: " . BASE_URL . "connexion.php?message=logout_success");
exit();