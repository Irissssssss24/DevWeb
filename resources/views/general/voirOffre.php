<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Offres de stage — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/offreStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'offres';
$recherche = request()->input('recherche', '');
include resource_path('views/layouts/barre_nav.php');
?>

<div class="contenu-offre">
    <h2>Offres de stage disponibles</h2>

    <?php if (session('success')): ?>
        <div class="message-succes"><?= htmlspecialchars(session('success')) ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="message-erreur"><?= htmlspecialchars(session('error')) ?></div>
    <?php endif; ?>

    <!-- Barre de recherche -->
    <form method="GET" action="/offres" class="form-recherche">
        <div class="recherche-wrapper">
            <input 
                type="text" 
                name="recherche" 
                placeholder="Rechercher par titre, compétence, entreprise, secteur..."
                value="<?= htmlspecialchars($recherche) ?>"
                class="champ-recherche"
            >
            <button type="submit" class="btn-recherche">Rechercher</button>
            <?php if ($recherche): ?>
                <a href="/offres" class="btn-reset">✕ Effacer</a>
            <?php endif; ?>
        </div>
    </form>

    <?php if ($recherche): ?>
        <p style="color: #666; margin-bottom: 15px;">
            Résultats pour <strong>"<?= htmlspecialchars($recherche) ?>"</strong> — 
            <?= count($offres) ?> offre(s) trouvée(s)
        </p>
    <?php else: ?>
        <p style="color: #666; margin-bottom: 20px;">
            <?= count($offres) ?> offre(s) disponible(s)
        </p>
    <?php endif; ?>

    <?php if (count($offres) === 0): ?>
        <div style="text-align: center; padding: 3rem; color: #999;">
            <p style="font-size: 1.1rem; margin-bottom: 8px;">Aucune offre trouvée</p>
            <?php if ($recherche): ?>
                <p style="font-size: 0.9rem;">Essayez avec d'autres mots-clés ou <a href="/offres">voir toutes les offres</a></p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="liste-offres">
            <?php foreach ($offres as $offre): ?>
                <div class="carte-offre">
                    <div class="carte-offre-header">
                        <div>
                            <h3><?= htmlspecialchars($offre->titre) ?></h3>
                            <p class="entreprise-nom">
                                🏢 <?= htmlspecialchars($offre->entreprise->nom_entreprise ?? 'Entreprise inconnue') ?>
                                <?php if ($offre->entreprise->secteur ?? null): ?>
                                    <span class="badge-secteur"><?= htmlspecialchars($offre->entreprise->secteur) ?></span>
                                <?php endif; ?>
                            </p>
                        </div>
                        <span class="badge-duree">⏱ <?= htmlspecialchars($offre->duree) ?></span>
                    </div>

                    <p class="offre-description"><?= htmlspecialchars($offre->description) ?></p>

                    <?php if ($offre->competences): ?>
                        <div class="competences">
                            <?php foreach (explode(',', $offre->competences) as $comp): ?>
                                <span class="badge-competence"><?= htmlspecialchars(trim($comp)) ?></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <div class="carte-offre-footer">
                        <div>
                            <strong style="color: #0062AD;">Missions :</strong>
                            <p style="margin: 5px 0 0; color: #555; font-size: 0.9rem;">
                                <?= nl2br(htmlspecialchars($offre->missions)) ?>
                            </p>
                        </div>

                        <?php if (session('role_actif') === 'etudiant'): ?>
                            <a href="<?= route('postuler.index', $offre->id_offre) ?>" class="btn-postuler">
                                Postuler
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>
