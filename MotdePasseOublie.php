<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" href="Adminstyle.css">
        <title>Réinitialiser mot de passe</title>
    </head>
    <body>
        <div class="reinitialiser">
        <h1>Réinitialiser votre mot de passe</h1>
        <h2>Vérifions votre identité :</h2>
        <form id="reponse" name ="reponse" action="verifQuestions.php" method="POST">
            <label class="label">Quelle est votre mail ?<input class="barre" type="text" id="mail" name="mail" required></label><br><br>
            <label class="label">Quelle est votre numéro ?<input class="barre" type="text" id="num" name="num" required></label><br><br>
            <label class="label">Quelle est votre couleur préférée ?<input class="barre" type="text" id="couleur" name="couleur"></label><br><br>
            <label class="label">Quelle est votre plat préféré ?<input class="barre" type="text" id="plat" name="plat"></label><br><br>
            <label class="label">Quelle est votre hobby préféré ?<input class="barre" type="text" id="hobby" name="hobby"></label><br><br>
            <label class="label">Quelle la marque de votre première voiture ?<input class="barre" type="text" id="marque" name="marque"></label><br><br>
            
            <input type="submit"  class="bouton" id="submit" name="submit" value="Valider">
        </form>
    </div>    
    <button class='bouton1'><a class='demande1' href='Portail_Connexion.php'>Retour</a></button>
    
    </body>
</html>
