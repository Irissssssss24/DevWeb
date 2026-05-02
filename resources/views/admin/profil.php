<html>
    <head>
    <meta charset="utf-8">
    <title>Mon Profil</title>
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
    <link rel="stylesheet" href="/css/Profilstyle.css">
    <link rel="stylesheet" href="/css/Adminstyle.css">
</head>
    <body>
<?php
$pageCourante = 'administrateur/profil';
include resource_path('views/layouts/barre_nav.php');

$prenom = session('prenom');
$nom    = session('nom');
$roles  = session('roles', []);
?>

    <div class="Profil">

        <form method="POST" action="/administrateur/profil/modifier">
        <?php echo csrf_field() ?? '' ?>
 
        <div class="coordonnees">
            <h3>Mes coordonnées</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Nom</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars(old('nom', session('nom'))) ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars(old('prenom', session('prenom'))) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars(old('email', session('email'))) ?>" required>
            </div>
            <div class="form-group">
                <label>Rôle(s)</label>
                <input type="text" value="<?= htmlspecialchars(implode(', ', session('roles', []))) ?>" disabled style="background:#f0f0f0;color:#666;">
            </div>
        </div>

        <div class="modification">
            <h3>Modifier le mot de passe :</h3>
            <a href="/changer-mdp" class="bouton">Changer mon mot de passe</a>
        </div>
    </div>

    </body>
</html>
