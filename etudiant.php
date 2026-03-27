
<?php
session_start();

// --- BLOC DE TEST TEMPORAIRE ---
// On commente la sécurité pour ne pas être redirigé
/*
if (!isset($_SESSION["role"])){
    header('Location: Portail_Connexion.php');
}
*/

// On remplit la session avec des fausses données pour que la page s'affiche
$_SESSION['user_id'] = 1;
$_SESSION["nom"] = "DUPONT";
$_SESSION["prenom"] = "Jean";
$_SESSION["role"] = "etudiant";
// ------------------------------
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Page de <?php echo $_SESSION["nom"]. " ". $_SESSION["prenom"]?></title>
        <link rel="stylesheet" href="Adminstyle.css">
        <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    </head>
<body>
    <?php include "barre_nav.php"; ?>

    <main> 
        <div class="partie-gauche">
            
            <div class="statut">
                <h1>Mon Stage Actuel</h1>
                <div class="demandesS">
                    <a class="bouton" href="ajoutremarque.php">Ajouter une remarque</a>
                    <a class="bouton" href="informetuteur.php">Informer le tuteur</a>
                </div>
            </div>

            <div class="documents">
                <h1>Dépôt des Documents</h1>
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="ligne">
                        <p>Rapport de stage :</p>
                        <input type="file" name="rapportstage">
                        <button type="submit" name="depot_rapport">Déposer le rapport</button>
                    </div>

                    <div class="ligne">
                        <p>Fiche D'evaluation :</p>
                        <input type="file" name="fiche_eval">
                        <button type="submit" name="depot_ficheeval">Déposer la fiche</button>
                    </div>

                    <div class="ligne">
                        <p>Resume de stage :</p>
                        <input type="file" name="resum_stage">
                        <button type="submit" name="depot_resumstage">Déposer le resumé</button>
                    </div>
                </form>
            </div>

        </div> 
        <div class="cahierStage">
            <h1>Mon Cahier de Stage</h1>
            <form method="post" action="journalnotes.php">
                <p>
                    <label for="note">Saisir une note :</label><br>
                    <textarea name="note" id="note" rows="15"></textarea>
                    <button type="submit" class="valider">Ajouter au journal</button>
                </p>
            </form>
        </div>

    </main>
</body>
</html>

