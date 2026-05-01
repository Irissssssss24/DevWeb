<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mon Avancement – MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/etudiant.css">
</head>
<body>

<?php
// ── Navigation ─────────────────────────────────────────────
$pageCourante = 'etudiant';
include resource_path('views/layouts/barre_nav.php');

// ── Modèles ────────────────────────────────────────────────
use App\Models\Etudiant;
use App\Models\Stage;
use App\Models\Document;
use App\Models\Suivi;
use App\Models\Utilisateur;
use App\Models\Tuteur;
use App\Models\Entreprise;
use App\Models\OffreStage;

// ── Récupération des données ────────────────────────────────
$userId   = session('user_id');
$etudiant = Etudiant::where('id_utilisateur', $userId)->first();

// ── Filtre demandé via l'URL (?filtre=en_cours | valide) ────
$filtre = request()->query('filtre'); // 'en_cours' | 'valide' | null

// ── Compteurs pour les boutons de filtre ────────────────────
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

// ── Sélection du stage selon le filtre ──────────────────────
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
    // Défaut : stage en cours en priorité, sinon le plus récent
    $stage = $etudiant
        ? Stage::where('id_etudiant', $etudiant->id_etudiant)
            ->orderByRaw("CASE WHEN statut = 'en_cours' THEN 0 ELSE 1 END")
            ->orderByDesc('date_debut')
            ->first()
        : null;
}

// ── Données liées au stage sélectionné ──────────────────────
$documents = $stage ? Document::where('id_stage', $stage->id_stage)->get()           : collect();
$suivis    = $stage ? Suivi::where('id_stage', $stage->id_stage)->orderBy('date')->get() : collect();

// ── Contacts ────────────────────────────────────────────────
$tuteurUser     = null;
$entrepriseUser = null;
$offre          = null;

if ($stage) {
    $offre = OffreStage::find($stage->id_offre);

    if ($stage->id_tuteur) {
        $tuteur = Tuteur::find($stage->id_tuteur);
        if ($tuteur) $tuteurUser = Utilisateur::find($tuteur->id_utilisateur);
    }

    if ($offre) {
        $entreprise = Entreprise::find($offre->id_entreprise);
        if ($entreprise) $entrepriseUser = Utilisateur::find($entreprise->id_utilisateur);
    }
}

// ── Calcul de la progression (%) ────────────────────────────
$progression = 0;
if ($stage && $stage->date_debut && $stage->date_fin) {
    $debut = $stage->date_debut->timestamp;
    $fin   = $stage->date_fin->timestamp;
    $now   = now()->timestamp;
    if ($now >= $fin)       $progression = 100;
    elseif ($now > $debut)  $progression = round(($now - $debut) / ($fin - $debut) * 100);
}

// ── Labels des types de documents ───────────────────────────
$typesLabels = [
    'rapport'    => ['label' => 'Rapport de stage'],
    'convention' => ['label' => 'Convention de stage'],
    'evaluation' => ['label' => "Fiche d'évaluation"],
    'resume'     => ['label' => 'Résumé de stage'],
];
?>

<!-- ── Flash messages ──────────────────────────────────────── -->
<?php if (session('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars(session('success')) ?></div>
<?php endif; ?>
<?php if (session('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars(session('error')) ?></div>
<?php endif; ?>

<!-- ── Filtre de stage ─────────────────────────────────────── -->
<div class="filtre-etudiant-bar">
    <span class="filtre-etudiant-label">Afficher le stage :</span>
    <div class="filtre-etudiant-boutons">

        <a href="/etudiant"
           class="filtre-btn <?= !$filtre ? 'filtre-btn-actif' : '' ?>">
            Par défaut
        </a>

        <a href="/etudiant?filtre=en_cours"
           class="filtre-btn <?= $filtre === 'en_cours' ? 'filtre-btn-actif filtre-en-cours' : '' ?>">
            En cours
            <?php if ($nbEnCours > 0): ?>
                <span class="filtre-badge"><?= $nbEnCours ?></span>
            <?php endif; ?>
        </a>

        <a href="/etudiant?filtre=valide"
           class="filtre-btn <?= $filtre === 'valide' ? 'filtre-btn-actif filtre-valide' : '' ?>">
            Validé
            <?php if ($nbValides > 0): ?>
                <span class="filtre-badge"><?= $nbValides ?></span>
            <?php endif; ?>
        </a>

    </div>

    <?php if ($stage && $filtre): ?>
        <div class="filtre-stage-nom">
            <span class="filtre-stage-titre"><?= htmlspecialchars($offre->titre ?? 'Stage') ?></span>
            <?php if ($stage->date_debut): ?>
                <span class="filtre-stage-dates">
                    Du <?= $stage->date_debut->format('d/m/Y') ?>
                    <?= $stage->date_fin ? '→ ' . $stage->date_fin->format('d/m/Y') : '' ?>
                </span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if ($filtre && !$stage): ?>
        <div class="filtre-vide">
            <?= $filtre === 'en_cours' ? 'Aucun stage en cours.' : 'Aucun stage validé.' ?>
        </div>
    <?php endif; ?>
</div>

<!-- ── Contenu principal ──────────────────────────────────── -->
<main>

    <?php include __DIR__ . '/partials/stage.php'; ?>

    <?php include __DIR__ . '/partials/documents.php'; ?>

    <?php include __DIR__ . '/partials/suiviStage.php'; ?>

    <?php include __DIR__ . '/partials/carnet.php'; ?>

    <?php include __DIR__ . '/partials/contact.php'; ?>

</main>

<style>
/* ── Barre de filtre ─────────────────────────────────────── */
.filtre-etudiant-bar {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
    padding: 12px 20px;
    background: white;
    border-bottom: 1px solid #e8f0fb;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04);
}

.filtre-etudiant-label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748b;
    white-space: nowrap;
}

.filtre-etudiant-boutons {
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

/* Info stage sélectionné */
.filtre-stage-nom {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    padding: 5px 12px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.82rem;
}
.filtre-stage-titre { font-weight: 600; color: #1e293b; }
.filtre-stage-dates { color: #64748b; }

.filtre-vide {
    font-size: 0.82rem;
    color: #94a3b8;
    font-style: italic;
}
</style>

</body>
</html>
