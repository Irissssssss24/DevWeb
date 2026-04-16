<?php
// deconnexion.php
session_start();

// Supprime toutes les variables de session
$_SESSION = [];

// Détruit la session côté serveur
session_destroy();

header('Location: Portail_Connexion.php');
exit();
?>