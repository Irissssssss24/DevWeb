<?php
// On inclut la barre de navigation qui gère déjà le session_start() et la vérification de connexion
include "barre_nav.php";

// Sécurité supplémentaire : vérifier que c'est bien un étudiant
if ($_SESSION['role'] !== 'etudiant') {
    header('Location: Portail_Connexion.php');
    exit();
}

// Simulation des chemins de fichiers (À remplacer plus tard par une requête SQL sur la table 'document')
$RapportDeStage = "#"; 
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
        
        <div class="statut">
            <h1>Ma demande de Stage</h1>
            <div class="demandesS">
                <a class="lien" href="stage.php">
                    <button class="bouton">Ajouter une remarque</button>
                </a>
                <button class="bouton" onclick="alert('Notification envoyée au tuteur')">Informer le tuteur</button>
            </div>
        </div>

        <hr style="border: none; height: 2px; background-color: red; width: 73%; margin: 20px auto;">

        <div class="documents">
            <h1>Dépôt des Documents</h1>
            <div class="demandesS">
                <div class="doc-item">
                    <p>Rapport de stage</p>
                    <button class="bouton"> 
                        <a class="rapport" href="<?php echo $RapportDeStage ?>" download> Déposer / Télécharger </a>
                    </button>
                </div>
                
                <div class="doc-item">
                    <p>Évaluation</p>
                    <button class="bouton"> 
                        <a class="eval" href="#" download> Déposer </a>
                    </button>
                </div>

                <div class="doc-item">
                    <p>Résumé</p>
                    <button class="bouton"> 
                        <a class="resume" href="#" download> Déposer </a>
                    </button>
                </div>
            </div>
        </div>
    </body>
</html>