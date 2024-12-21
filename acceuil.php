<?php
$title = "Accueil - Zoo Arcadia";
$meta_description = "Bienvenue au Zoo Arcadia, un parc écologique et immersif avec une faune diversifiée et des services uniques pour toute la famille.";
require_once 'elements/header.php';
?>
    <div class="title beige">
        <h2 class="sous-menu vert">Bienvenue</h2>
    </div>
<div class="mainpage">
    <img src="image/page d'accueil/Image.webp" alt="photo de l'entrée du zoo d'Arcadia" class="imgZoo">

    <?php
    // Vérification de la connexion déjà effectuée dans db_connection.php
    // Informations de connexion à la base de données
    require_once 'db_connection.php';
    // Récupère les horaires d'ouverture
    $query = "SELECT jour, ouverture, fermeture FROM horaires_ouverture ORDER BY FIELD(jour, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')";
    $result = $mysqli->query($query);

    // Génération du tableau des horaires
    $horaires = [];
    while ($row = $result->fetch_assoc()) {
        $jour = ucfirst($row['jour']);
        $ouverture = date('H:i', strtotime($row['ouverture']));
        $fermeture = date('H:i', strtotime($row['fermeture']));
        
        if ($ouverture === '00:00' && $fermeture === '00:00') {
            $horaires[$jour] = 'Fermé';
        } else {
            $horaires[$jour] = "de $ouverture à $fermeture";
        }
    }
    $result->free();
    ?>

    <div class="bienvenue2">
        <h3>Horaires d'ouverture</h3>
        <ul>
            <?php foreach ($horaires as $jour => $horaire): ?>
                <li><strong><?php echo $jour; ?></strong> : <?php echo $horaire; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
    
    <div class="mainpage2">
        <p class="bienvenue">Bienvenue au Zoo Arcadia, situé en plein cœur de la
            Bretagne, près de la légendaire forêt de Brocéliande. Fondé en 1960, le zoo est un
            havre de paix pour des centaines d’espèces animales réparties dans des habitats naturels
            soigneusement reconstitués, tels que la savane, la jungle, et les marais. Arcadia se distingue
            par son engagement profond en faveur de la préservation de la faune et de l’écologie.
        </p>
        <img src="image/page d'accueil/elephant-8663016_640.jpg" alt="Image de l'oeil d'un éléphant" class="imgZoo2">
    </div>

    <div class="mainpage3">
        <img src="image/page d'accueil/solar-cell-4045029_640.jpg" alt="photo des panneaux solaire du zoo" class="imgZoo3">
        <p class="bienvenue2">
        Entièrement autonome sur le plan énergétique, 
        le zoo fonctionne exclusivement à partir de sources renouvelables. 
        Notre équipe de vétérinaires dévoués veille quotidiennement à la santé 
        et au bien-être des animaux pour garantir une expérience inoubliable aux
        visiteurs, tout en respectant les principes éthiques les plus stricts.  
        Venez découvrir un lieu où la biodiversité et l’écologie sont au cœur de chaque action !
        <br><a href="ecologie.php" class="button">Voir plus</a></p>
    </div>
    <div class="title vert">
    <h2 class="sous-menu beige">Les habitats du zoo</h2>
</div>
<div class="mainpage4">
    <?php 
    // Récupération des habitats
    $query_habitats = "SELECT id, nom, description, image FROM habitats LIMIT 3";  // Limite à 3 habitats
    $result_habitats = $mysqli->query($query_habitats);

    while ($habitat = $result_habitats->fetch_assoc()):
        // Génère le chemin de l'image pour chaque habitat
        $photo_path = !empty($habitat['image']) ? "dashbord/" . $habitat['image'] : "images/default_habitat.jpg";
    ?>
        <a href="habitats.php#habitat-<?php echo $habitat['id']; ?>" class="card-link">
            <div class="card">
                <img src="<?php echo htmlspecialchars($photo_path); ?>" class="card-img-top" alt="Image de <?php echo htmlspecialchars($habitat['nom']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($habitat['nom']); ?></h5>
                </div>
            </div>
        </a>
    <?php endwhile; ?>
</div>

<div class="mainpage4">
    <a href="habitats.php" class="button">Voir plus</a>
</div>


<div class="title vert">
    <h2 class="sous-menu beige">Nos Services</h2>
</div>

<div class="mainpage4">
    <?php 
    // Récupération des services
    $query_services = "SELECT id, nom, description, photo FROM services LIMIT 3";  // Limite à 3 services
    $result_services = $mysqli->query($query_services);

    while ($service = $result_services->fetch_assoc()):
        // Génère le chemin de l'image pour chaque service
        $photo_path = !empty($service['photo']) ? "dashbord/" . $service['photo'] : "images/default_service.jpg";
        
        // Génère un ID d'ancre unique basé sur le nom du service
        $anchor_id = strtolower(str_replace(' ', '-', $service['nom']));  // Convertit les espaces en tirets
    ?>
        <a href="service.php#<?php echo $anchor_id; ?>" class="card-link">
            <div class="card">
                <img src="<?php echo htmlspecialchars($photo_path); ?>" class="card-img-top" alt="Image de <?php echo htmlspecialchars($service['nom']); ?>">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($service['nom']); ?></h5>
                </div>
            </div>
        </a>
    <?php endwhile; ?>
</div>
<div class="mainpage4">
    <a href="service.php" class="button">Voir plus</a>
</div>

<?php
// Libérer les résultats et fermer la connexion
$result_services->free();
?>
    <div class="title beige">
        <h2 class="sous-menu vert"> Avis</h2>
    </div>
    <div class="mainpage5">
    <?php 
        // Requête pour récupérer les avis approuvés
        $query = "SELECT pseudo, avis FROM avis_client WHERE status = 'approved'";
        $result = $mysqli->query($query);
        
        if ($result->num_rows > 0): ?>
            <div id="avisCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <?php $isActive = true; ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="carousel-item <?php echo $isActive ? 'active' : ''; ?>">
                            <div class="mainavis text-center">
                                <p class="pseudo"><strong><?php echo htmlspecialchars($row['pseudo']); ?></strong></p>
                                <p class="avis"><?php echo htmlspecialchars(stripslashes($row['avis'])); ?></p>
                            </div>
                        </div>
                        <?php $isActive = false; ?>
                    <?php endwhile; ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#avisCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Précédent</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#avisCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Suivant</span>
                </button>
            </div>
        <?php else: ?>
            <div class="mainavis">
                <p class="excuse">SECTION AVIS BIENTOT DISPONIBLE</p>
                <p class="excuse">EXCUSEZ NOUS POUR LA GENE OCCASIONNEE</p>
                <a href="#" class="button">Laisser un avis</a>
            </div>
        <?php endif; ?>

    <?php
    // Libération des résultats et fermeture de la connexion
    $result->free();
    $mysqli->close();
    ?>
    
</div>
<div class="mainpage5">
    <a href="contact.php#avis" class="button">Laisser un avis</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
<?php require_once 'elements/footer.php'; ?>
<script src="script.js"></script>
</body>
</html>