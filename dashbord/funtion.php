<?php
try {
    // Connexion avec les informations de ta base de données
    $pdo = new PDO('mysql:host=127.0.0.1;port=8889;dbname=zoo_arcadia', 'root', 'root');
    
    $search = 'L%';
    // Requête SQL avec une jointure pour récupérer les informations des animaux et des habitats
    $query = '
        SELECT animaux.prenom, animaux.race, habitats.nom AS habitat_nom 
        FROM animaux 
        JOIN habitats ON animaux.habitat_id = habitats.id
        WHERE animaux.race LIKE ?';
    $pdoStatement= $pdo -> prepare($query);
    $pdoStatement -> bindValue(1, $search, PDO::PARAM_STR);

    $pdoStatement -> execute();
    // Exécution de la requête
    foreach ($pdo->query($query, PDO::FETCH_ASSOC) as $animal) {
        echo $animal['prenom'] . ' - ' . $animal['race'] .' => '. $animal['habitat_nom'] . '<br>';
    }
} catch (PDOException $e) {
    echo 'Impossible de récupérer la liste des animaux : ' . $e->getMessage();
}

