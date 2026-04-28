<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <!-- Chemin vers fichier CSS -->
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>

        <!-- Affichage des messages de succès ou d'erreur -->
        <!-- utilisation fréquente dans Laravel -->
        <!-- remplace les $SESSION de PHP classique -->
        <?php if (session('success')): ?>
            <div class="alert alert-success">
                <?php echo session('success'); ?>
            </div>
        <?php endif; ?>

        <?php if (session('errors')): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach (session('errors')->all() as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <!--afficher les rôles-->
        <div class="cartes-roles">
        <div class="carte">
            <span class="carte-icon">🎓</span>
            <h3>Étudiant</h3>
            <p>Trouvez un stage, déposez vos documents, suivez votre avancement</p>
        </div>
        <div class="carte">
            <span class="carte-icon">🏢</span>
            <h3>Entreprise</h3>
            <p>Publiez des offres, gérez vos stagiaires</p>
        </div>
        <div class="carte">
            <span class="carte-icon">👨‍🏫</span>
            <h3>Tuteur</h3>
            <p>Suivez vos stagiaires, ajoutez des remarques</p>
        </div>
        <div class="carte">
            <span class="carte-icon">⚖️</span>
            <h3>Jury</h3>
            <p>Évaluez les soutenances et rapports</p>
        </div>
    </div>

        <!-- Formulaire d'inscription -->
        <form action="/inscription" method="POST">
            <!-- sert à protéger contre les attaques CSRF, Laravel génère un token unique pour chaque session utilisateur -->
            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>

            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>

            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe">Mot de passe :</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <div class="form-group">
                <label for="mot_de_passe_confirmation">Confirmer le mot de passe :</label>
                <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation" required>
            </div>

            <div class="form-group">
                <label for="role">Rôle :</label>
                <select id="role" name="role" required>
                    <option value="">Sélectionnez un rôle</option>
                    <option value="etudiant">Étudiant</option>
                    <option value="entreprise">Entreprise</option>
                    <option value="tuteur">Tuteur</option>
                    <option value="jury">Jury</option>
                    <option value="administrateur">Administrateur</option>
                </select>
            </div>

            <button type="submit">S'inscrire</button>
        </form>

        <p>Déjà un compte ? <a href="/connexion">Se connecter</a></p>
    </div>
</body>
</html>