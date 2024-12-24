<?php
$title = "Tarification - Zoo Arcadia";
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
        <div class="title vert">
        <h2 class="sous-menu beige" id="tarif">Tarif</h2>
    </div>
    <div class="mainpage2">
        <p class="bienvenue">
                Catégorie...........Tarif <br>

            Adulte (13 ans et +)...........15,00 €<br>
            Enfant (3 à 12 ans)...........10,00 €<br>
            Senior (65 ans et +)...........12,00 €<br>
            Famille (2 adultes + 2 enfants)...........40,00 €<br>
            Enfant (moins de 3 ans)...........Gratuit <br>

            <strong>Tarif pour le Petit Train </strong><br>

            Catégorie...........Tarif <br>
            Balade en Petit Train (par personne)...........5,00 € <br>
        </p>
    </div>
    
<?php require_once 'elements/footer.php'; ?>
<script src="script.js"></script>
</body>
</html>