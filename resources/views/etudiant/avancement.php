<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Mon Avancement – MYstage</title>
    <link rel="stylesheet" href="/css/Adminstyle.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <?php include __DIR__ . '/partials/avancement_css.php'; ?>
</head>
<body>

<?php
// ── Navigation ─────────────────────────────────────────────
$pageCourante = 'avancement';
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

// Stage le plus récent de l'étudiant
$stage = $etudiant
    ? Stage::where('id_etudiant', $etudiant->id_etudiant)
            ->orderByDesc('date_debut')
            ->first()
    : null;

// Données liées au stage
$documents = $stage ? Document::where('id_stage', $stage->id_stage)->get()      : collect();
$suivis    = $stage ? Suivi::where('id_stage', $stage->id_stage)->orderBy('date')->get() : collect();

// Contacts
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

// Calcul de la progression (%)
$progression = 0;
if ($stage && $stage->date_debut && $stage->date_fin) {
    $debut = $stage->date_debut->timestamp;
    $fin   = $stage->date_fin->timestamp;
    $now   = now()->timestamp;
    if ($now >= $fin)        $progression = 100;
    elseif ($now > $debut)   $progression = round(($now - $debut) / ($fin - $debut) * 100);
}

// Labels des types de documents
$typesLabels = [
    'rapport'    => ['label' => 'Rapport de stage',    'icon' => '📄'],
    'convention' => ['label' => 'Convention de stage',  'icon' => '📋'],
    'evaluation' => ['label' => "Fiche d'évaluation",   'icon' => '✅'],
    'resume'     => ['label' => 'Résumé de stage',      'icon' => '📝'],
];
?>

<!-- ── Flash messages ─────────────────────────────────────── -->
<?php if (session('success')): ?>
    <div class="alert alert-success"><?= htmlspecialchars(session('success')) ?></div>
<?php endif; ?>
<?php if (session('error')): ?>
    <div class="alert alert-error"><?= htmlspecialchars(session('error')) ?></div>
<?php endif; ?>

<!-- ── Contenu principal ──────────────────────────────────── -->
<main>

    <?php include __DIR__ . '/partials/stage.php'; ?>

    <?php include __DIR__ . '/partials/documents.php'; ?>

    <?php include __DIR__ . '/partials/carnet.php'; ?>

    <?php include __DIR__ . '/partials/contact.php'; ?>

</main>

</body>
</html>
