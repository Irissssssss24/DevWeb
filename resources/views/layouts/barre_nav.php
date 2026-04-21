<?php
// Récupération des informations de l'utilisateur depuis la session Laravel
$prenom = session('prenom');
$nom    = session('nom');
// On récupère le tableau des rôles (plusieurs rôles possibles)
$roles  = session('roles', []);
?>

<nav style="background: #0062AD; color: white; padding: 15px; border-bottom: 1px solid #ccc;">
    <div style="margin-bottom: 10px;">
        <!-- Affichage du prénom, nom et des rôles de l'utilisateur connecté -->
        <strong>Bienvenue, <?php echo htmlspecialchars($prenom . ' ' . $nom); ?></strong>
        (Rôle : <em><?php echo htmlspecialchars(implode(', ', $roles)); ?></em>)
    </div>
    <ul>
        <li><a href="/connexion">Accueil</a></li>

        <!-- Affichage des liens selon le ou les rôles de l'utilisateur -->
        <?php if (in_array('etudiant', $roles)): ?>
            <li><a href="/offres">Voir les offres de stage</a></li>
            <li><a href="/etudiant">Mon avancement</a></li>

        <?php elseif (in_array('entreprise', $roles)): ?>
            <li><a href="/creer-offre">Publier une offre</a></li>
            <li><a href="/entreprise">Voir les candidats</a></li>

        <?php elseif (in_array('tuteur', $roles)): ?>
            <li><a href="/tuteur">Suivre mes stagiaires</a></li>

        <?php elseif (in_array('jury', $roles)): ?>
            <li><a href="/jury">Évaluations</a></li>

        <?php endif; ?>

        <!-- Lien de déconnexion -->
        <li style="margin-bottom: 10px; list-style: none;">
            <a href="/deconnexion" style="color: red;">Déconnexion</a>
        </li>
    </ul>
</nav>
<hr>