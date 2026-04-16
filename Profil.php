<?php
// On ne lance la session que si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur n'est pas connecté, on le renvoie vers la connexion
// Note : j'ai mis Portail_Connexion.php car c'est le nom dans ton verifConnexion
if (!isset($_SESSION['user_id'])) {
    header("Location: Portail_Connexion.php");
    exit();
}

// Récupération du rôle (on le force en minuscules pour être sûr de la comparaison)
$role = strtolower($_SESSION['role']);
?>
<html>
    <head>
        <title>Modification des données</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="Profilstyle.css">
    </head>
    <body>
    <div class="Profil">
                <h2>Mes coordonnées :</h2>
                <div class="coordonnees">
                    <p><strong>Nom :</strong> <?php echo htmlspecialchars($_SESSION['nom']); ?></p>
                    <p><strong>Prénom :</strong> <?php echo htmlspecialchars($_SESSION['prenom']); ?></p>
                    <p><strong>Email :</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                    <p><strong>Rôle :</strong> <?php echo htmlspecialchars($role); ?></p>  
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

