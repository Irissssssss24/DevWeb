<?php
// On inclut la barre de navigation
include resource_path('views/layouts/barre_nav.php');



// Simulation des chemins de fichiers (À remplacer plus tard par une requête SQL sur la table 'document')
$RapportDeStage = "#"; 
?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Page de <?php echo htmlspecialchars(session('nom') . " " . session('prenom')); ?></title> 
        <link rel="stylesheet" href="/css/Adminstyle.css">
        <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    </head>
<body>
    <main> 
        <div class="partie-gauche">
            
            <div class="statut">
                <h1>Mon Stage Actuel</h1>
                <div class="demandesS">
                    <a class="bouton" href="/ajout-remarque">Ajouter une remarque</a>
                    <a class="bouton" href="/informe-tuteur">Informer le tuteur</a>
                </div>
            </div>

            <div class="documents">
                <h1>Dépôt des Documents</h1>
                <form action="/upload" method="POST" enctype="multipart/form-data">
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
            <form method="post" action="/journal-notes">
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