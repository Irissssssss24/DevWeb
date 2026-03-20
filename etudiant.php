<?php
session_start();

if (!isset($_SESSION["role"])){
    header('Location: Portail_Connexion.php');
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Page de <?php echo $_SESSION["nom"]. " ". $_SESSION["prénom"]?></title>
        <link rel="stylesheet" href="Adminstyle.css">
        <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    </head>
    <body>
        <!-- barre de navigation -->
        <?php
            include "barre_nav_admin.php";
        ?>
        <div class="statut">
            <h1>Ma demande de Stage</h1>
                <div class="demandesS">
                    <!-- à changer -->
                    <a class="lien" href="stage.php">
                        <button class="bouton"> <a class="remarque" >Ajouter une remarque </a></button>
                        <button class="bouton"> <a class="informer" >Informer le tuteur </a></button>
                    </a>
                </div>
        </div>
        <hr style="border: none; height: 2px; background-color: red; width: 73%;">
        <div class="documents">
            <h1>Dépôt des Documents</h1>
                <div class="demandesS">
                    <button class="bouton"> <a class="rapport" href ="<?php echo $RapportDeStage ?>"  download="rapport_de_stage"> Déposer </a></button>
                    <button class="bouton"> <a class="eval" href ="<?php echo $RapportDeStage ?>"  download="rapport_de_stage"> Déposer </a></button>
                    <button class="bouton"> <a class="resume" href ="<?php echo $RapportDeStage ?>"  download="rapport_de_stage"> Déposer </a></button>
                </div>
        </div>
    </body>
</html>

