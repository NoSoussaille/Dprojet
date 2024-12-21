<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo isset($meta_description) ? $meta_description : 'Zoo Arcadia - Un parc écologique et immersif offrant une expérience unique au cœur de la nature.'; ?>">
    <title><?php echo isset($title) ? $title : 'ARCADIA'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <h1><a href="acceuil.php" class="href">ARCADIA</a></h1>
        <button class="burger" id="burger-menu">&#9776;</button>
        <nav id="nav-menu">
            <ul class="main-nav">
                <li><a href="#">Parc</a>
                    <ul class="submenu">
                        <li><a href="ecologie.php">Ecologie</a></li>
                        <li><a href="habitats.php">Habitats & Animaux</a></li>
                        <li><a href="horaire.php#tarif">Tarif</a></li>
                    </ul>
                </li>
                <li><a href="service.php">Nos Services</a>
                    <ul class="submenu">
                        <li><a href="service.php#le-petit-train">Le petit train</a></li>
                        <li><a href="service.php#visite-guidee">Visite guidée</a></li>
                        <li><a href="service.php#restauration">Restauration</a></li>
                    </ul>
                </li>
                <li><a href="contact.php">Contact</a>
                    <ul class="submenu">
                        <li><a href="contact.php#contact">Nous contacter</a></li>
                        <li><a href="contact.php#avis">Laisser un avis</a></li>
                    </ul>
                </li>
                <li><a href="connexion.php">Connexion</a></li>
            </ul>
        </nav> 
    </header>