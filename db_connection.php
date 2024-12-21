<?php 
define('BASE_URL', '/DProjet/');  // Modifiez selon le chemin racine de votre projet

// Configuration de la base de données
$db_host = 'etdq12exrvdjisg6.cbetxkdyhwsb.us-east-1.rds.amazonaws.com'; // Hôte
$db_user = 'n00lftk92t8tbpa5'; // Nom d'utilisateur
$db_password = 'gnuck26pqghpi4ya'; // Mot de passe
$db_name = 'zfvmq5bxcplaropm'; // Nom de la base de données
$db_port = 3306; // Port MySQL

// Connexion à la base de données
$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name, $db_port);

// Vérification de la connexion
if ($mysqli->connect_error) {
    die('Erreur de connexion (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
}

// Optionnel : pour vérifier que la connexion fonctionne (à désactiver en production)
// echo "Connexion réussie à la base de données !";
?>