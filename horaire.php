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
    use MongoDB\Client;
    
    // 1) Inclure l’autoload de Composer
    require __DIR__ . '/vendor/autoload.php';
    
    // 2) Connexion à MongoDB (adapter la chaîne si nécessaire)
    try {
        // Chaîne de connexion exemple : remplacer par vos identifiants
        $client = new Client("mongodb+srv://Cluster54037:marwane123@cluster54037.zom2y.mongodb.net/ma_base?retryWrites=true&w=majority");
        $collection = $client->ma_base->horaires; // Nom de la collection “horaires”
    } catch (Exception $e) {
        die("Erreur de connexion MongoDB : " . $e->getMessage());
    }
    
    $cursor = $collection->find([]);
    $orderMap = [
        'lundi'    => 1,
        'mardi'    => 2,
        'mercredi' => 3,
        'jeudi'    => 4,
        'vendredi' => 5,
        'samedi'   => 6,
        'dimanche' => 7,
    ];
    
    $horairesRaw = iterator_to_array($cursor); // Convertir le cursor en tableau
    // Trier localement selon $orderMap
    usort($horairesRaw, function ($a, $b) use ($orderMap) {
        // Récupérer le jour (en minuscule) et comparer
        $jourA = strtolower($a['jour']);
        $jourB = strtolower($b['jour']);
    
        return $orderMap[$jourA] <=> $orderMap[$jourB];
    });
    
    // Maintenant $horairesRaw est trié par l’ordre du tableau $orderMap
    $horaires = [];
    foreach ($horairesRaw as $doc) {
        // On récupère jour, ouverture, fermeture
        $jour = ucfirst($doc['jour']);
        // Sécuriser “ouverture” et “fermeture” (ex: 08:00, 00:00, etc.)
        $ouverture = date('H:i', strtotime($doc['ouverture']));
        $fermeture = date('H:i', strtotime($doc['fermeture']));
    
        if ($ouverture === '00:00' && $fermeture === '00:00') {
            $horaires[$jour] = 'Fermé';
        } else {
            $horaires[$jour] = "de $ouverture à $fermeture";
        }
    }
    ?>
    
    <div class="bienvenue2">
        <h3>Horaires d'ouverture</h3>
        <ul>
            <?php foreach ($horaires as $jour => $horaire): ?>
                <li><strong><?php echo htmlspecialchars($jour); ?></strong> : 
                    <?php echo htmlspecialchars($horaire); ?>
                </li>
            <?php endforeach; ?>
        </ul>
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