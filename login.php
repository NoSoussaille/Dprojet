<?php
// Démarrage de la session
session_start();
define('BASE_URL', '/');  // Modifiez selon le chemin racine de votre projet
// Détails de connexion à la base de données
require_once 'db_connection.php';

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifie que les champs username et mdp sont bien remplis
    if (!empty($_POST['username']) && !empty($_POST['mdp'])) {
        $username = $_POST['username'];
        $password = $_POST['mdp'];

        // Prépare la requête pour vérifier les identifiants et récupérer le rôle et le mot de passe haché
        $stmt = $mysqli->prepare("
            SELECT utilisateurs.id, utilisateurs.username, utilisateurs.mdp, roles.nom AS role
            FROM utilisateurs
            INNER JOIN utilisateur_roles ON utilisateurs.id = utilisateur_roles.utilisateur_id
            INNER JOIN roles ON utilisateur_roles.role_id = roles.id
            WHERE utilisateurs.username = ?
            LIMIT 1
        ");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Vérifie si un utilisateur est trouvé
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Vérifie le mot de passe haché
            if (password_verify($password, $user['mdp'])) {
                // Enregistre les informations de l'utilisateur dans la session
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Redirection vers le dashboard
                header("Location: dashbord/dashbord.php");
                exit();
            } else {
                // Mot de passe incorrect, redirection avec message d'erreur
                header("Location: connexion.php?error=1");
                exit();
            }
        } else {
            // Identifiants incorrects, redirection avec message d'erreur
            header("Location: connexion.php?error=1");
            exit();
        }

        // Ferme la requête
        $stmt->close();
    } else {
        // Champs manquants, redirection avec message d'erreur
        header("Location: connexion.php?error=1");
        exit();
    }
}

// Ferme la connexion à la base de données
$mysqli->close();
?>