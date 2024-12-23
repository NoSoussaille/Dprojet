<?php
require_once 'session_check.php';
if (!isset($_SESSION['username'])) {
    // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
    header("Location: " . BASE_URL . "connexion.php");
    exit();
}
// Définis le titre en fonction du rôle
$title = "Dashboard"; // Titre par défaut
if ($_SESSION['role'] === 'administrateur') {
    $title = "Espace Administrateur";
} elseif ($_SESSION['role'] === 'vétérinaire') {
    $title = "Espace Vétérinaire";
} elseif ($_SESSION['role'] === 'employé') {
    $title = "Espace Employé";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styledash.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
<header>
    <h1><a href="dashbord.php" class="href">ARCADIA</a></h1>
    <button class="burger" id="burger-menu">&#9776;</button>
    <nav id="nav-menu">
        <ul class="main-nav">
            <?php if ($_SESSION['role'] === 'administrateur'): ?>
                <li><a href="#">Gestion du Parc</a>
                    <ul class="submenu">
                        <li><a href="ganimaux.php">Gestion animaux</a></li>
                        <li><a href="ghabitats.php">Gestion habitats</a></li>
                        <li><a href="gservices.php">Gestion des services</a></li>
                        <li><a href="gemploye.php">Gestion des Employés</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'vétérinaire' || $_SESSION['role'] === 'administrateur'): ?>
                <li><a href="#">Soin des animaux</a>
                    <ul class="submenu">
                        <li><a href="veto.php">Mise a jour du suivie</a></li>
                        <li><a href="antecedent.php">Antécédent de suivie</a></li>
                    </ul>
                </li>
            <?php endif; ?>

            <?php if ($_SESSION['role'] === 'employé' || $_SESSION['role'] === 'administrateur'): ?>
                <li><a href="gestion_avis.php">Gestion des avis</a></li>
            <?php endif; ?>

            <li><a href="logout.php">Deconnexion</a></li>
        </ul>
    </nav>
</header>
<script>const burgerMenu = document.getElementById('burger-menu');
const navMenu = document.getElementById('nav-menu');
const menuItems = document.querySelectorAll('.menu-item');

// Ajouter un événement pour ouvrir/fermer le menu principal
burgerMenu.addEventListener('click', () => {
    const mainNav = navMenu.querySelector('.main-nav');
    mainNav.classList.toggle('active'); // Afficher/masquer le menu
});</script>
