<?php
session_start();
if (!isset($_SESSION["role"])){
    header('Location: Portail_Connexion.php');
}
//verifie si premiere connexion
if (($handle = fopen("premiereCo.csv", "r"))) {
   while (($data = fgetcsv($handle, 1000, ";"))) {
        if ($_SESSION["identifiant"]==$data[0] && $_SESSION["motSecret"]==$data[1]) {
            fclose($handle);
            header('Location: changerMdpPremiereCo.php?id='. $_SESSION["identifiant"] .'');
            exit();
        }
    }
}    


fclose($handle);
header('Location: salariés.php');
exit();






?>