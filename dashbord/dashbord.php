<?php require_once 'headerdash.php'; ?>

<div class="title beige">
    <h3 class="sous-menu vert">Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
</div>

<?php
// Affiche le message d'erreur si le paramètre 'error' est dans l'URL
if (isset($_GET['error']) && $_GET['error'] == 'acces_refuse') {
    echo "<div class='alert alert-danger text-center' role='alert'>
        Vous n'avez pas accès à cette section.
      </div>";
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Récupérer les horaires actuels
$horaires_result = $mysqli->query("SELECT * FROM horaires_ouverture");

// Traitement du formulaire de mise à jour des horaires
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['role']) && $_SESSION['role'] === 'administrateur') {
    foreach ($_POST['horaires'] as $jour => $horaire) {
        $ouverture = $mysqli->real_escape_string($horaire['ouverture']);
        $fermeture = $mysqli->real_escape_string($horaire['fermeture']);
        
        $stmt = $mysqli->prepare("UPDATE horaires_ouverture SET ouverture = ?, fermeture = ? WHERE jour = ?");
        $stmt->bind_param("sss", $ouverture, $fermeture, $jour);
        $stmt->execute();
    }
    header("Location: dashbord.php");
    exit();
}

// Récupérer le top 5 des animaux les plus populaires
$popular_animals_result = $mysqli->query("SELECT prenom, race, popularite FROM animaux ORDER BY popularite DESC LIMIT 5");
?>

<section class="dash1">
    <iframe id="widget_autocomplete_preview" width="400" height="320" frameborder="0" src="https://meteofrance.com/widget/prevision/352110##358D39CC" title="Prévisions Paimpont par Météo-France"></iframe>
</section>

<div class="title beige">
    <h3 class="sous-menu vert">Top 5 des Animaux les Plus Populaires</h3>
</div>

<div class="dash1">
    <table class="table">
        <thead>
            <tr class="titre">
                <th>Prénom</th>
                <th>Race</th>
                <th>Popularité</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($animal = $popular_animals_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($animal['prenom']); ?></td>
                    <td><?php echo htmlspecialchars($animal['race']); ?></td>
                    <td><?php echo htmlspecialchars($animal['popularite']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php if ($_SESSION['role'] === 'administrateur'): ?>
    <div class="title beige">
        <h3 class="sous-menu vert">Modifier les Horaires d'Ouverture</h3>
    </div>
    <div class="dash1">
        <form method="POST">
            <?php while ($horaire = $horaires_result->fetch_assoc()): ?>
                <?php
                    // Formater les horaires sans les secondes
                    $ouverture = date('H:i', strtotime($horaire['ouverture']));
                    $fermeture = date('H:i', strtotime($horaire['fermeture']));
                ?>
                <div class="mb-3">
                    <label for="ouverture_<?php echo $horaire['jour']; ?>" class="form-label"><?php echo ucfirst($horaire['jour']); ?></label>
                    <input type="time" name="horaires[<?php echo $horaire['jour']; ?>][ouverture]" value="<?php echo $ouverture; ?>" required>
                    <input type="time" name="horaires[<?php echo $horaire['jour']; ?>][fermeture]" value="<?php echo $fermeture; ?>" required>
                </div>
            <?php endwhile; ?>
            <button type="submit" class="btn btn-primary">Mettre à jour</button>
        </form>
    </div>
<?php endif; ?>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Supprime le paramètre "error" de l'URL après affichage
    if (window.location.search.includes("error=acces_refuse")) {
        const url = new URL(window.location);
        url.searchParams.delete("error");
        window.history.replaceState({}, document.title, url.toString());
    }
</script>
</body>
</html>