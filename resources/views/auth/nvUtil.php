<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <div class="container">
        <h1>Inscription</h1>

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

        <!-- Étape 1 : Choix du rôle -->
        <div id="etape-role">
            <p style="text-align:center; color:#666; margin-bottom: 20px;">
                Choisissez votre rôle pour commencer l'inscription
            </p>
            <div class="cartes-roles">
                <div class="carte" data-role="etudiant">
                    <span class="carte-icon">🎓</span>
                    <h3>Étudiant</h3>
                    <p>Trouvez un stage, déposez vos documents, suivez votre avancement</p>
                </div>
                <div class="carte" data-role="entreprise">
                    <span class="carte-icon">🏢</span>
                    <h3>Entreprise</h3>
                    <p>Publiez des offres, gérez vos stagiaires</p>
                </div>
                <div class="carte" data-role="tuteur">
                    <span class="carte-icon">👨‍🏫</span>
                    <h3>Tuteur</h3>
                    <p>Suivez vos stagiaires, ajoutez des remarques</p>
                </div>
                <div class="carte" data-role="jury">
                    <span class="carte-icon">⚖️</span>
                    <h3>Jury</h3>
                    <p>Évaluez les soutenances et rapports</p>
                </div>
            </div>
        </div>

        <!-- Étape 2 : Formulaire (caché au départ) -->
        <div id="etape-formulaire" style="display: none;">

            <div id="role-selectionne" style="text-align:center; margin-bottom: 20px;">
                <!-- Affiche le rôle choisi -->
            </div>

            <form action="/inscription" method="POST">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" id="role-hidden" name="role" value="">

                <!-- Champs communs -->
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

                <!-- Champs spécifiques au rôle -->
                <div id="champs-specifiques"></div>

                <button type="submit">S'inscrire</button>
                <button type="button" id="btn-retour" style="margin-top: 10px;">
                    ← Changer de rôle
                </button>
            </form>

            
        </div>
        <div>
            <p>Déjà un compte ? <a href="/connexion">Se connecter</a></p>
            <a href="/connexion" class="lien-secondaire">Retour </a>
        </div>
    </div>

<script>
const champsSpecifiques = {
    etudiant: `
        <h3>Informations étudiant</h3>
        <div class="form-group">
            <label for="filiere">Filière :</label>
            <input type="text" id="filiere" name="filiere" placeholder="ex: Génie Informatique" required>
        </div>
        <div class="form-group">
            <label for="niveau">Niveau :</label>
            <select id="niveau" name="niveau" required>
                <option value="">Sélectionnez votre niveau</option>
                <option value="P1">P1</option>
                <option value="P2">P2</option>
                <option value="ING1">ING1</option>
                <option value="ING2">ING2</option>
                <option value="ING3">ING3</option>
            </select>
        </div>
    `,
    entreprise: `
        <h3>Informations entreprise</h3>
        <div class="form-group">
            <label for="nom_entreprise">Nom de l'entreprise :</label>
            <input type="text" id="nom_entreprise" name="nom_entreprise" placeholder="ex: Google France" required>
        </div>
        <div class="form-group">
            <label for="adresse">Adresse :</label>
            <input type="text" id="adresse" name="adresse" placeholder="ex: 8 Rue de Londres, Paris" required>
        </div>
        <div class="form-group">
            <label for="secteur">Secteur d'activité :</label>
            <input type="text" id="secteur" name="secteur" placeholder="ex: Informatique, Finance..." required>
        </div>
        <div class="form-group">
            <label for="siret">Numéro de Siret:</label>
            <input type="text" id="siret" name="siret" placeholder="ex: 784 671 695 00087" required>
        </div>
    `,
    tuteur: `
        <h3>Informations tuteur</h3>
        <div class="form-group">
            <label for="specialite">Spécialité :</label>
            <input type="text" id="specialite" name="specialite" placeholder="ex: Développement web, IA..." required>
        </div>
    `,
    jury: `
        <h3>Informations jury</h3>
        <p style="color: #666; font-size: 0.9rem;">Aucune information supplémentaire requise.</p>
    `,
    administrateur: `
        <h3>Informations administrateur</h3>
        <p style="color: #666; font-size: 0.9rem;">Aucune information supplémentaire requise.</p>
    `
};

const icones = {
    etudiant: '🎓 Étudiant',
    entreprise: '🏢 Entreprise',
    tuteur: '👨‍🏫 Tuteur',
    jury: '⚖️ Jury',
    administrateur: '🔧 Administrateur'
};

// Clic sur une carte
document.querySelectorAll('.carte').forEach(carte => {
    carte.style.cursor = 'pointer';
    carte.addEventListener('click', function() {
        const role = this.dataset.role;

        // Remplir le champ caché
        document.getElementById('role-hidden').value = role;


        // Afficher le rôle sélectionné
        document.getElementById('role-selectionne').innerHTML = `
            <span style="background: #e6f1fb; color: #0062AD; padding: 8px 20px; border-radius: 20px; font-weight: 500;">
                ${icones[role]}
            </span>
        `;

        // Injecter les champs spécifiques
        document.getElementById('champs-specifiques').innerHTML = champsSpecifiques[role] || '';

        // Cacher l'étape 1, afficher l'étape 2
        
        document.getElementById('etape-formulaire').style.display = 'block';
    });
});

// Bouton retour
document.getElementById('btn-retour').addEventListener('click', function() {
    document.getElementById('etape-formulaire').style.display = 'none';
    document.getElementById('etape-role').style.display = 'block';
    document.getElementById('role-hidden').value = '';
    document.getElementById('champs-specifiques').innerHTML = '';
});
</script>
</body>
</html>