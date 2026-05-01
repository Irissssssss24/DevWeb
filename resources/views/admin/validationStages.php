<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Validation des stages — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/candidaturesStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'administrateur';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="contenu-candidatures">
    <h2>Stages en attente de validation</h2>

    <?php if (session('success')): ?>
        <div class="message-succes"><?= session('success') ?></div>
    <?php endif; ?>

    <?php if (count($stages) === 0): ?>
        <p class="aucune-candidature">Aucun stage en attente de validation.</p>
    <?php else: ?>
        <?php foreach ($stages as $stage): ?>
            <?php
            $etudiant = $stage->etudiant;
            $user = $etudiant->utilisateur ?? null;
            $offre = $stage->offre;
            $entreprise = $offre->entreprise ?? null;
            ?>
            <div class="carte-candidature statut-en-attente">
                <div class="candidature-header">
                    <div class="candidat-info">
                        <div class="candidat-avatar">
                            <?= strtoupper(substr($user->prenom ?? 'E', 0, 1) . substr($user->nom ?? '', 0, 1)) ?>
                        </div>
                        <div>
                            <p class="candidat-nom"><?= htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?></p>
                            <p class="candidat-email"><?= htmlspecialchars($user->email ?? '') ?></p>
                            <p class="candidat-filiere">
                                Stage : <strong><?= htmlspecialchars($offre->titre ?? '') ?></strong>
                                chez <?= htmlspecialchars($entreprise->nom_entreprise ?? '') ?>
                            </p>
                            <p class="candidat-filiere">
                                📅 Du <?= date('d/m/Y', strtotime($stage->date_debut_proposee)) ?>
                                au <?= date('d/m/Y', strtotime($stage->date_fin_proposee)) ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="documents-candidature">
                    <a href="/candidature/cv/<?= $user->id_utilisateur ?>" target="_blank" class="btn-document">
                        📄 CV
                    </a>
                    <a href="/candidature/lettre/<?= $user->id_utilisateur ?>" target="_blank" class="btn-document">
                        ✉️ Lettre de motivation
                    </a>
                    <a href="/administrateur/convention/<?= $stage->id_stage ?>" target="_blank" class="btn-document">
                        📋 Convention signée
                    </a>
                </div>

                <div class="actions-candidature" style="margin-top: 12px;">
                    <form method="POST" action="/administrateur/valider/<?= $stage->id_stage ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-accepter">✅ Valider le stage</button>
                    </form>
                    <form method="POST" action="/administrateur/refuser/<?= $stage->id_stage ?>">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <button type="submit" class="btn-refuser" onclick="return confirm('Confirmer le refus ?')">
                            ❌ Refuser
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
