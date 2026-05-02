<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Administrateur — MYstage</title>
    <!-- On inclut le fichier CSS global de l'admin et le nouveau fichier CSS dédié -->
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/Dashboard.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'administrateur';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="dashboard-content">
    <h1 class="dashboard-titre">Tableau de bord — Administration</h1>

    <?php if (session('success')): ?>
        <div class="alerte-succes">
            <?= htmlspecialchars(session('success')) ?>
        </div>
    <?php endif; ?>

    <!-- Statistiques globales -->
    <p class="section-titre">Vue d'ensemble de la plateforme</p>
    <div class="stats-grid">
        <div class="stat-card">
            <div class="nombre"><?= $totalUtilisateurs ?? 0 ?></div>
            <div class="label">Utilisateurs total</div>
        </div>
        <div class="stat-card">
            <div class="nombre"><?= $totalEtudiants ?? 0 ?></div>
            <div class="label">Étudiants</div>
        </div>
        <div class="stat-card">
            <div class="nombre"><?= $totalEntreprises ?? 0 ?></div>
            <div class="label">Entreprises</div>
        </div>
        <div class="stat-card">
            <div class="nombre"><?= $totalTuteurs ?? 0 ?></div>
            <div class="label">Tuteurs</div>
        </div>
        <div class="stat-card">
            <div class="nombre"><?= $totalJurys ?? 0 ?></div>
            <div class="label">Membres du jury</div>
        </div>
    </div>

   <div class="dashboard-content">

        <?php include __DIR__ . '/utilisateurs.php'; ?>
    </div>



</div>
</body>
</html>
