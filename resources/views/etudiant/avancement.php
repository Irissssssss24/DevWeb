<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Avancement administratif – MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link rel="stylesheet" href="/css/avancement.css">
    <link rel="stylesheet" href="/css/etudiant.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>

<?php
// ── Navigation ──────────────────────────────────────────────
$pageCourante = 'avancement';
include resource_path('views/layouts/barre_nav.php');

// ── Modèles ─────────────────────────────────────────────────
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\Document;
use App\Models\Utilisateur;

// ── Données de l'étudiant ────────────────────────────────────
$userId      = session('user_id');
$etudiant    = Etudiant::where('id_utilisateur', $userId)->first();
$utilisateur = Utilisateur::find($userId);

// ── Filtre demandé via l'URL (?filtre=en_cours | valide) ─────
$filtre = request()->query('filtre'); // 'en_cours' | 'valide' | null

// ── Compteurs pour les boutons ───────────────────────────────
$nbEnCours = $etudiant
    ? Stage::where('id_etudiant', $etudiant->id_etudiant)
        ->where('statut', 'en_cours')
        ->count()
    : 0;

$nbValides = $etudiant
    ? Stage::where('id_etudiant', $etudiant->id_etudiant)
        ->whereIn('statut', ['validé', 'en attente validation admin'])
        ->count()
    : 0;

// ── Sélection du stage selon le filtre ───────────────────────
if ($filtre === 'en_cours') {
    $stage = $etudiant
        ? Stage::where('id_etudiant', $etudiant->id_etudiant)
            ->where('statut', 'en_cours')
            ->orderByDesc('date_debut')
            ->first()
        : null;
} elseif ($filtre === 'valide') {
    $stage = $etudiant
        ? Stage::where('id_etudiant', $etudiant->id_etudiant)
            ->whereIn('statut', ['validé', 'en attente validation admin'])
            ->orderByDesc('date_debut')
            ->first()
        : null;
} else {
    $stage = $etudiant
        ? Stage::where('id_etudiant', $etudiant->id_etudiant)
            ->orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderByDesc('date_debut')
            ->first()
        : null;
}

$documents = $stage ? Document::where('id_stage', $stage->id_stage)->get() : collect();

// ── Types de documents requis ────────────────────────────────
$typesRequis = [
    'convention' => ['label' => 'Convention de stage', 'obligatoire' => true],
    'rapport'    => ['label' => 'Rapport de stage',    'obligatoire' => true],
    'evaluation' => ['label' => "Fiche d'évaluation",  'obligatoire' => true],
    'resume'     => ['label' => 'Résumé de stage',     'obligatoire' => false],
];

$typesDeposes = $documents->pluck('type')->toArray();

$manquantsObligatoires = [];
foreach ($typesRequis as $type => $info) {
    if ($info['obligatoire'] && !in_array($type, $typesDeposes)) {
        $manquantsObligatoires[] = $info['label'];
    }
}

$toutComplet = empty($manquantsObligatoires);

// ── Contacts administratifs ──────────────────────────────────
$contactsAdmin = [
    [
        'avatar'  => 'RS',
        'classe'  => 'avatar-responsable',
        'nom'     => 'Responsable des stages',
        'role'    => 'Coordination & validation des conventions',
        'email'   => 'stages@universite.fr',
        'phone'   => '04 XX XX XX XX',
    ]
];
?>

<!-- ── Flash messages ──────────────────────────────────────── -->
<?php if (session('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars(session('success')) ?></div>
<?php endif; ?>
<?php if (session('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars(session('error')) ?></div>
<?php endif; ?>

<main>

    <!-- ── Carte 1 : État global ──────────────────────────── -->
    <div class="card card-full">
        <div class="card-header">
            <h2>Avancement administratif</h2>
        </div>

        <!-- ── Filtre ─────────────────────────────────────── -->
        <div class="filtre-avancement">
            <span class="filtre-label">Afficher :</span>
            <div class="filtre-boutons">

                <a href="/etudiant/avancement"
                   class="filtre-btn <?= !$filtre ? 'filtre-btn-actif' : '' ?>">
                    Par défaut
                </a>

                <a href="/etudiant/avancement?filtre=en_cours"
                   class="filtre-btn <?= $filtre === 'en_cours' ? 'filtre-btn-actif filtre-en-cours' : '' ?>">
                    En cours
                    <?php if ($nbEnCours > 0): ?>
                        <span class="filtre-badge"><?= $nbEnCours ?></span>
                    <?php endif; ?>
                </a>

                <a href="/etudiant/avancement?filtre=valide"
                   class="filtre-btn <?= $filtre === 'valide' ? 'filtre-btn-actif filtre-valide' : '' ?>">
                    Validé
                    <?php if ($nbValides > 0): ?>
                        <span class="filtre-badge"><?= $nbValides ?></span>
                    <?php endif; ?>
                </a>

            </div>
        </div>

        <?php if (!$stage): ?>
            <div class="alert-banner warning">
                <div class="alert-icon">⚠️</div>
                <div class="alert-body">
                    <strong>
                        <?php
                        if ($filtre === 'en_cours')   echo 'Aucun stage en cours';
                        elseif ($filtre === 'valide') echo 'Aucun stage validé';
                        else                          echo 'Aucun stage enregistré';
                        ?>
                    </strong>
                    <?php
                    if ($filtre === 'en_cours')   echo 'Vous n\'avez pas de stage actuellement en cours.';
                    elseif ($filtre === 'valide') echo 'Vous n\'avez pas encore de stage validé à venir.';
                    else                          echo 'Vous n\'avez pas encore de stage enregistré. Aucun document administratif n\'est requis pour le moment.';
                    ?>
                </div>
            </div>

        <?php else: ?>

            <div class="etat-global <?= $toutComplet ? 'complet' : 'incomplet' ?>">
                <div>
                    <div class="etat-global-titre">
                        <?= $toutComplet
                            ? 'Dossier administratif complet'
                            : count($manquantsObligatoires) . ' document(s) obligatoire(s) manquant(s)' ?>
                    </div>
                    <div class="etat-global-sous">
                        <?= $toutComplet
                            ? 'Tous vos documents obligatoires ont bien été déposés.'
                            : 'Votre dossier est incomplet. Veuillez déposer les documents manquants dès que possible.' ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- ── Carte 2 : Checklist des documents ─────────────── -->
    <div class="card">
        <div class="card-header">
            <h2>Documents déposés</h2>
        </div>

        <?php if (!$stage): ?>
            <p class="empty-state">Aucun stage à afficher</p>
        <?php else: ?>
            <div class="checklist">
                <?php foreach ($typesRequis as $type => $info):
                    $depose = in_array($type, $typesDeposes);
                    $classe = $depose ? 'ok' : 'manque';
                    $doc    = $documents->firstWhere('type', $type);
                ?>
                    <div class="checklist-item <?= $classe ?>">
                        <div class="checklist-left">
                            <div>
                                <div class="checklist-label">
                                    <?= htmlspecialchars($info['label']) ?>
                                    <?php if (!$info['obligatoire']): ?>
                                        <span style="font-size:0.7rem;color:#94a3b8;font-weight:400;"> (facultatif)</span>
                                    <?php endif; ?>
                                </div>
                                <div class="checklist-sublabel">
                                    <?php if ($depose && $doc): ?>
                                        Déposé le <?= $doc->updated_at ? $doc->updated_at->format('d/m/Y') : '–' ?>
                                    <?php elseif ($info['obligatoire']): ?>
                                        Document obligatoire — non encore déposé
                                    <?php else: ?>
                                        Non déposé
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div>
                            <?php if ($depose): ?>
                                <span class="badge-ok"> Déposé</span>
                            <?php else: ?>
                                <span class="badge-manque"> Manquant</span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <p style="font-size:0.78rem;color:#94a3b8;margin-top:14px;">
                Pour déposer un document manquant, rendez-vous sur votre <a href="/etudiant" style="color:#0062AD;">page d'accueil</a>.
            </p>
        <?php endif; ?>
    </div>

    <!-- ── Carte 3 : Contacts administratifs ─────────────── -->
    <div class="card card-full">
        <div class="card-header">
            <h2>Contacts administratifs</h2>
        </div>

        <div class="contact-admin-list">
            <?php foreach ($contactsAdmin as $contact): ?>
                <div class="contact-admin-card">
                    <div class="contact-admin-avatar <?= $contact['classe'] ?>">
                        <?= $contact['avatar'] ?>
                    </div>
                    <div class="contact-admin-info">
                        <div class="contact-admin-nom"><?= htmlspecialchars($contact['nom']) ?></div>
                        <div class="contact-admin-role"><?= htmlspecialchars($contact['role']) ?></div>
                        <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="contact-admin-email">
                            ✉ <?= htmlspecialchars($contact['email']) ?>
                        </a>
                        <?php if ($contact['phone']): ?>
                            <div class="contact-admin-phone"> <?= htmlspecialchars($contact['phone']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

</main>

<style>
/* ── Filtre avancement ───────────────────────────────────── */
.filtre-avancement {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 14px 20px;
    border-bottom: 1px solid #f0f4f8;
}

.filtre-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
}

.filtre-boutons {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filtre-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 0.84rem;
    font-weight: 500;
    text-decoration: none;
    color: #475569;
    background: #f1f5f9;
    border: 1.5px solid #e2e8f0;
    transition: all 0.15s ease;
}
.filtre-btn:hover { background: #e2e8f0; color: #1e293b; }

.filtre-btn-actif             { background: #0062AD; color: white; border-color: transparent; }
.filtre-btn-actif:hover       { background: #004f8a; color: white; }
.filtre-btn-actif.filtre-en-cours       { background: #1565c0; }
.filtre-btn-actif.filtre-en-cours:hover { background: #0d47a1; }
.filtre-btn-actif.filtre-valide         { background: #2e7d32; }
.filtre-btn-actif.filtre-valide:hover   { background: #1b5e20; }

.filtre-badge {
    display: inline-block;
    background: rgba(255,255,255,0.3);
    border-radius: 10px;
    padding: 0 7px;
    font-size: 0.75rem;
    font-weight: 600;
    min-width: 18px;
    text-align: center;
}
.filtre-btn:not(.filtre-btn-actif) .filtre-badge { background: #cbd5e1; color: #334155; }
</style>

</body>
</html>
