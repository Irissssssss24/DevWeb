<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Changer mon mot de passe — ProjetStage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
</head>
<body>

    <h1>Changer mon mot de passe</h1>
    <p>Choisissez un nouveau mot de passe (8 caractères minimum).</p>

    <!-- Affichage des erreurs -->
    <?php if (session('error')): ?>
        <p style="color:red;"><?php echo session('error'); ?></p>
    <?php endif; ?>

    <!-- Affichage du succès -->
    <?php if (session('success')): ?>
        <p style="color:green;"><?php echo session('success'); ?></p>
    <?php endif; ?>

    <form method="POST" action="/changer-mdp">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">



        <label for="nouveau">Nouveau mot de passe :</label><br>
        <input type="password" id="nouveau" name="nouveau" required minlength="8" autocomplete="new-password"><br><br>

        <label for="confirmer">Confirmer le mot de passe :</label><br>
        <input type="password" id="confirmer" name="confirmer" required minlength="8" autocomplete="new-password"><br><br>

        <button type="submit">Valider</button>
    </form>

</body>
</html>