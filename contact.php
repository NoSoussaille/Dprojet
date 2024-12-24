<?php
$title = "Contact - Zoo Arcadia";
$meta_description = "Contactez nous pour avoir plus d'informations";
require_once 'elements/header.php'; ?>

<dix section="mainpage">
<?php if (isset($_GET['message']) && $_GET['message'] == 'avis_envoye'): ?>
    <p class="alert alert-success">Merci pour votre avis !</p>
<?php endif; ?>
</section>
<section class="mainpage">
<?php if (isset($_GET['message'])): ?>
    <?php if ($_GET['message'] == 'message_envoye'): ?>
        <p class="alert alert-success">Votre message a été envoyé avec succès !</p>
    <?php elseif ($_GET['message'] == 'erreur_envoi'): ?>
        <p class="alert alert-danger">Une erreur s'est produite lors de l'envoi de votre message. Veuillez réessayer.</p>
    <?php elseif ($_GET['message'] == 'erreur_formulaire'): ?>
        <p class="alert alert-warning">Veuillez remplir tous les champs correctement.</p>
    <?php endif; ?>
<?php endif; ?>
</section>

<div class="mainpage">
   
   <img src="image/page d'accueil/Image.webp" alt="photo de l'entrée du zoo d'Arcadia" class="imgZoo">
   <?php
    // Vérification de la connexion déjà effectuée dans db_connection.php
    // Informations de connexion à la base de données
    require_once 'db_connection.php';
    // Récupère les horaires d'ouverture
    $query = "SELECT jour, ouverture, fermeture FROM horaires_ouverture ORDER BY FIELD(jour, 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche')";
    $result = $mysqli->query($query);

    // Génération du tableau des horaires
    $horaires = [];
    while ($row = $result->fetch_assoc()) {
        $jour = ucfirst($row['jour']);
        $ouverture = date('H:i', strtotime($row['ouverture']));
        $fermeture = date('H:i', strtotime($row['fermeture']));
        
        if ($ouverture === '00:00' && $fermeture === '00:00') {
            $horaires[$jour] = 'Fermé';
        } else {
            $horaires[$jour] = "de $ouverture à $fermeture";
        }
    }
    $result->free();
    $mysqli->close();
    ?>

    <div class="bienvenue2">
        <h3>Horaires d'ouverture</h3>
        <ul>
            <?php foreach ($horaires as $jour => $horaire): ?>
                <li><strong><?php echo $jour; ?></strong> : <?php echo $horaire; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
   <div class="mainpage">
   
   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d42676.09817185243!2d-2.2233505570440144!3d48.047392065857466!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480faceab3587495%3A0xcdc883e818be2eb2!2sFor%C3%AAt%20de%20Broc%C3%A9liande!5e0!3m2!1sfr!2sfr!4v1730032286274!5m2!1sfr!2sfr" width="400" height="300" style="border:0; border-radius: 20px;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe> 
        <p class="horaire">Zoo ARCADIA
            Près de la la foret de Brocéliande
            42, avenue Carre, Paimpont 35211

            Numero : 0404040404
            Email : zoo-arcadia@hotmail.fr
        </p>
   </div>
   <div class="title vert">
    <h2 class="sous-menu beige">Envoyez un Message</h2>
</div>
<div class="title vert">
    <form class="formulaire" method="POST" action="submit_contact.php">
        <fieldset id="contact">
            <legend><h4>Contact</h4></legend>

            <div id="div_nm">
                <label for="name">Nom :</label>
                <input type="text" name="nom" id="name" placeholder="Nom" required>
            </div>

            <div id="div_fnm">
                <label for="firstname">Prenom :</label>
                <input type="text" name="prenom" id="firstname" placeholder="Prenom" required>
            </div>

            <div>
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" placeholder="email@mail.com" required>
            </div>

            <textarea name="message" id="message" cols="30" rows="10" placeholder="Message..." required></textarea>
            <button type="submit" class="button">Envoyer</button>
        </fieldset>
    </form>
</div>
    <div class="title beige">
    <h2 class="sous-menu vert">Laissez un avis</h2>
</div>
<div class="title beige">
    <form class="formulaire" method="POST" action="submit_avis.php">
        <fieldset id="avis">
            <legend><h4>Avis</h4></legend>

            <div id="div_fnm">
                <label for="pseudo">Pseudo :</label>
                <input type="text" name="pseudo" id="pseudo" placeholder="Pseudo" required>
            </div>

            <textarea name="avis" id="avis" cols="30" rows="10" placeholder="Votre avis..." required></textarea>

            <button type="submit" class="button">Envoyer</button>
        </fieldset>
    </form>
</div>
   
<?php require_once 'elements/footer.php'; ?>
<script src="script.js"></script> 
</body>
</html>