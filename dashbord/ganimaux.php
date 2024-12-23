<?php
// Inclusion du header
require_once 'headerdash.php';
require_once 'session_check.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Récupère les habitats pour le formulaire
$habitats_query = "SELECT id, nom FROM habitats";
$habitats_result = $mysqli->query($habitats_query);
$habitats = [];
while ($row = $habitats_result->fetch_assoc()) {
    $habitats[] = $row;
}

// Vérifie si le formulaire d'ajout a été soumis
$message = ""; // Variable pour afficher les messages
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $prenom = $_POST['prenom'];
    $race = $_POST['race'];
    $habitat_id = $_POST['habitat_id'];
    $image = $_FILES['image'];

    // Dossier de destination des images
    $target_dir = "images/animaux/";
    $target_file = $target_dir . uniqid() . "-" . basename($image["name"]);

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        // Insère les données dans la base de données
        $stmt = $mysqli->prepare("INSERT INTO animaux (prenom, race, habitat_id, photo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssis", $prenom, $race, $habitat_id, $target_file);
        if ($stmt->execute()) {
            $message = "<p class='alert alert-success'>Animal ajouté avec succès !</p>";
        } else {
            $message = "<p class='alert alert-danger'>Erreur lors de l'ajout de l'animal.</p>";
        }
        $stmt->close();

        header("Location: ganimaux.php"); // Redirection après ajout pour éviter double soumission
        exit();
    } else {
        $message = "<p class='alert alert-danger'>Erreur lors de l'upload de l'image.</p>";
    }
}

// Supprime un animal si le bouton "Supprimer" est cliqué
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Prépare et exécute la suppression dans la base de données
    $stmt = $mysqli->prepare("DELETE FROM animaux WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<p class='alert alert-success'>Animal supprimé avec succès !</p>";
    } else {
        $message = "<p class='alert alert-danger'>Erreur lors de la suppression de l'animal.</p>";
    }
    $stmt->close();

    header("Location: ganimaux.php"); // Redirection après suppression pour éviter double soumission
    exit();
}

// Récupérer les animaux et leurs habitats pour l'affichage
$query = "SELECT animaux.id, animaux.prenom, animaux.race, habitats.nom AS habitat_nom 
          FROM animaux 
          JOIN habitats ON animaux.habitat_id = habitats.id";
$result = $mysqli->query($query);
?>

<div class="title beige">
     <h3 class="sous-menu vert">Gestion des animaux</h3>
</div>

<div class="dash1">
    <?php if ($result->num_rows > 0): ?>
        <h1>Liste des Animaux et leurs Habitats</h1>
        <table class="table">
            <thead>
                <tr class="titre">
                    <th>Prénom</th>
                    <th>Race</th>
                    <th>Habitat</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['prenom']); ?></td>
                        <td><?php echo htmlspecialchars($row['race']); ?></td>
                        <td><?php echo htmlspecialchars($row['habitat_nom']); ?></td>
                        <td>
                            <!-- Bouton pour ouvrir le modal de confirmation -->
                            <button type="button" class="btn btn-danger" data-id="<?php echo $row['id']; ?>" onclick="openModal(this)">Supprimer</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun animal trouvé.</p>
    <?php endif; ?>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Êtes-vous sûr de vouloir supprimer cet animal ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form id="deleteForm" action="ganimaux.php" method="post" style="display:inline;">
            <input type="hidden" name="id" id="animalId">
            <button type="submit" name="delete" class="btn btn-danger">Confirmer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
// Libère le résultat de la mémoire
$result->free();
?>
    <div class="dash1">
        <!-- Formulaire pour ajouter un animal -->
        <form action="ganimaux.php" method="post" enctype="multipart/form-data">
        <h2>Ajouter un nouvel animal</h2>
            <input type="text" name="prenom" placeholder="Prénom de l'animal" required>
            <input type="text" name="race" placeholder="Race de l'animal" required>

            <!-- Sélection de l'habitat -->
            <select name="habitat_id" required>
                <option value="">Sélectionnez l'habitat</option>
                <?php foreach ($habitats as $habitat): ?>
                    <option value="<?php echo $habitat['id']; ?>"><?php echo htmlspecialchars($habitat['nom']); ?></option>
                <?php endforeach; ?>
            </select>

            <input type="file" name="image" required>
            <button type="submit">Ajouter l'animal</button>
        </form>
    </div>
</div>

<br>

<?php
// Libération des résultats et fermeture de la connexion
$habitats_result->free();
$mysqli->close();
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scriptdash.js"></script>
<script>
    // Fonction pour ouvrir le modal de confirmation
    function openModal(button) {
        // Récupère l'id de l'animal à supprimer
        var animalId = button.getAttribute('data-id');
        document.getElementById('animalId').value = animalId;

        // Affiche le modal de confirmation
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), {
            keyboard: false
        });
        myModal.show();
    }
</script>

</body>
</html>