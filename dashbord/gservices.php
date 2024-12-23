<?php
// Inclusion du header
require_once 'headerdash.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Ajout d'un service avec une image
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_service'])) {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // Dossier de destination des images
    $target_dir = "images/services/";
    $target_file = $target_dir . uniqid() . "-" . basename($image["name"]);

    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        // Insertion du service dans la base de données avec le chemin de l'image
        $stmt = $mysqli->prepare("INSERT INTO services (nom, description, photo) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nom, $description, $target_file);
        $stmt->execute();
        $stmt->close();

        echo "<p class='alert alert-success'>Service ajouté avec succès !</p>";
    } else {
        echo "<p class='alert alert-danger'>Erreur lors de l'upload de l'image.</p>";
    }
}

// Suppression d'un service
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $stmt = $mysqli->prepare("DELETE FROM services WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: gservices.php");
    exit();
}

// Modification d'un service
if (isset($_POST['edit'])) {
    $id = (int)$_POST['id'];
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // Si une nouvelle image est téléchargée
    if ($image['tmp_name']) {
        $target_dir = "images/services/";
        $target_file = $target_dir . uniqid() . "-" . basename($image["name"]);
        move_uploaded_file($image["tmp_name"], $target_file);

        $stmt = $mysqli->prepare("UPDATE services SET nom = ?, description = ?, photo = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nom, $description, $target_file, $id);
    } else {
        // Mise à jour sans changement d'image
        $stmt = $mysqli->prepare("UPDATE services SET nom = ?, description = ? WHERE id = ?");
        $stmt->bind_param("ssi", $nom, $description, $id);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: gservices.php");
    exit();
}

// Récupérer les services pour affichage
$query = "SELECT id, nom, description, photo FROM services";
$result = $mysqli->query($query);
?>
<div class="title beige">
     <h3 class="sous-menu vert">Gestion des animaux</h3>
</div>

<div class="dash1">
<h1>liste des Services</h1>
<table class="table">
    <thead>
        <tr class="titre">
            <th>Nom</th>
            <th>Description</th>
            <th>Photo</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['nom']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td>
                    <?php if (!empty($row['photo'])): ?>
                        <img src="<?php echo $row['photo']; ?>" alt="Photo du service" style="width: 50px; height: 50px;">
                    <?php endif; ?>
                </td>
                <td>
                    <button type="button" class="btn btn-warning" data-id="<?php echo $row['id']; ?>" data-nom="<?php echo $row['nom']; ?>" data-description="<?php echo $row['description']; ?>" onclick="openEditModal(this)">Modifier</button>
                    <button type="button" class="btn btn-danger" data-id="<?php echo $row['id']; ?>" onclick="openDeleteModal(this)">Supprimer</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<!-- Modal pour la confirmation de suppression -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Êtes-vous sûr de vouloir supprimer ce service ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form id="deleteForm" action="gservices.php" method="post">
            <input type="hidden" name="id" id="serviceId">
            <button type="submit" name="delete" class="btn btn-danger">Confirmer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Modal pour la modification -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Modification du Service</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editForm" action="gservices.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="id" id="editServiceId">
            <div class="mb-3">
                <label for="editNom" class="form-label">Nom</label>
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
            <button type="submit" name="edit" class="btn btn-primary">Enregistrer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Formulaire pour ajouter un service -->
<div class="dash1">
    <form action="gservices.php" method="post" enctype="multipart/form-data">
        <h2>Ajouter un Service</h2>
        <input type="text" name="nom" placeholder="Nom du service" required>
        <textarea name="description" placeholder="Description du service" required></textarea>
        <input type="file" name="image" required>
        <button type="submit" name="add_service">Ajouter le service</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scriptdash.js"></script>
<script>
    function openDeleteModal(button) {
        var serviceId = button.getAttribute('data-id');
        document.getElementById('serviceId').value = serviceId;
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), { keyboard: false });
        myModal.show();
    }

    function openEditModal(button) {
        var serviceId = button.getAttribute('data-id');
        var nom = button.getAttribute('data-nom');
        var description = button.getAttribute('data-description');

        document.getElementById('editServiceId').value = serviceId;
        document.getElementById('editNom').value = nom;
        document.getElementById('editDescription').value = description;

        var editModal = new bootstrap.Modal(document.getElementById('editModal'), { keyboard: false });
        editModal.show();
    }
</script>
</body>
</html>
