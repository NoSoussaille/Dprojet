<?php
// Vérifiez que le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et filtrer les données du formulaire
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    // Vérifiez que toutes les données sont présentes
    if ($nom && $prenom && $email && $message) {
        // Préparer l'email
        $to = "zoo-arcadia@hotmail.fr";
        $subject = "Nouveau message de contact de $nom $prenom";
        $email_message = "Nom: $nom\n";
        $email_message .= "Prénom: $prenom\n";
        $email_message .= "Email: $email\n\n";
        $email_message .= "Message:\n$message\n";

        // Entêtes de l'email
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email\r\n";

        // Envoie l'email
        if (mail($to, $subject, $email_message, $headers)) {
            // Redirigez vers la page de contact avec un message de succès
            header("Location: contact.php?message=message_envoye");
            exit();
        } else {
            // Afficher un message d'erreur en cas d'échec
            header("Location: contact.php?message=erreur_envoi");
            exit();
        }
    } else {
        // Redirige en cas de données manquantes
        header("Location: contact.php?message=erreur_formulaire");
        exit();
    }
} else {
    // Si le formulaire n'est pas soumis via POST, redirigez vers la page de contact
    header("Location: contact.php");
    exit();
}