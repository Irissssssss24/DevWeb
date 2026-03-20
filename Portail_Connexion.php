<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Portail de Connexion</title>
        <link rel="stylesheet" href="Connexion_style.css">
    </head>
    <body>
        <div class="connexion">
        <h1>Portail de connexion</h1>
            <form id="formulaire" name="formulaire" action="verifierConnexion.php" method="post">
                <label id="id"><u>Identifiant</u> : <input type="text" class="form" id="id" name="id"></label><br>
                <label id="mdp"><u>Mot de passe</u> : <input type="password" class="form" id="mdp" name="mdp"></label><br>
                <input type="submit" value="Connexion" class="button">
            </form>
            <br>
            <a href="MotdePasseOublie.php">Mot de passe ou Identifiant oubliés</a>
            <br>
        </div>
    </body>
</html>
