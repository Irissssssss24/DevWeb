<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Accueil — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/accueilStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>


<div class="hero">
    <img src="/images/CY_Tech.png" alt="CY Tech" class="hero-logo">
    <h1>MYstage</h1>
    <p class="hero-subtitle">La plateforme de gestion des stages de CY Tech</p>
    <?php if (!session()->has('user_id')): ?>
    <div class="hero-boutons">
        <a href="/connexion" class="btn-blanc">Se connecter</a>
        <a href="/inscription" class="btn-contour">S'inscrire</a>
    </div>
    <?php endif; ?>
</div>

<div class="contenu">

    <p class="intro">
        MYstage centralise le suivi des stages pour les étudiants, entreprises, tuteurs et jurys de CY Tech — de la recherche d'offre jusqu'à l'évaluation finale.
    </p>

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

    <div class="etapes">
        <h2>Comment ça fonctionne ?</h2>
        <div class="etape">
            <div class="etape-num">1</div>
            <div>
                <p class="etape-titre">Créez votre compte</p>
                <p class="etape-desc">Inscrivez-vous en choisissant votre rôle : étudiant, entreprise, tuteur ou jury.</p>
            </div>
        </div>
        <div class="etape">
            <div class="etape-num">2</div>
            <div>
                <p class="etape-titre">Accédez à votre espace</p>
                <p class="etape-desc">Chaque rôle dispose d'un espace dédié avec les outils adaptés à vos besoins.</p>
            </div>
        </div>
        <div class="etape">
            <div class="etape-num">3</div>
            <div>
                <p class="etape-titre">Gérez votre stage</p>
                <p class="etape-desc">Déposez vos documents, communiquez avec votre tuteur et suivez votre progression.</p>
            </div>
        </div>
    </div>

    <footer class="pied">
        <p>CY Tech — CY Cergy Paris Université &nbsp;·&nbsp; Tous droits réservés <?= date('Y') ?></p>
    </footer>

</div>
</body>
</html>