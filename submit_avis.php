<?php
// Connexion à la base de données
require_once 'db_connection.php';

// Vérifie que le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pseudo = $mysqli->real_escape_string($_POST['pseudo']);
    $avis = $mysqli->real_escape_string($_POST['avis']);

    // Prépare et exécute l'insertion dans la base de données
    $stmt = $mysqli->prepare("INSERT INTO avis_client (pseudo, avis) VALUES (?, ?)");
    $stmt->bind_param("ss", $pseudo, $avis);
    
    if ($stmt->execute()) {
        // Redirige vers la page d'accueil avec un message de confirmation
        header("Location: contact.php?message=avis_envoye");
        exit();
    } else {
        echo "Erreur : " . $stmt->error;
    }
    
    $stmt->close();
}

$mysqli->close();
?>