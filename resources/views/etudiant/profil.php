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
$pageCourante = 'etudiant/profil';
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

        <div class="cv">
            <h3>Mon CV :</h3>

            <?php if ($cvExiste): ?>
                <p style="color: green; margin-bottom: 15px;">✅ CV déposé</p>

                <!-- Affichage sécurisé via la route Laravel -->
                <iframe 
                    src="/mon-cv" 
                    width="100%" 
                    height="500px" 
                    style="border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px;">
                    <p>Votre navigateur ne supporte pas l'affichage PDF. 
                        <a href="/mon-cv">Télécharger le CV</a>
                    </p>
                </iframe>

                <a href="/mon-cv" download class="bouton" style="display:inline-block; margin-bottom: 15px;">
                    Télécharger mon CV
                </a>
            <?php else: ?>
                <p style="color: #999; margin-bottom: 15px;">Aucun CV déposé pour le moment.</p>
            <?php endif; ?>

            <form action="/upload-cv" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <input type="file" name="cv" accept=".pdf">
                <button type="submit" name="depot_cv">
                    <?= $cvExiste ? 'Remplacer le CV' : 'Déposer le CV' ?>
                </button>
            </form>
        </div>

        <div class="modification">
            <h3>Modifier le mot de passe : </h3>

            

            <?php if (session('error')): ?>
                <p style="color:red;"><?php echo session('error'); ?></p>
            <?php endif; ?>

            <?php if (session('success')): ?>
                <p style="color:green;"><?php echo session('success'); ?></p>
            <?php endif; ?>

            <form method="POST" action="/changer-mdp">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <label for="nouveau">Nouveau mot de passe :</label><br>
                <input type="password" class="champ-saisie" placeholder="Votre Nouveau mot de passe" id="nouveau" name="nouveau" required minlength="8" autocomplete="new-password"><br><br>
                <label for="confirmer">Confirmer le mot de passe :</label><br>
                <input type="password" class="champ-saisie" placeholder="Votre Nouveau mot de passe" id="confirmer" name="confirmer" required minlength="8" autocomplete="new-password"><br><br>
                <button type="submit">Valider</button>
            </form>
        </div>
    </div>

    </body>
</html>