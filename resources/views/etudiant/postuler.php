<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Postuler — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/offreStyle.css">
    <link rel="stylesheet" href="/css/postulerStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'offres';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="contenu-postuler">

    <!-- Récapitulatif de l'offre -->
    <div class="recap-offre">
        <h2>Postuler à l'offre</h2>
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
        </div>
    </div>

    <!-- Messages -->
    <?php if (session('error')): ?>
        <div class="message-erreur"><?= session('error') ?></div>
    <?php endif; ?>

    <?php if (session('success')): ?>
        <div class="message-succes"><?= session('success') ?></div>
    <?php endif; ?>

    <?php if ($dejaPostule): ?>
        <div class="message-erreur">
            Vous avez déjà postulé à cette offre.
            <a href="/offres">Retour aux offres</a>
        </div>
    <?php endif; ?>
    

    <!-- Formulaire de candidature -->
    <div class="formulaire-candidature">
        <h2>Vos documents</h2>

        <form method="POST" action="<?= route('postuler.store', $offre->id_offre) ?>" enctype="multipart/form-data">
            <input type="hidden" name="_token" value="<?= csrf_token() ?>">

            <!-- CV -->
            <div class="document-section <?= $cvExiste ? 'document-ok' : 'document-manquant' ?>">
                <div class="document-header">
                    <div>
                        <h3>
                            <?php if ($cvExiste): ?>
                                ✅ Curriculum Vitae
                            <?php else: ?>
                                ⚠️ Curriculum Vitae
                            <?php endif; ?>
                        </h3>
                        <?php if ($cvExiste): ?>
                            <p class="document-statut ok">CV déjà déposé — vous pouvez le remplacer si besoin</p>
                        <?php else: ?>
                            <p class="document-statut manquant">Aucun CV trouvé — veuillez en déposer un</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($cvExiste): ?>
                        <a href="/mon-cv" target="_blank" class="btn-voir">Voir mon CV</a>
                    <?php endif; ?>
                </div>
                <input type="file" name="cv" accept=".pdf"
                       <?= !$cvExiste ? 'required' : '' ?>>
                <small>Format PDF uniquement<?= $cvExiste ? ' (optionnel si vous gardez l\'actuel)' : '' ?></small>
            </div>

            <!-- Lettre de motivation -->
            <div class="document-section <?= $lmExiste ? 'document-ok' : 'document-manquant' ?>">
                <div class="document-header">
                    <div>
                        <h3>
                            <?php if ($lmExiste): ?>
                                ✅ Lettre de motivation
                            <?php else: ?>
                                ⚠️ Lettre de motivation
                            <?php endif; ?>
                        </h3>
                        <?php if ($lmExiste): ?>
                            <p class="document-statut ok">Lettre déjà déposée — vous pouvez la remplacer si besoin</p>
                        <?php else: ?>
                            <p class="document-statut manquant">Aucune lettre trouvée — veuillez en déposer une</p>
                        <?php endif; ?>
                    </div>
                    <?php if ($lmExiste): ?>
                        <a href="/ma-lettre" target="_blank" class="btn-voir">Voir ma lettre</a>
                    <?php endif; ?>
                </div>
                <input type="file" name="lettre_motivation" accept=".pdf"
                       <?= !$lmExiste ? 'required' : '' ?>>
                <small>Format PDF uniquement<?= $lmExiste ? ' (optionnel si vous gardez l\'actuelle)' : '' ?></small>
            </div>

            <div class="boutons-form">
                <a href="/offres" class="btn-annuler">Annuler</a>
                <button type="submit">
                    <?= ($cvExiste && $lmExiste) ? 'Envoyer ma candidature' : 'Déposer les documents et postuler' ?>
                </button>
            </div>
        </form>
    </div>

</div>
</body>
</html>
