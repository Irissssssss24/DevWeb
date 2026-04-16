<?php
session_start();

if (!isset($_SESSION["role"])){
    header('Location: Portail_Connexion.php');
}
?>
