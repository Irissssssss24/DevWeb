<?php
session_start();
$_SESSION["identifiant"]=$_POST['id'];
$_SESSION["motdepasse"]=$_POST['mdp'];


if (($handle = fopen("infos_Salarié.csv", "r"))) {
   while (($data = fgetcsv($handle, 1000, ";"))) {


    if ($data[6] == $_POST['id'] && password_verify($_POST['mdp'], $data[7])) {
            $_SESSION["nom"]=$data[0];
            $_SESSION["prénom"]=$data[1];
            $_SESSION["date_de_naissance"]=$data[2];
            $_SESSION["mail"]=$data[3];
            $_SESSION["téléphone"]=$data[4];
            $_SESSION["num_sécu"]=$data[5];
            $_SESSION["salariés"] = $_SESSION["nom"]. " ".$_SESSION["prénom"] ;
            $_SESSION["section"] = $data[8];
            $_SESSION["identifiant"]=$data[6];
            $_SESSION["motSecret"]=$data[7];
            $_SESSION["statut"]="salarie";
            $_SESSION["role"]="salarie";
            header('Location: premiereCo.php');
            exit();
        }
    }
    fclose($handle);
}
if (($handle1 = fopen("infos_Admin1.csv", "r"))) {
    while (($data = fgetcsv($handle1, 1000, ";"))) {
        if ($data[6]==$_POST['id'] && password_verify($_POST['mdp'], $data[7])){
            $_SESSION["nom"]=$data[0];
            $_SESSION["prénom"]=$data[1];
            $_SESSION["date_de_naissance"]=$data[2];
            $_SESSION["mail"]=$data[3];
            $_SESSION["téléphone"]=$data[4];
            $_SESSION["num_sécu"]=$data[5];
            $_SESSION["admin1"] = $_SESSION["nom"]. " ".$_SESSION["prénom"];
            $_SESSION["identifiant"]=$data[6];
            $_SESSION["motSecret"]=$data[7];
            $_SESSION["role"]="admin1";
            header('Location: admin1.php');
            exit();
        }
    }
    fclose($handle1);
}
if (($handle = fopen("infos_Admin.csv", "r"))) {
    while (($data = fgetcsv($handle, 1000, ";"))) {
        
        if ($data[6]==$_POST['id'] && password_verify($_POST['mdp'], $data[7])){
            $_SESSION["nom"]=$data[0];
            $_SESSION["prénom"]=$data[1];
            $_SESSION["date_de_naissance"]=$data[2];
            $_SESSION["mail"]=$data[3];
            $_SESSION["téléphone"]=$data[4];
            $_SESSION["num_sécu"]=$data[5];
            $_SESSION["admin"] = $_SESSION["nom"]. " ".$_SESSION["prénom"];
            $_SESSION["identifiant"]=$data[6];
            $_SESSION["motSecret"]=$data[7];
            $_SESSION["role"]="admin";
            header('Location: admin.php');
            exit();
        }
        
    }
    fclose($handle);
}

if (($handle = fopen("delegation.csv", "r"))) {
    while (($data = fgetcsv($handle, 1000, ";"))) {
        
        if ($data[6]==$_POST['id'] && password_verify($_POST['mdp'], $data[7])){
            $_SESSION["nom"]=$data[0];
            $_SESSION["prénom"]=$data[1];
            $_SESSION["date_de_naissance"]=$data[2];
            $_SESSION["mail"]=$data[3];
            $_SESSION["téléphone"]=$data[4];
            $_SESSION["num_sécu"]=$data[5];
            $_SESSION["delegation"] = $_SESSION["nom"]. " ".$_SESSION["prénom"];

            $_SESSION["identifiant"]=$data[6];
            $_SESSION["motSecret"]=$data[7];
            $_SESSION["role"]="delegation";
            header('Location: delegation.php');
            exit();
        }
        
    }
    fclose($handle);
}



header('Location: Portail_Connexion.php');
?>