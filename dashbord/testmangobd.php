<?php

use MongoDB\Client;

require __DIR__ . '/../vendor/autoload.php';
try {
    // Connexion à MongoDB
    $client = new Client("mongodb+srv://Cluster54037:bHBYVk59XFh4@cluster54037.zom2y.mongodb.net/?retryWrites=true&w=majority&appName=Cluster54037");

    // Lister les bases de données
    $databases = $client->listDatabases();
    echo "Connexion réussie ! Voici vos bases de données :<br>";
    foreach ($databases as $db) {
        echo $db->getName() . "<br>";
    }
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}