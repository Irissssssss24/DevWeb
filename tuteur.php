<?php
// On inclut la barre de navigation qui gère déjà le session_start() et la vérification de connexion
include "barre_nav.php";
// Sécurité supplémentaire : vérifier que c'est bien un tuteur
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tuteur') {
    header('Location: Portail_Connexion.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Page de <?php echo htmlspecialchars($_SESSION["nom"] . " " . $_SESSION["prenom"]); ?></title> 
        <link rel="stylesheet" href="Adminstyle.css">
        <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    </head>
<body>
    <h1>Bienvenue sur votre espace tuteur, <?php echo htmlspecialchars($_SESSION["prenom"]); ?> !</h1>
    <p>Voici les fonctionnalités disponibles pour vous :</p>


    <p><a href="deconnexion.php">Se déconnecter</a></p>
</body>
</html>