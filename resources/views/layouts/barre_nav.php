<?php
// Récupération des informations de l'utilisateur depuis la session Laravel
$prenom = session('prenom');
$nom    = session('nom');
// On récupère le tableau des rôles (plusieurs rôles possibles)
$roles  = session('roles', []);

// On vérifie si l'utilisateur est connecté
$estConnecte = session()->has('user_id');

//on récupère la page actuelle 
$pageCourante = $pageCourante ?? '';

if (!function_exists('styleLien')) {
    function styleLien($page, $pageCourante) {
        if ($page === $pageCourante) {
            return 'class="lien-actif"';
        }
        return '';
    }
}
?>

<nav>
    <!-- Affichage du bienvenue uniquement si connecté -->
    <?php if ($estConnecte): ?>
    <div class="nav-info">
        <strong>Bienvenue, <?php echo htmlspecialchars($prenom . ' ' . $nom); ?></strong>
        (Rôle : <em><?php echo htmlspecialchars(implode(', ', $roles)); ?></em>)
    </div>
    <?php endif; ?>

    <ul>
        <!-- Affichage des liens selon le ou les rôles de l'utilisateur -->
        <?php if (in_array('etudiant', $roles)): ?>
            <li><a href="/etudiant" <?= styleLien('etudiant', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/offres" <?= styleLien('offres', $pageCourante) ?>>Voir les offres de stage</a></li>
            <li><a href="/etudiant" <?= styleLien('etudiant', $pageCourante) ?>>Mon avancement</a></li>
            <li><a href="/etudiant/profil" <?= styleLien('etudiant/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif (in_array('entreprise', $roles)): ?>
            <li><a href="/entreprise" <?= styleLien('entreprise', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/creer-offre" <?= styleLien('creer-offre', $pageCourante) ?>>Publier une offre</a></li>
            <li><a href="/entreprise" <?= styleLien('entreprise', $pageCourante) ?>>Voir les candidats</a></li>
            <li><a href="/entreprise/profil" <?= styleLien('entreprise/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif (in_array('tuteur', $roles)): ?>
            <li><a href="/tuteur" <?= styleLien('tuteur', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/tuteur" <?= styleLien('tuteur', $pageCourante) ?>>Suivre mes stagiaires</a></li>
            <li><a href="/tuteur/profil" <?= styleLien('tuteur/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif (in_array('jury', $roles)): ?>
            <li><a href="/jury" <?= styleLien('jury', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/jury" <?= styleLien('jury', $pageCourante) ?>>Évaluations</a></li>
            <li><a href="/jury/profil" <?= styleLien('jury/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php else: ?>
            <!-- Liens pour les visiteurs non connectés -->
            <li><a href="/accueil" <?= styleLien('accueil', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/offres" <?= styleLien('offres', $pageCourante) ?>>Voir les offres de stage</a></li>
            <li><a href="/connexion">Se connecter</a></li>
            <li><a href="/inscription">S'inscrire</a></li>
        <?php endif; ?>

        <!-- Lien de déconnexion — affiché uniquement si connecté -->
        <?php if ($estConnecte): ?>
            <li>
                <a href="/deconnexion" class="lien-deconnexion">Déconnexion</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>