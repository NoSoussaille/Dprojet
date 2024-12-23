<?php
// Inclusion du header
require_once 'headerdash.php';
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'administrateur' && $_SESSION['role'] !== 'vétérinaire')) {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Paramètres de pagination
$limit = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filtre de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Requête pour récupérer les animaux et leur suivi, avec recherche et pagination
$query = "SELECT animaux.id AS animal_id, animaux.prenom, animaux.race,
                 etats_animaux.etat, etats_animaux.nourriture, etats_animaux.grammage, etats_animaux.date_passage, etats_animaux.details
          FROM animaux
          LEFT JOIN etats_animaux ON animaux.id = etats_animaux.animal_id
          WHERE animaux.prenom LIKE ? OR animaux.race LIKE ?
          LIMIT ? OFFSET ?";

$stmt = $mysqli->prepare($query);
$search_param = '%' . $search . '%';
$stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
$stmt->execute();
$animaux_result = $stmt->get_result();

// Compter le nombre total d'animaux pour la pagination
$count_query = "SELECT COUNT(*) AS total FROM animaux WHERE prenom LIKE ? OR race LIKE ?";
$count_stmt = $mysqli->prepare($count_query);
$count_stmt->bind_param("ss", $search_param, $search_param);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_animals = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_animals / $limit);

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['animal_id'])) {
    $animal_id = $_POST['animal_id'];
    $etat = $_POST['etat'];
    $nourriture = $_POST['nourriture'];
    $grammage = $_POST['grammage'];
    $date_passage = $_POST['date_passage'];
    $details = $_POST['details'];

    // Préparer et exécuter la requête pour insérer ou mettre à jour les informations de suivi
    $stmt = $mysqli->prepare("INSERT INTO etats_animaux (animal_id, etat, nourriture, grammage, date_passage, details)
                              VALUES (?, ?, ?, ?, ?, ?)
                              ON DUPLICATE KEY UPDATE etat = VALUES(etat), nourriture = VALUES(nourriture), grammage = VALUES(grammage), date_passage = VALUES(date_passage), details = VALUES(details)");
    $stmt->bind_param("ississ", $animal_id, $etat, $nourriture, $grammage, $date_passage, $details);
    if ($stmt->execute()) {
        $message = "<p class='alert alert-success'>Suivi de l'animal mis à jour avec succès !</p>";
    } else {
        $message = "<p class='alert alert-danger'>Erreur lors de la mise à jour du suivi de l'animal.</p>";
    }
    $stmt->close();

    // Redirection pour éviter la resoumission et vider les champs
    header("Location: " . $_SERVER['PHP_SELF'] . "?search=" . urlencode($search) . "&page=" . $page);
    exit();
}
?>

<style>

</style>
<div class="title beige">
    <h3 class="sous-menu vert">Suivi vétérinaire des animaux</h3>
</div>

<div class="dash1">
    <h1>Liste des Animaux - Mise à jour du suivi</h1>
    <form method="get" action="veto.php" class="search-form">
        <input type="text" name="search" placeholder="Rechercher par prénom ou race" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Rechercher</button>
    </form>
</div>

<div class="dash1">
    <?php if ($animaux_result->num_rows > 0): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr class="titre">
                        <th>Prénom</th>
                        <th>Race</th>
                        <th>État</th>
                        <th>Nourriture</th>
                        <th>Grammage</th>
                        <th>Date de passage</th>
                        <th>Détails</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($animal = $animaux_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($animal['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($animal['race']); ?></td>
                            <form action="veto.php?search=<?php echo urlencode($search); ?>&page=<?php echo $page; ?>" method="post">
                                <input type="hidden" name="animal_id" value="<?php echo $animal['animal_id']; ?>">
                                <td>
                                    <select name="etat" required>
                                        <option value="">Sélectionner</option>
                                        <option value="bon">Bon</option>
                                        <option value="moyen">Moyen</option>
                                        <option value="mauvais">Mauvais</option>
                                    </select>
                                </td>
                                <td><input type="text" name="nourriture" placeholder="Entrer nourriture" required></td>
                                <td><input type="number" name="grammage" placeholder="Entrer grammage" required></td>
                                <td><input type="date" name="date_passage" required></td>
                                <td><input type="text" name="details" placeholder="Entrer détails"></td>
                                <td><button type="submit" class="btn btn-primary">Mettre à jour</button></td>
                            </form>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>  
        <?php else: ?>
        <p>Aucun animal trouvé.</p>
    <?php endif; ?>
</div>
<section class="dash1">
            <!-- Pagination -->
            <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="veto.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                   class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>

</section>

<?php
$stmt->close();
$animaux_result->free();
$mysqli->close();
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scriptdash.js"></script>
</body>
</html>