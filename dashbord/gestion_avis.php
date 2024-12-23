<?php
require_once 'headerdash.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'administrateur' && $_SESSION['role'] !== 'employé')) {
    header("Location: dashbord.php?error=acces_refuse");
    exit();
}

// Connexion à la base de données
require_once 'db_connexion.php';

// Traitement de l'approbation des avis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $avis_id = $_POST['avis_id'];
        $stmt = $mysqli->prepare("UPDATE avis_client SET status = 'approved' WHERE id = ?");
        $stmt->bind_param("i", $avis_id);
        $stmt->execute();
        header("Location: gestion_avis.php?success=approve");
        exit();
    } elseif (isset($_POST['confirm_delete'])) {
        $avis_id = $_POST['avis_id'];
        $stmt = $mysqli->prepare("DELETE FROM avis_client WHERE id = ?");
        $stmt->bind_param("i", $avis_id);
        $stmt->execute();
        header("Location: gestion_avis.php?success=delete");
        exit();
    }
}

// Récupération des avis
$query = "SELECT * FROM avis_client ORDER BY status ASC, id DESC";
$result = $mysqli->query($query);
?>

<div class="title beige">
    <h3 class="sous-menu vert">Gestion des Avis Clients</h3>
</div>

<div class="dash1">
    <?php if ($result->num_rows > 0): ?>
        <table class="table">
            <thead>
                <tr class="titre">
                    <th>Pseudo</th>
                    <th>Avis</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['pseudo']); ?></td>
                        <td><?php echo htmlspecialchars(stripslashes($row['avis'])); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td>
                            <div class="d-flex justify-content-center">
                                <form method="post" action="gestion_avis.php" class="me-2">
                                    <input type="hidden" name="avis_id" value="<?php echo $row['id']; ?>">
                                    <?php if ($row['status'] === 'pending'): ?>
                                        <button type="submit" name="approve" class="btn btn-success">Approuver</button>
                                    <?php endif; ?>
                                </form>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?php echo $row['id']; ?>">
                                    Supprimer
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun avis trouvé.</p>
    <?php endif; ?>
</div>

<!-- Modal de confirmation de suppression -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmation de suppression</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Êtes-vous sûr de vouloir supprimer cet avis ?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <form method="post" action="gestion_avis.php" style="display: inline;">
            <input type="hidden" name="avis_id" id="deleteAvisId">
            <button type="submit" name="confirm_delete" class="btn btn-danger">Confirmer</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
// Libération des résultats et fermeture de la connexion
$result->free();
$mysqli->close();
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // JavaScript pour passer l'ID de l'avis dans le modal
    document.addEventListener('DOMContentLoaded', function () {
        var deleteModal = document.getElementById('confirmDeleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var avisId = button.getAttribute('data-id');
            var inputAvisId = document.getElementById('deleteAvisId');
            inputAvisId.value = avisId;
        });
    });
</script>
</body>
</html>