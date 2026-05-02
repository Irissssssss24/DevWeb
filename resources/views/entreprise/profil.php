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
 
            <form method="POST" action="/entreprise/profil/modifier">
        <?php echo csrf_field() ?? '' ?>
 
        <div class="coordonnees">
            <h3>Vos coordonnées</h3>
            <div class="form-row">
                <div class="form-group">
                    <label>Nom du contact</label>
                    <input type="text" name="nom" value="<?= htmlspecialchars(old('nom', session('nom'))) ?>" required>
                </div>
                <div class="form-group">
                    <label>Prénom du contact</label>
                    <input type="text" name="prenom" value="<?= htmlspecialchars(old('prenom', session('prenom'))) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars(old('email', session('email'))) ?>" required>
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
 
        <div class="section-entreprise">
            <h3>Informations entreprise</h3>
            <div class="form-group">
                <label>Nom de l'entreprise</label>
                <input type="text" name="nom_entreprise" value="<?= htmlspecialchars(old('nom_entreprise', $entreprise->nom_entreprise ?? '')) ?>">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Secteur d'activité</label>
                    <input type="text" name="secteur" value="<?= htmlspecialchars(old('secteur', $entreprise->secteur ?? '')) ?>">
                </div>
                <div class="form-group">
                    <label>SIRET</label>
                    <input type="text" name="siret" value="<?= htmlspecialchars(old('siret', $entreprise->siret ?? '')) ?>" maxlength="14">
                </div>
            </div>
            <div class="form-group">
                <label>Adresse</label>
                <input type="text" name="adresse" value="<?= htmlspecialchars(old('adresse', $entreprise->adresse ?? '')) ?>">
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
 
        
 
        <div class="modification">
            <h3>Modifier le mot de passe :</h3>
            <a href="/changer-mdp" class="bouton">Changer mon mot de passe</a>
        </div>
    </div>
 
    </body>
</html>