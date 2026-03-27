<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Portail_Connexion.php");
    exit();
}
// Récupération du rôle de l'utilisateur à partir de la session
$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Espace <?php echo $role; ?></title>
</head>
<body>
    <h1>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom']); ?></h1>
    <p>Vous êtes connecté en tant que : <strong><?php echo $role; ?></strong></p>

    <nav>
        <?php if ($role === 'etudiant'): ?>
            <ul>
                <li><a href="offres.php">Voir les offres de stage</a></li>
                <li><a href="mon_suivi.php">Mon avancement</a></li>
            </ul>
        <?php elseif ($role === 'Entreprise'): ?>
            <ul>
                <li><a href="creer_offre.php">Publier une offre</a></li>
                <li><a href="candidatures.php">Voir les candidats</a></li>

            </ul>
        <?php elseif ($role === 'Tuteur'): ?>
            <ul>
                <li><a href="suivi_eleves.php">Suivre mes stagiaires</a></li>
            </ul>
        <?php endif; ?>

    <hr>
    <a href="deconnexion.php">Déconnexion</a>
    </nav>
</body>
</html>