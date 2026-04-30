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
$userId   = session('user_id');
$etudiant = Etudiant::where('id_utilisateur', $userId)->first();
$utilisateur = Utilisateur::find($userId);
 
$stage = $etudiant
    ? Stage::where('id_etudiant', $etudiant->id_etudiant)
            ->orderByDesc('date_debut')
            ->first()
    : null;
 
$documents = $stage ? Document::where('id_stage', $stage->id_stage)->get() : collect();
 
// Types de documents requis
$typesRequis = [
    'convention' => ['label' => 'Convention de stage', 'obligatoire' => true],
    'rapport'    => ['label' => 'Rapport de stage',     'obligatoire' => true],
    'evaluation' => ['label' => "Fiche d'évaluation",   'obligatoire' => true],
    'resume'     => ['label' => 'Résumé de stage',      'obligatoire' => false],
];
 
// Quels types sont déjà déposés ?
$typesDeposes = $documents->pluck('type')->toArray();
 
// Documents obligatoires manquants
$manquantsObligatoires = [];
foreach ($typesRequis as $type => $info) {
    if ($info['obligatoire'] && !in_array($type, $typesDeposes)) {
        $manquantsObligatoires[] = $info['label'];
    }
}
 
$toutComplet = empty($manquantsObligatoires);
 
// Contacts administratifs (à adapter selon votre établissement)
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
 
<!-- ── Flash messages ─────────────────────────────────────── -->
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
 
        <?php if (!$stage): ?>
            <div class="alert-banner warning">
                <div class="alert-icon">⚠️</div>
                <div class="alert-body">
                    <strong>Aucun stage en cours</strong>
                    Vous n'avez pas encore de stage enregistré. Aucun document administratif n'est requis pour le moment.
                </div>
            </div>
 
        <?php else: ?>
 
            <!-- Récapitulatif état global -->
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
            <p class="empty-state">Aucun stage en cours </p>
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
 
</body>
</html>
