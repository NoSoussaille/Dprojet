<?php
// Inclusion du header
require_once 'headerdash.php';
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'administrateur' && $_SESSION['role'] !== 'vétérinaire')) {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Filtre de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 30;
$offset = ($page - 1) * $limit;

// Compter le nombre total de résultats pour la pagination
$count_query = "SELECT COUNT(*) as total FROM etats_animaux
                JOIN animaux ON etats_animaux.animal_id = animaux.id
                WHERE animaux.prenom LIKE ? OR animaux.race LIKE ?";
$count_stmt = $mysqli->prepare($count_query);
$search_param = '%' . $search . '%';
$count_stmt->bind_param("ss", $search_param, $search_param);
$count_stmt->execute();
$count_result = $count_stmt->get_result();
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Requête pour récupérer l'historique des suivis vétérinaires avec recherche et pagination
$query = "SELECT animaux.prenom, animaux.race, etats_animaux.etat, etats_animaux.nourriture, 
                 etats_animaux.grammage, etats_animaux.date_passage, etats_animaux.details
          FROM etats_animaux
          JOIN animaux ON etats_animaux.animal_id = animaux.id
          WHERE animaux.prenom LIKE ? OR animaux.race LIKE ?
          ORDER BY etats_animaux.date_passage DESC
          LIMIT ? OFFSET ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("ssii", $search_param, $search_param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="title beige">
    <h3 class="sous-menu vert">Historique des Suivis Vétérinaires</h3>
</div>

<div class="dash1">
    <h1>Antécédents de Suivi des Animaux</h1>
    <!-- Formulaire de recherche -->
    <form method="get" action="antecedent.php" class="search-form">
        <input type="text" name="search" placeholder="Rechercher par prénom ou race" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Rechercher</button>
    </form>
</div>

<div class="dash1">
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr class="titre">
                        <th>Prénom</th>
                        <th>Race</th>
                        <th>État</th>
                        <th>Nourriture</th>
                        <th>Grammage</th>
                        <th>Date de Passage</th>
                        <th>Détails</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                            <td><?php echo htmlspecialchars($row['race']); ?></td>
                            <td><?php echo htmlspecialchars($row['etat']); ?></td>
                            <td><?php echo htmlspecialchars($row['nourriture']); ?></td>
                            <td><?php echo htmlspecialchars($row['grammage']); ?></td>
                            <td><?php echo htmlspecialchars($row['date_passage']); ?></td>
                            <td><?php echo htmlspecialchars($row['details']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>Aucun antécédent de suivi trouvé.</p>
    <?php endif; ?>
</div>
<div class="dash1">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="antecedent.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                   class="<?php echo ($i == $page) ? 'active' : ''; ?>">
                   <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>

<?php
// Libération des résultats et fermeture de la connexion
$stmt->close();
$count_stmt->close();
$result->free();
$mysqli->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>