<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Candidatures — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/candidaturesStyle.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageCourante = 'entreprise';
include resource_path('views/layouts/barre_nav.php');
?>

<div class="contenu-candidatures">
    <h2>Candidatures reçues</h2>

    <?php if (session('success')): ?>
        <div class="message-succes"><?= session('success') ?></div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="message-erreur"><?= session('error') ?></div>
    <?php endif; ?>

    <?php if (count($stages) === 0): ?>
        <p class="aucune-candidature">Aucune candidature reçue pour le moment.</p>
    <?php else: ?>

        <?php $parOffre = $stages->groupBy('id_offre'); ?>

        <?php foreach ($parOffre as $idOffre => $candidatures): ?>
            <?php $offre = $candidatures->first()->offre; ?>
            <div class="groupe-offre">
                <h3>
                    <?= htmlspecialchars($offre->titre) ?>
                    <span class="badge-count"><?= count($candidatures) ?> candidature(s)</span>
                </h3>

                <?php foreach ($candidatures as $stage): ?>
                    <?php
                    $etudiant = $stage->etudiant;
                    $user = $etudiant->utilisateur ?? null;
                    $classeStatut = match($stage->statut) {
                        "en attente d'acceptation" => 'statut-en-attente',
                        'accepté'                  => 'statut-accepte',
                        'refusé'                   => 'statut-refuse',
                        default                    => ''
                    };
                    $badgeStatut = match($stage->statut) {
                        "en attente d'acceptation" => 'en-attente',
                        'accepté'                  => 'accepte',
                        'refusé'                   => 'refuse',
                        default                    => ''
                    };
                    ?>

                    <div class="carte-candidature <?= $classeStatut ?>">
                        <div class="candidature-header">
                            <div class="candidat-info">
                                <div class="candidat-avatar">
                                    <?= strtoupper(substr($user->prenom ?? 'E', 0, 1) . substr($user->nom ?? '', 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="candidat-nom"><?= htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?></p>
                                    <p class="candidat-email"><?= htmlspecialchars($user->email ?? '') ?></p>
                                    <p class="candidat-filiere">
                                        <?= htmlspecialchars($etudiant->filiere ?? '') ?>
                                        <?php if ($etudiant->niveau ?? null): ?>
                                            — <?= htmlspecialchars($etudiant->niveau) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <span class="badge-statut badge-<?= $badgeStatut ?>">
                                <?= htmlspecialchars($stage->statut) ?>
                            </span>
                        </div>

                        <!-- Documents -->
                        <div class="documents-candidature">
                            <?php if ($user): ?>
                                <a href="/candidature/cv/<?= $user->id_utilisateur ?>" 
                                   target="_blank" class="btn-document">
                                    Voir le CV
                                </a>
                                <?php if ($stage->lettre_motivation): ?>
                                    <a href="/candidature/lettre/<?= $user->id_utilisateur ?>" 
                                       target="_blank" class="btn-document">
                                        Voir la lettre
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Actions selon le statut -->
                        <?php if ($stage->statut === "en attente d'acceptation"): ?>
                            <div class="actions-candidature">
                                <form method="POST" action="/candidature/proposer-dates/<?= $stage->id_stage ?>" class="form-accepter">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <div class="dates-form">
                                        <div class="groupe-date">
                                            <label>Date de début proposée</label>
                                            <input type="date" name="date_debut" required>
                                        </div>
                                        <div class="groupe-date">
                                            <label>Date de fin proposée</label>
                                            <input type="date" name="date_fin" required>
                                        </div>
                                    </div>
                                    <div class="boutons-actions">
                                        <button type="submit" class="btn-accepter">Proposer des dates</button>
                                    </div>
                                </form>
                                <form method="POST" action="/candidature/refuser/<?= $stage->id_stage ?>">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <button type="submit" class="btn-refuser" onclick="return confirm('Confirmer le refus ?')">❌ Refuser</button>
                                </form>
                            </div>

                        <?php elseif ($stage->statut === 'convention soumise'): ?>
                            <div class="info-dates">
                                <p>📅 Du <strong><?= date('d/m/Y', strtotime($stage->date_debut_proposee)) ?></strong>
                                au <strong><?= date('d/m/Y', strtotime($stage->date_fin_proposee)) ?></strong></p>
                            </div>
                            <div class="actions-candidature" style="margin-top: 12px;">
                                <a href="/candidature/convention/<?= $stage->id_stage ?>" target="_blank" class="btn-document">
                                    Voir la convention
                                </a>
                                <form method="POST" action="/candidature/convention-signee/<?= $stage->id_stage ?>" enctype="multipart/form-data" style="display:flex; gap:8px; align-items:center;">
                                    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                    <input type="file" name="convention_signee" accept=".pdf" required>
                                    <button type="submit" class="btn-accepter">Envoyer convention signée</button>
                                </form>
                            </div>

                        <?php elseif ($stage->statut === 'en attente validation admin'): ?>
                            <div class="info-dates">
                                <p>En attente de validation par l'administrateur</p>
                            </div>

                        <?php elseif ($stage->statut === 'accepté'): ?>
                            <div class="info-dates">
                                <p>📅 Du <strong><?= date('d/m/Y', strtotime($stage->date_debut)) ?></strong>
                                au <strong><?= date('d/m/Y', strtotime($stage->date_fin)) ?></strong></p>
                            </div>
                        <?php endif; ?>
                    </div>

                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>
</body>
</html>