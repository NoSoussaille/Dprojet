<?php
// Inclusion du header
require_once 'headerdash.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Message pour afficher les retours d'actions
$message = "";

// Ajout d'un habitat
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    
    // Traitement de l'image pour l'ajout
    $image = $_FILES['image'];
    $image_path = null;
    if ($image['tmp_name']) {
        $target_dir = "images/habitats/";
        $image_path = $target_dir . uniqid() . "-" . basename($image["name"]);
        if (move_uploaded_file($image["tmp_name"], $image_path)) {
            // Image téléchargée avec succès
        } else {
            $message = "<p class='alert alert-danger'>Erreur lors de l'upload de l'image.</p>";
        }
    }

    // Insertion du nouvel habitat dans la base de données
    $stmt = $mysqli->prepare("INSERT INTO habitats (nom, description, image) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nom, $description, $image_path);
    if ($stmt->execute()) {
        $message = "<p class='alert alert-success'>Habitat ajouté avec succès !</p>";
    } else {
        $message = "<p class='alert alert-danger'>Erreur lors de l'ajout de l'habitat.</p>";
    }
    $stmt->close();
}

// Modification d'un habitat
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];

    // Traitement de l'image pour la modification
    $image = $_FILES['image'];
    if ($image['tmp_name']) {
        $target_dir = "images/habitats/";
        $image_path = $target_dir . uniqid() . "-" . basename($image["name"]);
        move_uploaded_file($image["tmp_name"], $image_path);

        // Mise à jour avec nouvelle image
        $stmt = $mysqli->prepare("UPDATE habitats SET nom = ?, description = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nom, $description, $image_path, $id);
    } else {
        // Mise à jour sans changement d'image
        $stmt = $mysqli->prepare("UPDATE habitats SET nom = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nom, $description, $id);
    }
    $stmt->execute();
    $stmt->close();
}

// Suppression d'un habitat
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $stmt = $mysqli->prepare("DELETE FROM habitats WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Récupérer la liste des habitats
$query = "SELECT id, nom, description, image FROM habitats";
$result = $mysqli->query($query);
?>

<div class="title beige">
     <h3 class="sous-menu vert">Gestion des habitats</h3>
</div>

<div class="dash1">
    <?php if ($result->num_rows > 0): ?>
        <h1>Liste des Habitats</h1>
        <table class="table">
            <thead>
                <tr class="titre">
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['nom']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td>
                            <?php if (!empty($row['image'])): ?>
                                <img src="<?php echo htmlspecialchars($row['image']); ?>" alt="Image de l'habitat" style="width: 50px; height: 50px;">
                            <?php endif; ?>
                        </td>
                        <td>
                            <!-- Bouton pour ouvrir le modal de modification -->
                            <button type="button" class="btn btn-warning" data-id="<?php echo $row['id']; ?>" data-nom="<?php echo $row['nom']; ?>" data-description="<?php echo $row['description']; ?>" onclick="openEditModal(this)">Modifier</button>
                            <!-- Bouton pour ouvrir le modal de confirmation de suppression -->
                            <button type="button" class="btn btn-danger" data-id="<?php echo $row['id']; ?>" onclick="openDeleteModal(this)">Supprimer</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun habitat trouvé.</p>
    <?php endif; ?>
</div>

<!-- Modal Bootstrap pour la suppression -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Êtes-vous sûr de vouloir supprimer cet habitat ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form id="deleteForm" action="ghabitats.php" method="post" style="display:inline;">
            <input type="hidden" name="id" id="habitatId">
            <button type="submit" name="delete" class="btn btn-danger">Confirmer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal Bootstrap pour la modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Modification de l'habitat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editForm" action="ghabitats.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editHabitatId">
            <div class="mb-3">
                <label for="editNom" class="form-label">Nom de l'habitat</label>
                <input type="text" class="form-control" id="editNom" name="nom" required>
            </div>
            <div class="mb-3">
                <label for="editDescription" class="form-label">Description</label>
                <textarea class="form-control" id="editDescription" name="description" required></textarea>
            </div>
            <div class="mb-3">
                <label for="editImage" class="form-label">Changer l'image</label>
                <input type="file" class="form-control" id="editImage" name="image">
            </div>
            <button type="submit" name="edit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Formulaire pour ajouter un habitat -->
<div class="dash1">
    <form action="ghabitats.php" method="post" enctype="multipart/form-data">
        <h2>Ajouter un nouvel habitat</h2>
        <input type="hidden" name="add">
        <input type="text" name="nom" placeholder="Nom de l'habitat" required>
        <textarea name="description" placeholder="Description de l'habitat" required></textarea>
        <input type="file" name="image" required>
        <button type="submit" class="btn btn-primary">Ajouter l'habitat</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scriptdash.js"></script>
<script>
    function openDeleteModal(button) {
        var habitatId = button.getAttribute('data-id');
        document.getElementById('habitatId').value = habitatId;
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), { keyboard: false });
        myModal.show();
    }

    function openEditModal(button) {
        var habitatId = button.getAttribute('data-id');
        var nom = button.getAttribute('data-nom');
        var description = button.getAttribute('data-description');
        
        document.getElementById('editHabitatId').value = habitatId;
        document.getElementById('editNom').value = nom;
        document.getElementById('editDescription').value = description;

        var editModal = new bootstrap.Modal(document.getElementById('editModal'), { keyboard: false });
        editModal.show();
    }
</script>

</body>
</html>