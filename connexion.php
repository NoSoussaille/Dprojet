<?php
$title = "Page de connection - Zoo Arcadia";
$meta_description = "Espace employé.";
require_once 'elements/header.php' ?>

<div class="title beige">
  <h2 class="sous-menu vert">Connexion</h2>
</div>
<div class="maineco1">
      <form action="login.php" method="post" class="formulaire">
          <fieldset id="contact">
              <div id="div_username">
                  <label for="username">Utilisateur :</label>
                  <input type="text" name="username" id="username" placeholder="Nom d'utilisateur" required>
              </div>

              <div id="div_password">
                  <label for="password">Mot de passe :</label>
                  <input type="password" name="mdp" id="mdp" placeholder="Mot de passe" style="border-radius: 20px; padding: 10px; border: 1px solid black;" required>
              </div>

              <button type="submit" class="button">Connexion</button>
              <?php
    // Affiche le message d'erreur si le paramètre 'error' est dans l'URL
    if (isset($_GET['error']) && $_GET['error'] == 1) {
        echo "<div class='alert alert-danger text-center' role='alert'>
        Nom d'utilisateur ou mot de passe incorrect.
      </div>";
    };

      if (isset($_GET['message']) && $_GET['message'] === 'logout_success') {
          echo "<p class='alert alert-success'>Vous avez été déconnecté avec succès.</p>";
      }
    ?>
          </fieldset>
      </form>
</div>

<script src="script.js"></script>
<script>
  // Supprime le paramètre "error" de l'URL après affichage
  if (window.location.search.includes("error=1")) {
      const url = new URL(window.location);
      url.searchParams.delete("error");
      window.history.replaceState({}, document.title, url.toString());
  }
</script>
<?php require_once 'elements/footer.php' ?>