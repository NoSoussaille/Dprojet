<?php
require_once 'db_connection.php';

if (isset($_GET['animal_id'])) {
    $animal_id = (int)$_GET['animal_id'];

    // Incrémente le compteur de popularité
    $stmt = $mysqli->prepare("UPDATE animaux SET popularite = popularite + 1 WHERE id = ?");
    $stmt->bind_param("i", $animal_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "error" => $stmt->error]);
    }
    $stmt->close();
} else {
    echo json_encode(["success" => false, "error" => "ID de l'animal non fourni."]);
}
$mysqli->close();
?>