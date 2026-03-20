<?php
session_start();

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
    </head>
    <body>
        <h1>Veuillez changer votre mot de passe</h1>
        <form id="reponse" name ="reponse" action="changerMdpsurFichier.php" method="POST">
            <input type="text" id="mdp" name="mdp">
            <input type="hidden" id="mail" name="mail" value=<?php echo $_GET["mail"] ?>>
            <?php
                switch($_GET["statut"]) {
                    case "salarie":
                        echo '<input type="hidden" id="statut" name="statut" value="salarie">';
                        break;
                    case "admin1":
                        echo '<input type="hidden" id="statut" name="statut" value="admin1">';
                        break;
                    case "admin":
                        echo '<input type="hidden" id="statut" name="statut" value="admin">';
                        break;
                    case "alternant":
                        echo '<input type="hidden" id="statut" name="statut" value="alternant">';
                        break;
                    case "delegation":
                        echo '<input type="hidden" id="statut" name="statut" value="delegation">';
                        break;
                    }
            ?>
            <input type="submit" id="submit" name="submit" value="Valider">
        </form>
        <a href="Portail_Connexion.php">Annuler</a> 
    </body>
</html>
