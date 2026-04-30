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
// On inclut la barre de navigation
$pageCourante = 'entreprise/profil';
include resource_path('views/layouts/barre_nav.php');
 
// Récupération des informations de l'utilisateur depuis la session Laravel
$prenom = session('prenom');
$nom    = session('nom');
$roles  = session('roles', []);
?>
 
    <div class="Profil">
        <h2>Mes coordonnées :</h2>
 
        <div class="coordonnees">
            <p><strong>Nom :</strong> <?php echo htmlspecialchars(session('nom')); ?></p>
            <p><strong>Prénom :</strong> <?php echo htmlspecialchars(session('prenom')); ?></p>
            <p><strong>Email :</strong> <?php echo htmlspecialchars(session('email')); ?></p>
            <p><strong>Rôle :</strong><em><?php echo htmlspecialchars(implode(', ', $roles)); ?></em></p> 
        </div>
 
        <?php 
        $idUtilisateur = session('user_id');
        $cheminCV = storage_path('app/private/Documents/' . $idUtilisateur . '/CV.pdf');
        $cvExiste = file_exists($cheminCV);
        ?>
 
        
 
        <div class="modification">
            <h3>Modifier le mot de passe :</h3>
            <a href="/changer-mdp" class="bouton">Changer mon mot de passe</a>
        </div>
    </div>
 
    </body>
</html>