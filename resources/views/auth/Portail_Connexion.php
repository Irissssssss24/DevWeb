<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — ProjetStage</title>
    <link rel="stylesheet" href="/css/stylePortail.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>

    <div class="conteneur-connexion">
        <h1>ProjetStage — Connexion</h1>

        <?php
        $errorMessage = session()->get('error', '');
        $messages = [
            'champs_vides'        => 'Veuillez remplir tous les champs.',
            'identifiants_invalides' => 'Email ou mot de passe incorrect.',
            'role_invalide'       => 'Vous n\'avez pas ce rôle.',
            'role_inconnu'        => 'Aucun rôle trouvé pour ce compte.',
        ];
        $errorMessage = $messages[$errorMessage] ?? $errorMessage;
        $successMessage = session()->get('success', '');
        ?>

        <?php if ($errorMessage): ?>
            <div class="alerte alerte-erreur">
                <strong>Erreur:</strong> <?php echo htmlspecialchars($errorMessage); ?>
            </div>
        <?php endif; ?>

        <?php if ($successMessage): ?>
            <div class="alerte alerte-succes">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>
        <?php endif; ?>
 
        <form action="/connexion" method="POST">
            <!-- génère un token unique pour chaque session utilisateur -->
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
 
            <div class="groupe-formulaire">
                <label for="email">Adresse e-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="champ-saisie"
                    placeholder="nom@exemple.fr"
                    required
                    autocomplete="email"
                    value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>"
                >
            </div>
 
            <div class="groupe-formulaire">
                <label for="password">Mot de passe</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="champ-saisie"
                    placeholder="Votre mot de passe"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="groupe-formulaire">
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


 
            <button type="submit" class="bouton-connexion">Se connecter</button>
 
        </form>
 
        <div class="liens-bas">
            <a href="/changer-mdp" class="lien-secondaire">Mot de passe oublié ?</a>
            <br>
            <a href="/inscription" class="lien-secondaire">S'inscrire</a>
            <br>
            <a href="/accueil" class="lien-secondaire">Retour à l'accueil</a>
        </div>
    </div>

</body>
</html>