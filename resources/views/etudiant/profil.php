
<?php
// Récupération des informations de l'utilisateur depuis la session Laravel
$prenom = session('prenom');
$nom    = session('nom');
// On récupère le tableau des rôles (plusieurs rôles possibles)
$roles  = session('roles', []);
?>
<html>
    <head>
        <title>Modification des données</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="/css/Profilstyle.css">
    </head>

    <body>

    <div class="Profil">

                <h2>Mes coordonnées :</h2>

                <div class="coordonnees">

                    <p><strong>Nom :</strong> <?php echo htmlspecialchars(session('nom')); ?></p>

                    <p><strong>Prénom :</strong> <?php echo htmlspecialchars(session('prenom')); ?></p>

                    <p><strong>Email :</strong> <?php echo htmlspecialchars(session('email')); ?></p>

                    <p><strong>Rôle :</strong><em><?php echo htmlspecialchars(implode(', ', $roles)); ?></em></p> 

                </div>

                <div class="cv">

                    <form action="upload.php" method="POST" enctype="multipart/form-data">

                        <h3>Mon cv :</h3>

                        <input type="file" name="Mon cv"> 

                        <button type="submit" name="depot_cv">Déposer le CV</button>

                    </form> 

                </div>

                <div class="modification">

                    <h3>Modifier le mot de passe : </h3>

                    <form method="post" action="modifMdp.php">

                        <label for="Mdp">Changer Mot de passe :</label><br>

                        <input type="text" id="Mdp" name="Mdp"><br>

                        <label for="Confirmer">Confirmer le mot de passe :</label><br>

                        <input type="text" id="Confirmer" name="Confirmer">

                        <button type="submit" class="valider">Valider</button>

                    </form>

                </div>

            </div>

        </div>

    </body>

</html>