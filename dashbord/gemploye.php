<?php
require_once 'headerdash.php';
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'administrateur') {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Détails de connexion à la base de données
require_once 'db_connexion.php';

// Supprime un employé si le bouton "Supprimer" est cliqué
if (isset($_POST['delete']) && isset($_POST['id'])) {
    $id = (int) $_POST['id'];

    // Prépare et exécute la suppression dans la base de données
    $stmt = $mysqli->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: gemploye.php");
    exit();
}

// Modifie un employé si le bouton "Modifier" est cliqué
if (isset($_POST['edit'])) {
    $id = (int) $_POST['id'];
    $username = $_POST['username'];
    $mdp = $_POST['mdp'];
    $role_id = $_POST['role_id'];

    // Hachage du mot de passe avant la mise à jour
    $hashed_password = password_hash($mdp, PASSWORD_DEFAULT);

    // Mise à jour de l'utilisateur dans la table `utilisateurs` avec le mot de passe haché
    $stmt = $mysqli->prepare("UPDATE utilisateurs SET username = ?, mdp = ? WHERE id = ?");
    $stmt->bind_param("ssi", $username, $hashed_password, $id);
    $stmt->execute();
    $stmt->close();

    // Mise à jour du rôle dans la table `utilisateur_roles`
    $stmt = $mysqli->prepare("UPDATE utilisateur_roles SET role_id = ? WHERE utilisateur_id = ?");
    $stmt->bind_param("ii", $role_id, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: gemploye.php");
    exit();
}

// Ajoute un employé en empéchant le duplicatat des username
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_employee'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['mdp'], PASSWORD_DEFAULT); // Hachage du mot de passe
    $role_id = $_POST['role_id'];

    // Vérifie si le nom d'utilisateur existe déjà
    $stmt = $mysqli->prepare("SELECT id FROM utilisateurs WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Le nom d'utilisateur existe déjà
        echo "<p class='alert alert-danger'>Ce nom d'utilisateur est déjà pris. Veuillez en choisir un autre.</p>";
        $stmt->close();
    } else {
        // Insère l'utilisateur si le nom est unique
        $stmt->close();

        // Insérer dans la table `utilisateurs`
        $stmt = $mysqli->prepare("INSERT INTO utilisateurs (username, mdp) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $user_id = $stmt->insert_id;
        $stmt->close();

        // Associer le rôle à l'utilisateur
        $stmt = $mysqli->prepare("INSERT INTO utilisateur_roles (utilisateur_id, role_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $role_id);
        $stmt->execute();
        $stmt->close();

        // Redirection après ajout
        header("Location: gemploye.php");
        exit();
    }
}


// Récupère les utilisateurs et leurs rôles
$query = "SELECT utilisateurs.id, utilisateurs.username, utilisateurs.mdp, roles.nom AS role, roles.id AS role_id
          FROM utilisateurs
          JOIN utilisateur_roles ON utilisateurs.id = utilisateur_roles.utilisateur_id
          JOIN roles ON utilisateur_roles.role_id = roles.id";

$result = $mysqli->query($query);

// Récupère les rôles pour le formulaire de modification
$roles_query = "SELECT id, nom FROM roles";
$roles_result = $mysqli->query($roles_query);
$roles = [];
while ($row = $roles_result->fetch_assoc()) {
    $roles[] = $row;
}
?>

<div class="title beige">
     <h3 class="sous-menu vert">Gestion des employés</h3>
</div>
<div class="dash1">
    <!-- Titre de la section -->
    <h1>Liste des utilisateurs et leurs rôles</h1>
    
    <!-- Tableau des utilisateurs -->
    <table class="table">
        <thead>
            <tr class="titre">
                <th>Nom d'utilisateur</th>
                <th>Rôle</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['role']); ?></td>
                    <td>
                        <!-- Boutons pour Modifier et Supprimer -->
                        <button type="button" class="btn btn-warning" data-id="<?php echo $row['id']; ?>" data-username="<?php echo $row['username']; ?>" data-mdp="<?php echo $row['mdp']; ?>" data-role-id="<?php echo $row['role_id']; ?>" onclick="openEditModal(this)">Modifier</button>
                        <button type="button" class="btn btn-danger" data-id="<?php echo $row['id']; ?>" onclick="openDeleteModal(this)">Supprimer</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Formulaire pour ajouter un employé -->
    <form action="gemploye.php" method="post" class="form-employee">
        <h2>Ajouter un nouvel employé</h2>
        <input type="text" name="username" placeholder="Nom d'utilisateur" required>

        <!-- Champ pour le mot de passe -->
        <input type="password" name="mdp" placeholder="Mot de passe" required>

        <!-- Sélection du rôle -->
        <select name="role_id" required>
            <option value="">Sélectionnez le rôle</option>
            <?php foreach ($roles as $role): ?>
                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['nom']); ?></option>
            <?php endforeach; ?>
        </select>

        <button type="submit" name="add_employee" class="button">Ajouter l'employé</button>
    </form>
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
        Êtes-vous sûr de vouloir supprimer cet employé ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form id="deleteForm" action="gemploye.php" method="post" style="display:inline;">
            <input type="hidden" name="id" id="employeId">
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
        <h5 class="modal-title" id="editModalLabel">Modification de l'employé</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editForm" action="gemploye.php" method="post">
            <input type="hidden" name="id" id="editEmployeId">
            <div class="mb-3">
                <label for="editUsername" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="editUsername" name="username" required>
            </div>
            <div class="mb-3">
                <label for="editMdp" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="editMdp" name="mdp" required>
            </div>
            <div class="mb-3">
                <label for="editRole" class="form-label">Rôle</label>
                <select id="editRole" name="role_id" class="form-select" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['nom']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="edit" class="btn btn-primary">Enregistrer les modifications</button>
        </form>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scriptdash.js"></script>
<script>
    function openDeleteModal(button) {
        var employeId = button.getAttribute('data-id');
        document.getElementById('employeId').value = employeId;
        var myModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'), { keyboard: false });
        myModal.show();
    }

    function openEditModal(button) {
        var employeId = button.getAttribute('data-id');
        var username = button.getAttribute('data-username');
        var mdp = button.getAttribute('data-mdp');
        var roleId = button.getAttribute('data-role-id');
        
        document.getElementById('editEmployeId').value = employeId;
        document.getElementById('editUsername').value = username;
        document.getElementById('editMdp').value = mdp;
        document.getElementById('editRole').value = roleId;

        var editModal = new bootstrap.Modal(document.getElementById('editModal'), { keyboard: false });
        editModal.show();
    }
</script>
</body>
</html>