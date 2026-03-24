<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styleMdpOublie.css">
    <title>Réinitialiser mot de passe</title>
</head>
<body>

    <div class="reinitialiser">
        <h1>Réinitialiser votre mot de passe</h1>
        <h2>Vérifions votre identité :</h2>

        <form id="reponse" name="reponse" action="verifIdentite.php" method="POST">
            
            <label class="label-formulaire">
                Quel est votre mail ?
                <input class="champ-saisie" type="email" id="mail" name="mail" required>
            </label>

            <label class="label-formulaire">
                Quel est votre nom ?
                <input class="champ-saisie" type="text" id="num" name="num" required>
            </label>

            <label class="label-formulaire">
                Quel est votre prénom ?
                <input class="champ-saisie" type="text" id="prenom" name="prenom" required>
            </label>

            <input type="submit" class="bouton-valider" id="submit" name="submit" value="Valider">
        </form>

        <button class="bouton-retour">
            <a class="lien-retour" href="Portail_Connexion.php">← Retour à la connexion</a>
        </button>
    </div>    
    
</body>
</html>