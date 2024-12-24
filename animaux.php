<?php
$title = "Nos Animaux - Zoo Arcadia";
$meta_description = "Les animaux d'Arcadia !";
require_once 'elements/header.php';
require_once 'db_connection.php';

if (isset($_GET['habitat_id'])) {
    $habitat_id = (int)$_GET['habitat_id'];

    // Récupérer les animaux de l'habitat
    $stmt = $mysqli->prepare("SELECT id, prenom, race, photo, popularite FROM animaux WHERE habitat_id = ?");
    $stmt->bind_param("i", $habitat_id);
    $stmt->execute();
    $result = $stmt->get_result();
?>

<div class="title beige">
    <h2 class="sous-menu vert">Animaux de l'habitat</h2>
</div>

<div class="mainpage5"> <!-- Nouveau conteneur principal -->
    <div class="mainpage5">
        <?php if ($result->num_rows > 0) {
            echo "<div class='animals'>";

            while ($animal = $result->fetch_assoc()) {
                $photo_path = !empty($animal['photo']) ? "dashbord/" . htmlspecialchars($animal['photo']) : "images/default_animal.jpg";
                echo "<div class='animal-card'>";
                echo "<img src='$photo_path' alt='Photo de " . htmlspecialchars($animal['prenom']) . "' class='animal-photo'>";
                echo "<h3>" . htmlspecialchars($animal['prenom']) . "</h3>";
                echo "<p>Race : " . htmlspecialchars($animal['race']) . "</p>";

                // Accordéon pour les détails vétérinaires
                echo "<button class='btn btn-primary' type='button' data-bs-toggle='collapse' data-bs-target='#detailsVet" . $animal['id'] . "' aria-expanded='false' aria-controls='detailsVet" . $animal['id'] . "' onclick='incrementPopularity(" . $animal['id'] . ")'>Voir les détails vétérinaires</button>";
                echo "<div class='collapse' id='detailsVet" . $animal['id'] . "'>";
                echo "<div class='card card-body'>";

                // Récupérer les détails vétérinaires pour cet animal
                $vet_stmt = $mysqli->prepare("SELECT etat, nourriture, grammage, date_passage, details FROM etats_animaux WHERE animal_id = ? ORDER BY date_passage DESC");
                $vet_stmt->bind_param("i", $animal['id']);
                $vet_stmt->execute();
                $vet_result = $vet_stmt->get_result();

                if ($vet_result->num_rows > 0) {
                    while ($vet = $vet_result->fetch_assoc()) {
                        echo "<p><strong>Date de passage :</strong> " . htmlspecialchars($vet['date_passage']) . "</p>";
                        echo "<p><strong>État :</strong> " . htmlspecialchars($vet['etat']) . "</p>";
                        echo "<p><strong>Nourriture :</strong> " . htmlspecialchars($vet['nourriture']) . " (" . htmlspecialchars($vet['grammage']) . "g)</p>";
                        echo "<p><strong>Détails :</strong> " . htmlspecialchars($vet['details']) . "</p>";
                        echo "<hr>";
                    }
                } else {
                    echo "<p>Aucun détail vétérinaire disponible pour cet animal.</p>";
                }

                echo "</div></div>";  // Fin de l'accordéon

                echo "</div>";

                $vet_stmt->close();
            }

            echo "</div>";
        } else {
            echo "<p>Aucun animal trouvé pour cet habitat.</p>";
        }
        
        $stmt->close();
    } else {
        echo "<p>ID de l'habitat non fourni.</p>";
    }
    $mysqli->close();
    ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>l
<script>
function incrementPopularity(animalId) {
    fetch("increment_popularity.php?animal_id=" + animalId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mettre à jour l'affichage de la popularité
                let popularityElement = document.getElementById("popularity-" + animalId);
                popularityElement.innerText = parseInt(popularityElement.innerText) + 1;
            }
        })
        .catch(error => console.error("Erreur:", error));
}
</script>

<!-- Styles pour l'affichage -->
<style>
    
.content-wrapper {
    min-height: 100vh; /* Remplit au moins 80% de la hauteur de l'écran */
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    padding: 20px;
    background-color: #f9f9f9;
}

.animals {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center; /* Centre les éléments horizontalement */
}

.animal-card {
    border: 1px solid #ccc;
    padding: 10px;
    width: 300px;
    text-align: center;
    background-color: white;
    border-radius: 20px;
}

.animal-photo {
    width: 100%;
    height: auto;
    border-radius: 5px;
}

.collapse {
    background-color: #f9f9f9; /* Couleur de fond de l'accordéon */
    border: 1px solid #ddd; /* Bordure autour du contenu de l'accordéon */
    padding: 10px;
    border-radius: 5px;
    margin-top: 10px;
}

/* Bouton d'accordéon */
.btn-primary {
    display: inline-block;
    padding: 10px 20px;
    background-color: #E9A300;
    color: black;
    text-align: center;
    text-decoration: none;
    border-radius: 20px;
    font-size: 16px;
    transition: transform 0.3s ease, background-color 0.3s ease;
    margin: 10px;
}

.btn-primary:hover {
    transform: scale(1.15); 
    background-color: #E9A300;
}

/* Contenu de l'accordéon */
.card.card-body p {
    width: auto;
    margin: 5px 0;
    font-size: 14px;
    color: #333;
}

/* Bordure entre les entrées vétérinaires */
.card.card-body hr {
    border-top: 1px solid #bbb;
}
</style>

<?php require_once 'elements/footer.php'; ?>