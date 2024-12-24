<?php 
$title = "Les habitats - Zoo Arcadia";
$meta_description = "Découvrez tous les habitats de notre zoo.";
require_once 'elements/header.php'; 
require_once 'db_connection.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

// Récupération des habitats depuis la base de données
$query_habitats = "SELECT id, nom, description, image FROM habitats";
$result_habitats = $mysqli->query($query_habitats);

if ($result_habitats->num_rows > 0):
    while ($habitat = $result_habitats->fetch_assoc()): 
        // Génère le chemin de l'image pour chaque habitat
        $photo_path = !empty($habitat['image']) ? "dashbord/" . $habitat['image'] : "images/default_habitat.jpg";
?>
        <div class="title beige">
            <!-- Utilisation de l'identifiant 'habitat-id' pour chaque section -->
            <h2 class="sous-menu vert" id="habitat-<?php echo htmlspecialchars($habitat['id']); ?>">
                <?php echo htmlspecialchars($habitat['nom']); ?>
            </h2>
        </div>
        <div class="mainpage3">
            <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Photo de <?php echo htmlspecialchars($habitat['nom']); ?>" class="imgZoo3">
            <p class="bienvenue2">
                <?php echo htmlspecialchars($habitat['description']); ?>
                <br>
                <a href="animaux.php?habitat_id=<?php echo $habitat['id']; ?>" class="button">Voir les animaux</a>
            </p>
        </div>
<?php 
    endwhile; 
else: 
?>
    <p>Aucun habitat disponible pour le moment.</p>
<?php 
endif; 

$mysqli->close();
require_once 'elements/footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>