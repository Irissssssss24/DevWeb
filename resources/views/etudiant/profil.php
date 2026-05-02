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
 
        <!-- Formulaire modification profil -->
    <form method="POST" action="/etudiant/profil/modifier">
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
            <div class="form-row">
                <div class="form-group">
                    <label>Filière</label>
                    <input type="text" name="filiere" value="<?= htmlspecialchars(old('filiere', $etudiant->filiere ?? '')) ?>" placeholder="Ex: Informatique">
                </div>
                <div class="form-group">
                    <label>Niveau</label>
                    <select name="niveau">
                        <option value="">— Choisir —</option>
                        <?php foreach (['P1','P2','ING1','ING2','ING3'] as $n): ?>
                            <option value="<?= $n ?>" <?= ($etudiant->niveau ?? '') === $n ? 'selected' : '' ?>><?= $n ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Messages -->
            <?php if (session('success')): ?>
                <div style="background:#eafaf1; color:#27ae60; padding:10px 15px; border-left:4px solid #27ae60; border-radius:5px; margin: 20px 0;">
                    <?= session('success') ?>
                </div>
            <?php endif; ?>

            <?php 
            $erreurs = session('errors');
            if ($erreurs && $erreurs->any()): 
            ?>
                <div style="background:#fdecea; color:#c0392b; padding:10px 15px; border-left:4px solid #c0392b; border-radius:5px; margin: 20px 0;">
                    <ul style="margin:0; padding-left:15px;">
                        <?php foreach ($erreurs->all() as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Bouton submit -->
            <div style="margin-top: 20px; text-align: left;,padding-bottom:20px;">
                <button type="submit"> Enregistrer les modifications</button>
            </div>
        </div>
        </form>
 
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
            <h3>Modifier le mot de passe :</h3>
            <a href="/changer-mdp" class="bouton">Changer mon mot de passe</a>
        </div>
    </div>
 
    </body>
</html>