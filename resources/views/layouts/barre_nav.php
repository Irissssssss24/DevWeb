<?php
// Récupération des informations de l'utilisateur depuis la session Laravel
$prenom = session('prenom');
$nom    = session('nom');
// On récupère le tableau des rôles (plusieurs rôles possibles)
$roles  = session('roles', []);
$role_actif = session('role_actif');

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
    <!-- Partie gauche : logo + nom app + bienvenue -->
    <div class="nav-gauche">
        <img src="/images/CY_Tech.png" class="images" alt="CY Tech"/>
        <span class="nav-app-name">MYstage</span>
        <?php if ($estConnecte): ?>
        <div class="nav-info">
            <strong>Bienvenue, <?php echo htmlspecialchars($prenom . ' ' . $nom); ?></strong>
            (Rôle : <em><?php echo htmlspecialchars($role_actif); ?></em>)
        </div>
        <?php endif; ?>
    </div>

    <!-- Partie droite : liens de navigation -->
    <ul>
        <?php if ('etudiant'=== $role_actif): ?>
            <li><a href="/etudiant" <?= styleLien('etudiant', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/offres" <?= styleLien('offres', $pageCourante) ?>>Voir les offres de stage</a></li>
            <li><a href="/etudiant/avancement" <?= styleLien('avancement', $pageCourante) ?>>Mon avancement</a></li>
            <li><a href="/etudiant/profil" <?= styleLien('etudiant/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif ('entreprise'=== $role_actif): ?>
            <li><a href="/entreprise" <?= styleLien('entreprise', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/creer-offre" <?= styleLien('creer-offre', $pageCourante) ?>>Publier une offre</a></li>
            <li><a href="/entreprise" <?= styleLien('entreprise', $pageCourante) ?>>Voir les candidats</a></li>
            <li><a href="/entreprise/profil" <?= styleLien('entreprise/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif ('tuteur'=== $role_actif): ?>
            <li><a href="/tuteur" <?= styleLien('tuteur', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/tuteur" <?= styleLien('tuteur', $pageCourante) ?>>Suivre mes stagiaires</a></li>
            <li><a href="/tuteur/profil" <?= styleLien('tuteur/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif ('jury'=== $role_actif): ?>
            <li><a href="/jury" <?= styleLien('jury', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/jury" <?= styleLien('jury', $pageCourante) ?>>Évaluations</a></li>
            <li><a href="/jury/profil" <?= styleLien('jury/profil', $pageCourante) ?>>Mon profil</a></li>

        <?php elseif ($role_actif === 'administrateur'): ?>
            <li><a href="/administrateur" <?= styleLien('administrateur', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/administrateur/validation" <?= styleLien('administrateur/validation', $pageCourante) ?>>Validation stages</a></li>
            <li><a href="/administrateur/profil" <?= styleLien('administrateur/profil', $pageCourante) ?>>Mon profil</a></li>


        <?php else: ?>
            <li><a href="/accueil" <?= styleLien('accueil', $pageCourante) ?>>Accueil</a></li>
            <li><a href="/offres" <?= styleLien('offres', $pageCourante) ?>>Voir les offres de stage</a></li>
            <li><a href="/connexion" class="lien-connexion">Se connecter</a></li>
            <li><a href="/inscription" class="lien-inscription">S'inscrire</a></li>
        <?php endif; ?>

        <!--bouton de switch entre les roles dispo-->
        <?php if ($estConnecte && count($roles) > 1): ?>
            <li class="switch-role" id="switch-role">
                <span class="switch-role-label" onclick="toggleDropdown()">Rôle actif ▾</span>
                <ul class="dropdown-roles" id="dropdown-roles">
                    <?php foreach ($roles as $r): ?>
                        <li>
                            <a href="/switch-role/<?= $r ?>" 
                            class="<?= $r === $role_actif ? 'role-actif-item' : '' ?>">
                                <?= ucfirst($r) ?>
                                <?php if ($r === $role_actif): ?> ✓<?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endif; ?>

        <?php if ($estConnecte): ?>
            <li>
                <a href="/deconnexion" class="lien-deconnexion">Déconnexion</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

<script>
function toggleDropdown() {
    var dropdown = document.getElementById('dropdown-roles');
    if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
    } else {
        dropdown.style.display = 'block';
    }
}

// Fermer le dropdown si on clique ailleurs
document.addEventListener('click', function(e) {
    var switchRole = document.getElementById('switch-role');
    if (switchRole && !switchRole.contains(e.target)) {
        document.getElementById('dropdown-roles').style.display = 'none';
    }
});
</script>