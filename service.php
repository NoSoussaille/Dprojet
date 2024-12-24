<?php 
$title = "Les services - Zoo Arcadia";
$meta_description = "Découvrez tout les service proposé par notre zoo.";
require_once 'elements/header.php'; 
require_once 'db_connection.php'; // Assurez-vous que ce fichier contient la connexion à la base de données

// Récupération des services depuis la base de données
$query_services = "SELECT id, nom, description, photo FROM services";
$result_services = $mysqli->query($query_services);

if ($result_services->num_rows > 0):
    while ($service = $result_services->fetch_assoc()): 
        // Génère le chemin de l'image pour chaque service
        $photo_path = !empty($service['photo']) ? "dashbord/" . $service['photo'] : "images/default_service.jpg";
?>
        <div class="title beige">
            <h2 class="sous-menu vert" id="<?php echo htmlspecialchars(strtolower($service['nom'])); ?>">
                <?php echo htmlspecialchars($service['nom']); ?>
            </h2>
        </div>
        <div class="mainpage3">
            <img src="<?php echo htmlspecialchars($photo_path); ?>" alt="Photo de <?php echo htmlspecialchars($service['nom']); ?>" class="imgZoo3">
            <p class="bienvenue2">
                <?php echo nl2br(htmlspecialchars($service['description'])); ?>
            </p>
        </div>
<?php 
    endwhile; 
else: 
?>
    <p>Aucun service disponible pour le moment.</p>
<?php 
endif; 

$mysqli->close();
require_once 'elements/footer.php'; 
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>