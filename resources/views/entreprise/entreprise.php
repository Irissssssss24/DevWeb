<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Espace Entreprise — MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/entrepriseHistorique.css">
    <link href="https://fonts.googleapis.com/css?family=Inter" rel="stylesheet">
</head>
<body>
<?php
$pageMode = $pageMode ?? 'accueil';
$estPageCandidatures = $pageMode === 'candidatures';
$pageCourante = $estPageCandidatures ? 'entreprise-candidatures' : 'entreprise';
$toutesLesOffres = $toutesLesOffres ?? collect();
$filtreOffreId = $filtreOffreId ?? null;
$statutsAcceptesVue = [
    'en attente convention',
    'convention soumise',
    'en attente validation admin',
    'accepté',
    'en_cours',
    'validé',
];
include resource_path('views/layouts/barre_nav.php');
?>

<div class="hist-wrapper">

    <!-- En-tête page -->
    <div class="hist-header">
        <div>
            <h1 class="hist-title"><?= $estPageCandidatures ? 'Voir les candidats' : 'Candidats acceptés' ?></h1>
            <p class="hist-subtitle">
                <?= $estPageCandidatures
                    ? 'Filtrez les candidatures reçues selon une offre publiée'
                    : 'Accueil entreprise avec uniquement les étudiants acceptés sur vos offres' ?>
            </p>
        </div>
        <a href="/creer-offre" class="btn-publier">+ Publier une offre</a>
    </div>

    <?php if ($estPageCandidatures): ?>
        <form method="GET" action="/entreprise/candidatures" class="filtre-candidats">
            <label for="offre">Offre sélectionnée</label>
            <div class="filtre-ligne">
                <select name="offre" id="offre">
                    <option value="">Toutes les offres</option>
                    <?php foreach ($toutesLesOffres as $offreFiltre): ?>
                        <option value="<?= $offreFiltre->id_offre ?>" <?= (string) $filtreOffreId === (string) $offreFiltre->id_offre ? 'selected' : '' ?>>
                            <?= htmlspecialchars($offreFiltre->titre) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn-filtrer">Filtrer</button>
                <?php if ($filtreOffreId): ?>
                    <a href="/entreprise/candidatures" class="btn-reset-filtre">Réinitialiser</a>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>

    <!-- Messages flash -->
    <?php if (session('success')): ?>
        <div class="flash flash-succes"><?= htmlspecialchars(session('success')) ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="flash flash-erreur"><?= htmlspecialchars(session('error')) ?></div>
    <?php endif; ?>

    <?php if ($offres->isEmpty()): ?>
        <div class="vide">
            <span class="vide-icon">📋</span>
            <?php if ($estPageCandidatures): ?>
                <p>Aucune candidature trouvée pour cette sélection.</p>
                <?php if ($toutesLesOffres->isEmpty()): ?>
                    <a href="/creer-offre" class="btn-publier">Publier votre première offre</a>
                <?php else: ?>
                    <a href="/entreprise/candidatures" class="btn-publier">Voir toutes les offres</a>
                <?php endif; ?>
            <?php else: ?>
                <p>Aucun candidat accepté pour le moment.</p>
                <a href="/entreprise/candidatures" class="btn-publier">Voir les candidatures</a>
            <?php endif; ?>
        </div>
    <?php else: ?>

        <?php foreach ($offres as $offre): ?>
            <?php
            $nbCandidatures = $offre->stages->count();
            $nbAcceptees    = $offre->stages->filter(fn($stage) => in_array($stage->statut, $statutsAcceptesVue, true))->count();
            $nbRefusees     = $offre->stages->where('statut', 'refusé')->count();
            $nbEnAttente    = $offre->stages->filter(fn($stage) => in_array($stage->statut, ["en attente d'acceptation", 'en_attente', 'dates proposées'], true))->count();
            ?>

            <div class="offre-bloc">
                <!-- Titre de l'offre + stats -->
                <div class="offre-titre-row">
                    <div class="offre-titre-info">
                        <h2 class="offre-titre"><?= htmlspecialchars($offre->titre) ?></h2>
                        <span class="offre-duree">⏱ <?= htmlspecialchars($offre->duree) ?></span>
                        <span class="offre-date">Publiée le <?= $offre->created_at ? $offre->created_at->format('d/m/Y') : 'date inconnue' ?></span>
                    </div>
                    <div class="offre-stats">
                        <span class="stat-badge stat-total">
                            <?= $nbCandidatures ?> <?= $estPageCandidatures ? 'candidature' : 'candidat accepté' ?><?= $nbCandidatures > 1 ? 's' : '' ?>
                        </span>
                        <?php if ($estPageCandidatures && $nbEnAttente > 0): ?>
                            <span class="stat-badge stat-attente"><?= $nbEnAttente ?> en attente</span>
                        <?php endif; ?>
                        <?php if ($nbAcceptees > 0): ?>
                            <span class="stat-badge stat-accepte"><?= $nbAcceptees ?> acceptée<?= $nbAcceptees > 1 ? 's' : '' ?></span>
                        <?php endif; ?>
                        <?php if ($estPageCandidatures && $nbRefusees > 0): ?>
                            <span class="stat-badge stat-refuse"><?= $nbRefusees ?> refusée<?= $nbRefusees > 1 ? 's' : '' ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Liste des candidatures -->
                <?php if ($nbCandidatures === 0): ?>
                    <p class="aucune-cand">
                        <?= $estPageCandidatures ? 'Aucune candidature pour cette offre.' : 'Aucun candidat accepté pour cette offre.' ?>
                    </p>
                <?php else: ?>
                    <div class="candidatures-liste">
                        <?php foreach ($offre->stages as $stage): ?>
                            <?php
                            $etudiant = $stage->etudiant;
                            $user     = $etudiant->utilisateur ?? null;
                            $initiales = strtoupper(
                                substr($user->prenom ?? 'E', 0, 1) .
                                substr($user->nom ?? '', 0, 1)
                            );

                            // Détermination du statut visuel
                            $estEnAttente = in_array($stage->statut, ["en attente d'acceptation", 'en_attente'], true);
                            $datesProposees = $stage->statut === 'dates proposées';
                            $estAccepte = in_array($stage->statut, $statutsAcceptesVue, true);
                            $statutInfo = match(true) {
                                $estEnAttente             => ['cls' => 'statut-attente',  'label' => 'En attente',      'icon' => '⏳'],
                                $datesProposees           => ['cls' => 'statut-attente',  'label' => 'Dates proposées', 'icon' => '📅'],
                                $stage->statut === 'refusé' => ['cls' => 'statut-refuse',   'label' => 'Refusé',          'icon' => '❌'],
                                $estAccepte               => ['cls' => 'statut-accepte',  'label' => 'Validé des deux côtés', 'icon' => '✅'],
                                default                   => ['cls' => '',                'label' => $stage->statut ?? 'Statut inconnu','icon' => ''],
                            };

                            // Vérification si la convention existe
                            $idUtil = $user->id_utilisateur ?? null;
                            $cheminConventionStage = $stage->convention
                                ? storage_path('app/private/Documents/' . $stage->convention)
                                : null;
                            $conventionExiste = ($cheminConventionStage && file_exists($cheminConventionStage))
                                || ($idUtil && file_exists(storage_path('app/private/Documents/' . $idUtil . '/ConventionDeStage.pdf')));

                            // Documents de stage déposés
                            $docsDeposes = $stage->documents ?? collect();

                            // Statut convention
                            $convStatut = null;
                            if ($estAccepte) {
                                if (!$conventionExiste) {
                                    $convStatut = 'absent';
                                } elseif (is_null($stage->convention_validee)) {
                                    $convStatut = 'en_attente';
                                } elseif ($stage->convention_validee) {
                                    $convStatut = 'validee';
                                } else {
                                    $convStatut = 'refusee';
                                }
                            }
                            ?>

                            <div class="cand-carte <?= $statutInfo['cls'] ?>">
                                <!-- Ligne principale : avatar + info + statut -->
                                <div class="cand-ligne-principale">
                                    <div class="cand-avatar"><?= $initiales ?></div>

                                    <div class="cand-info">
                                        <p class="cand-nom"><?= htmlspecialchars(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?></p>
                                        <p class="cand-email"><?= htmlspecialchars($user->email ?? '') ?></p>
                                        <p class="cand-filiere">
                                            <?= htmlspecialchars($etudiant->filiere ?? 'Filière non renseignée') ?>
                                            <?php if ($etudiant->niveau ?? null): ?>
                                                — <?= htmlspecialchars($etudiant->niveau) ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>

                                    <div class="cand-statut-badge <?= $statutInfo['cls'] ?>-badge">
                                        <?= $statutInfo['icon'] ?> <?= $statutInfo['label'] ?>
                                    </div>
                                </div>

                                <!-- Documents de candidature -->
                                <div class="cand-docs-candidature">
                                    <?php if ($idUtil): ?>
                                        <a href="/candidature/cv/<?= $idUtil ?>" target="_blank" class="btn-doc btn-cv">📄 CV</a>
                                        <?php if ($stage->lettre_motivation): ?>
                                            <a href="/candidature/lettre/<?= $idUtil ?>" target="_blank" class="btn-doc btn-lettre">✉️ Lettre de motivation</a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <!-- Actions si en attente -->
                                <?php if ($estEnAttente): ?>
                                    <div class="cand-actions">
                                        <form method="POST" action="/candidature/accepter/<?= $stage->id_stage ?>" class="form-dates">
                                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                            <div class="dates-inline">
                                                <div class="date-group">
                                                    <label>Début</label>
                                                    <input type="date" name="date_debut" required>
                                                </div>
                                                <div class="date-group">
                                                    <label>Fin</label>
                                                    <input type="date" name="date_fin" required>
                                                </div>
                                                <button type="submit" class="btn-action btn-accepter">✅ Accepter</button>
                                            </div>
                                        </form>
                                        <form method="POST" action="/candidature/refuser/<?= $stage->id_stage ?>">
                                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                            <button type="submit" class="btn-action btn-refuser"
                                                    onclick="return confirm('Confirmer le refus de cette candidature ?')">
                                                ❌ Refuser
                                            </button>
                                        </form>
                                    </div>

                                <?php elseif ($datesProposees): ?>
                                    <div class="cand-refuse-info">
                                        <p class="refuse-msg">Les dates ont été proposées. En attente de validation par l'étudiant.</p>
                                    </div>

                                <!-- Si accepté : afficher les dates + gestion convention + documents + remarques -->
                                <?php elseif ($estAccepte): ?>
                                    <div class="cand-accepte-details">

                                        <!-- Dates du stage -->
                                        <div class="info-dates">
                                            📅 Stage du <strong><?= $stage->date_debut ? $stage->date_debut->format('d/m/Y') : 'date à définir' ?></strong>
                                            au <strong><?= $stage->date_fin ? $stage->date_fin->format('d/m/Y') : 'date à définir' ?></strong>
                                        </div>

                                        <!-- ===== BLOC PROFIL ===== -->
                                        <div class="section-bloc">
                                            <h4 class="section-titre">👤 Profil de l'étudiant accepté</h4>
                                            <div class="profil-accepte">
                                                <div>
                                                    <span class="profil-label">Nom</span>
                                                    <strong><?= htmlspecialchars(trim(($user->prenom ?? '') . ' ' . ($user->nom ?? '')) ?: 'Non renseigné') ?></strong>
                                                </div>
                                                <div>
                                                    <span class="profil-label">Email</span>
                                                    <strong><?= htmlspecialchars($user->email ?? 'Non renseigné') ?></strong>
                                                </div>
                                                <div>
                                                    <span class="profil-label">Filière</span>
                                                    <strong><?= htmlspecialchars($etudiant->filiere ?? 'Non renseignée') ?></strong>
                                                </div>
                                                <div>
                                                    <span class="profil-label">Niveau</span>
                                                    <strong><?= htmlspecialchars($etudiant->niveau ?? 'Non renseigné') ?></strong>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- ===== BLOC CONVENTION ===== -->
                                        <div class="section-bloc">
                                            <h4 class="section-titre">📜 Convention de stage</h4>

                                            <?php if ($convStatut === 'absent'): ?>
                                                <div class="conv-absent">
                                                    <span class="conv-badge conv-absent-badge">⚠️ Non déposée</span>
                                                    <p class="conv-desc">L'étudiant n'a pas encore déposé sa convention de stage.</p>
                                                </div>

                                            <?php elseif ($convStatut === 'en_attente'): ?>
                                                <div class="conv-en-attente">
                                                    <a href="/candidature/convention-stage/<?= $stage->id_stage ?>" target="_blank" class="btn-doc btn-convention">📜 Voir la convention</a>
                                                    <div class="conv-actions">
                                                        <form method="POST" action="/convention/valider/<?= $stage->id_stage ?>">
                                                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                            <button type="submit" class="btn-action btn-valider-conv">✅ Valider la convention</button>
                                                        </form>
                                                        <form method="POST" action="/convention/refuser/<?= $stage->id_stage ?>"
                                                              onsubmit="return validerRefusConvention(this)">
                                                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                            <input type="hidden" name="remarque_convention" class="input-remarque-conv" value="">
                                                            <button type="submit" class="btn-action btn-refuser-conv">❌ Refuser la convention</button>
                                                        </form>
                                                    </div>
                                                </div>

                                            <?php elseif ($convStatut === 'validee'): ?>
                                                <div class="conv-validee">
                                                    <span class="conv-badge conv-ok-badge">✅ Convention validée</span>
                                                    <a href="/candidature/convention-stage/<?= $stage->id_stage ?>" target="_blank" class="btn-doc btn-convention">📜 Voir la convention</a>
                                                </div>

                                            <?php elseif ($convStatut === 'refusee'): ?>
                                                <div class="conv-refusee">
                                                    <span class="conv-badge conv-refus-badge">❌ Convention refusée</span>
                                                    <?php if ($stage->remarque_convention): ?>
                                                        <p class="conv-remarque">Motif : <?= htmlspecialchars($stage->remarque_convention) ?></p>
                                                    <?php endif; ?>
                                                    <a href="/candidature/convention-stage/<?= $stage->id_stage ?>" target="_blank" class="btn-doc btn-convention">📜 Voir la convention</a>
                                                    <!-- Permettre de re-valider après correction -->
                                                    <form method="POST" action="/convention/valider/<?= $stage->id_stage ?>" style="display:inline;">
                                                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                        <button type="submit" class="btn-action btn-valider-conv">✅ Valider quand même</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- ===== BLOC DOCUMENTS DE STAGE ===== -->
                                        <div class="section-bloc">
                                            <h4 class="section-titre">📁 Documents de stage</h4>
                                            <?php
                                            $typesDoc = [
                                                'rapport'    => ['label' => 'Rapport de stage',    'icon' => '📝'],
                                                'convention' => ['label' => 'Convention de stage', 'icon' => '📜'],
                                                'evaluation' => ['label' => 'Fiche d\'évaluation', 'icon' => '📋'],
                                                'resume'     => ['label' => 'Résumé de stage',     'icon' => '📄'],
                                            ];
                                            $typesDeposes = $docsDeposes->pluck('type')->toArray();
                                            ?>
                                            <div class="docs-grille">
                                                <?php foreach ($typesDoc as $typeKey => $typeInfo): ?>
                                                    <?php $depose = in_array($typeKey, $typesDeposes); ?>
                                                    <div class="doc-item <?= $depose ? 'doc-present' : 'doc-absent' ?>">
                                                        <span class="doc-icon"><?= $typeInfo['icon'] ?></span>
                                                        <span class="doc-label"><?= $typeInfo['label'] ?></span>
                                                        <?php if ($depose && $idUtil): ?>
                                                            <a href="/candidature/document/<?= $idUtil ?>/<?= $typeKey ?>"
                                                               target="_blank" class="doc-voir">Voir</a>
                                                        <?php else: ?>
                                                            <span class="doc-manquant">Non déposé</span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>

                                        <!-- ===== BLOC REMARQUES ===== -->
                                        <div class="section-bloc">
                                            <h4 class="section-titre">💬 Remarques pour l'étudiant</h4>

                                            <!-- Historique des remarques existantes -->
                                            <?php $remarques = $stage->remarques ?? collect(); ?>
                                            <?php if ($remarques->isNotEmpty()): ?>
                                                <div class="remarques-liste">
                                                    <?php foreach ($remarques as $rem): ?>
                                                        <div class="remarque-item">
                                                            <div class="rem-meta">
                                                                <span class="rem-auteur">Vous</span>
                                                                <span class="rem-date"><?= $rem->date?->format('d/m/Y à H:i') ?></span>
                                                            </div>
                                                            <p class="rem-contenu"><?= nl2br(htmlspecialchars($rem->contenu)) ?></p>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="aucune-remarque">Aucune remarque pour le moment.</p>
                                            <?php endif; ?>

                                            <!-- Formulaire ajout remarque -->
                                            <div class="form-remarque-wrapper">
                                                <button class="btn-toggle-remarque"
                                                        onclick="toggleRemarque(<?= $stage->id_stage ?>)">
                                                    ✏️ Ajouter une remarque
                                                </button>
                                                <div id="form-remarque-<?= $stage->id_stage ?>" class="form-remarque" style="display:none;">
                                                    <form method="POST" action="/candidature/remarque/<?= $stage->id_stage ?>">
                                                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                                                        <textarea name="contenu" placeholder="Votre remarque pour l'étudiant..." rows="3" required></textarea>
                                                        <div class="form-rem-actions">
                                                            <button type="submit" class="btn-action btn-envoyer-rem">Envoyer la remarque</button>
                                                            <button type="button" class="btn-annuler"
                                                                    onclick="toggleRemarque(<?= $stage->id_stage ?>)">Annuler</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                    </div><!-- /cand-accepte-details -->

                                <?php elseif ($stage->statut === 'refusé'): ?>
                                    <div class="cand-refuse-info">
                                        <p class="refuse-msg">Cette candidature a été refusée.</p>
                                    </div>
                                <?php endif; ?>

                            </div><!-- /cand-carte -->
                        <?php endforeach; ?>
                    </div><!-- /candidatures-liste -->
                <?php endif; ?>

            </div><!-- /offre-bloc -->
        <?php endforeach; ?>

    <?php endif; ?>

</div><!-- /hist-wrapper -->

<script>
function toggleRemarque(idStage) {
    const form = document.getElementById('form-remarque-' + idStage);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Demande une remarque avant de refuser la convention
function validerRefusConvention(form) {
    const motif = prompt('Motif du refus de la convention (optionnel) :');
    if (motif === null) return false; // Annulé
    form.querySelector('.input-remarque-conv').value = motif;
    return true;
}
</script>

</body>
</html>
