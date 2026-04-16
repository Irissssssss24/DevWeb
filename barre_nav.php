<?php
// On ne lance la session que si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si l'utilisateur n'est pas connecté, on le renvoie vers la connexion
// Note : j'ai mis Portail_Connexion.php car c'est le nom dans ton verifConnexion
if (!isset($_SESSION['user_id'])) {
    header("Location: Portail_Connexion.php");
    exit();
}

// Récupération du rôle (on le force en minuscules pour être sûr de la comparaison)
$role = strtolower($_SESSION['role']);
?>

<nav style="background: #0062AD;color: white; padding: 15px; border-bottom: 1px solid #ccc;">
    <div style="margin-bottom: 10px;">
        <strong>Bienvenue, <?php echo htmlspecialchars($_SESSION['prenom'] . ' ' . $_SESSION['nom']); ?></strong> 
        (Rôle : <em><?php echo htmlspecialchars($role); ?></em>)
    </div>
    <ul>
        <li><a href="Portail_Connexion.php">Accueil</a></li>
        <li><a href="Profil.php">Mon Profil</a></li>
        <?php if ($role === 'etudiant'): ?>
            <li><a href="offres.php">Voir les offres de stage</a></li>
            <li><a href="etudiant.php">Mon avancement</a></li>
            
        <?php elseif ($role === 'entreprise'): ?>
            <li><a href="creer_offre.php">Publier une offre</a></li>
            <li><a href="entreprise.php">Voir les candidats</a></li>
            
        <?php elseif ($role === 'tuteur'): ?>
            <li><a href="tuteur.php">Suivre mes stagiaires</a></li>
            
        <?php elseif ($role === 'jury'): ?>
            <li><a href="jury.php">Évaluations</a></li>
            
        <?php endif; ?>

        <li style="margin-top: 10px; list-style: none;">
            <a href="deconnexion.php" style="color: red;">Déconnexion</a>
        </li>
    </ul>
</nav>
<hr>
