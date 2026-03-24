<?php
// deconnexion.php
session_start();
session_unset();
session_destroy();

header('Location: Portail_Connexion.php');
exit();
?>