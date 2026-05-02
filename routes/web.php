<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\VoirOffreController;
use App\Http\Controllers\PostulerController;
use App\Http\Controllers\CandidaturesController;
use App\Http\Controllers\EtudiantStageController;
use App\Http\Controllers\AdminStageController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ProfilController;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Tuteur;
use App\Models\Suivi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ─────────────────────────────────────────────
// REDIRECTION RACINE
// ─────────────────────────────────────────────
Route::get('/', function () {
    return redirect('/accueil');
});

Route::get('/accueil', function() {
    include resource_path('views/auth/accueil.php');
});

// ─────────────────────────────────────────────
// AUTH
// ─────────────────────────────────────────────
Route::get('/connexion',  [LoginController::class, 'index']);
Route::post('/connexion', [LoginController::class, 'verifier']);
Route::get('/deconnexion', [LoginController::class, 'deconnexion']);

Route::get('/inscription',  [RegisterController::class, 'index']);
Route::post('/inscription', [RegisterController::class, 'register']);

Route::get('/inscription/verification',  [RegisterController::class, 'showVerifyForm']);
Route::post('/inscription/verification', [RegisterController::class, 'verifyAndCreate']);

// 2FA
Route::get('/verify-2fa',  [TwoFactorController::class, 'showVerifyForm']);
Route::post('/verify-2fa', [TwoFactorController::class, 'verify']);
Route::get('/cancel-2fa',  [TwoFactorController::class, 'cancel']);

// Vérification email
Route::get('/verification',  [VerificationController::class, 'index']);
Route::post('/verification', [VerificationController::class, 'verify']);

// Changement de mot de passe
Route::get('/changer-mdp', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    include resource_path('views/auth/changerMdp.php');
});
Route::post('/changer-mdp', [PasswordController::class, 'update']);

Route::get('/changer-mdp/verification',  [PasswordController::class, 'showVerifyForm']);
Route::post('/changer-mdp/verification', [PasswordController::class, 'verifyAndUpdate']);

// Switch de rôle
Route::get('/switch-role/{role}', function($role) {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array($role, session('roles', []))) {
        return redirect('/connexion')->with('error', 'Rôle invalide');
    }
    session()->put('role_actif', $role);
    $redirections = [
        'etudiant'       => '/etudiant',
        'entreprise'     => '/entreprise',
        'tuteur'         => '/tuteur',
        'jury'           => '/jury',
        'administrateur' => '/administrateur',
    ];
    return redirect($redirections[$role] ?? '/accueil');
});

// ─────────────────────────────────────────────
// ÉTUDIANT
// ─────────────────────────────────────────────
Route::get('/etudiant', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'etudiant';
    include resource_path('views/etudiant/etudiant.php');
});

Route::get('/etudiant/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    $etudiant = Etudiant::where('id_utilisateur', session('user_id'))->first();
    include resource_path('views/etudiant/profil.php');
});
Route::post('/etudiant/profil/modifier', [ProfilController::class, 'updateEtudiant']);

Route::get('/etudiant/avancement', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'avancement';
    include resource_path('views/etudiant/avancement.php');
});

// Postuler
Route::get('/postuler/{id}',  [PostulerController::class, 'index'])->name('postuler.index');
Route::post('/postuler/{id}', [PostulerController::class, 'store'])->name('postuler.store');

// Gestion de stage étudiant
Route::post('/stage/accepter-dates/{id}', [EtudiantStageController::class, 'accepterDates']);
Route::post('/stage/refuser-dates/{id}',  [EtudiantStageController::class, 'refuserDates']);
Route::post('/stage/convention/{id}',     [EtudiantStageController::class, 'uploadConvention']);

// Suivi carnet de bord
Route::post('/etudiant/ajouter-suivi', function(Request $request) {
    if (!session()->has('user_id')) return redirect('/connexion');
    $request->validate([
        'id_stage'   => 'required|integer',
        'avancement' => 'required|string|max:1000',
    ]);
    Suivi::create([
        'id_stage'   => $request->id_stage,
        'date'       => now(),
        'avancement' => $request->avancement,
    ]);
    return redirect('/etudiant')->with('success', 'Entrée ajoutée au carnet de bord !');
});

// Upload CV
Route::post('/upload-cv', function(Request $request) {
    if (!session()->has('user_id')) return redirect('/connexion');
    $idUtilisateur = session('user_id');
    $dossier = storage_path('app/private/Documents/' . $idUtilisateur);
    if (!file_exists($dossier)) mkdir($dossier, 0755, true);
    if ($request->hasFile('cv')) {
        $request->file('cv')->move($dossier, 'CV.pdf');
    }
    return redirect('/etudiant/profil')->with('success', 'CV déposé avec succès !');
});

// Upload document de stage
Route::post('/upload-document', function(Request $request) {
    if (!session()->has('user_id')) return redirect('/connexion');
    $idUtilisateur = session('user_id');
    $dossier = storage_path('app/private/Documents/' . $idUtilisateur);
    $typedoc = $request->input('type');
    $nomsDoc = [
        'rapport'    => 'RapportDeStage.pdf',
        'convention' => 'ConventionDeStage.pdf',
        'evaluation' => 'FicheEvaluation.pdf',
        'resume'     => 'ResumeDeStage.pdf',
    ];
    if (!file_exists($dossier)) mkdir($dossier, 0755, true);
    if ($request->hasFile('fichier')) {
        $request->file('fichier')->move($dossier, $nomsDoc[$typedoc]);
        \Illuminate\Support\Facades\DB::table('document')->updateOrInsert(
            ['id_stage' => $request->input('id_stage'), 'type' => $typedoc],
            ['fichier' => $idUtilisateur . '/' . $nomsDoc[$typedoc], 'updated_at' => now(), 'created_at' => now()]
        );
        return redirect('/etudiant/avancement')->with('success', $nomsDoc[$typedoc] . ' déposé avec succès !');
    }
    return redirect('/etudiant/avancement')->with('error', 'Aucun fichier sélectionné pour ' . ucfirst($typedoc) . '.');
});

// Visualiser CV
Route::get('/mon-cv', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    $cheminCV = storage_path('app/private/Documents/' . session('user_id') . '/CV.pdf');
    if (!file_exists($cheminCV)) abort(404, 'CV non trouvé');
    return response()->file($cheminCV, ['Content-Type' => 'application/pdf']);
});

// Visualiser lettre de motivation
Route::get('/ma-lettre', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    $cheminLM = storage_path('app/private/Documents/' . session('user_id') . '/LettreMotivation.pdf');
    if (!file_exists($cheminLM)) abort(404, 'Lettre de motivation non trouvée');
    return response()->file($cheminLM, ['Content-Type' => 'application/pdf']);
});

// Télécharger un document de stage
Route::get('/download-{type}', function($type) {
    if (!session()->has('user_id')) return redirect('/connexion');
    $idUtilisateur = session('user_id');
    $nomsDoc = [
        'rapport'    => 'RapportDeStage.pdf',
        'convention' => 'ConventionDeStage.pdf',
        'evaluation' => 'FicheEvaluation.pdf',
        'resume'     => 'ResumeDeStage.pdf',
    ];
    if (!isset($nomsDoc[$type])) abort(404, 'Type de document invalide');
    $cheminDoc = storage_path('app/private/Documents/' . $idUtilisateur . '/' . $nomsDoc[$type]);
    if (!file_exists($cheminDoc)) abort(404, 'Document non trouvé');
    return response()->file($cheminDoc, [
        'Content-Type'        => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $nomsDoc[$type] . '"',
    ]);
});

// ─────────────────────────────────────────────
// ENTREPRISE
// ─────────────────────────────────────────────
Route::get('/entreprise', [CandidaturesController::class, 'index']);

Route::get('/entreprise/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('entreprise', session('roles', []))) return redirect('/connexion');
    $entreprise = Entreprise::where('id_utilisateur', session('user_id'))->first();
    include resource_path('views/entreprise/profil.php');
});
Route::post('/entreprise/profil/modifier', [ProfilController::class, 'updateEntreprise']);

Route::get('/entreprise/candidatures', [CandidaturesController::class, 'candidats'])->name('entreprise.candidatures');

// Offres
Route::get('/creer-offre',  function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (session('role_actif') !== 'entreprise') return redirect('/connexion');
    include resource_path('views/entreprise/publierOffre.php');
})->name('offres.create');
Route::post('/creer-offre', [OffreController::class, 'store'])->name('offres.store');

Route::get('/publierOffre',           function() { return redirect('/creer-offre'); });
Route::get('/entreprise/publierOffre', function() { return redirect('/creer-offre'); });

// Offres publiques
Route::get('/offres', [VoirOffreController::class, 'index']);

// Candidatures
Route::post('/candidature/accepter/{id}',           [CandidaturesController::class, 'accepter']);
Route::post('/candidature/refuser/{id}',            [CandidaturesController::class, 'refuser']);
Route::post('/candidature/remarque/{idStage}',      [CandidaturesController::class, 'ajouterRemarque']);
Route::get('/candidature/cv/{idUtilisateur}',       [CandidaturesController::class, 'voirCV']);
Route::get('/candidature/lettre/{idUtilisateur}',   [CandidaturesController::class, 'voirLettreMotivation']);
Route::get('/candidature/convention/{idUtilisateur}',[CandidaturesController::class, 'voirConvention']);
Route::get('/candidature/convention-stage/{idStage}',[CandidaturesController::class, 'voirConventionStage']);
Route::get('/candidature/document/{idUtilisateur}/{type}', [CandidaturesController::class, 'voirDocument']);

// Convention entreprise
Route::post('/convention/valider/{id}', [CandidaturesController::class, 'validerConvention']);
Route::post('/convention/refuser/{id}', [CandidaturesController::class, 'refuserConvention']);

// Proposer des dates
Route::post('/candidature/proposer-dates/{id}',    [CandidaturesController::class, 'proposerDates']);
Route::post('/candidature/convention-signee/{id}', [CandidaturesController::class, 'envoyerConventionSignee']);

// ─────────────────────────────────────────────
// TUTEUR
// ─────────────────────────────────────────────
Route::get('/tuteur', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('tuteur', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'tuteur';
    include resource_path('views/tuteur/tuteur.php');
});

Route::get('/tuteur/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('tuteur', session('roles', []))) return redirect('/connexion');
    $tuteur = Tuteur::where('id_utilisateur', session('user_id'))->first();
    include resource_path('views/tuteur/profil.php');
});
Route::post('/tuteur/profil/modifier', [ProfilController::class, 'updateTuteur']);

// ─────────────────────────────────────────────
// JURY
// ─────────────────────────────────────────────
Route::get('/jury', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('jury', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'jury';
    include resource_path('views/jury/jury.php');
});

// ─────────────────────────────────────────────
// ADMINISTRATEUR
// ─────────────────────────────────────────────
Route::get('/administrateur', [AdminUserController::class, 'dashboard']);

Route::get('/administrateur/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('administrateur', session('roles', []))) return redirect('/connexion');
    include resource_path('views/admin/profil.php');
});
Route::post('/administrateur/profil/modifier', [ProfilController::class, 'updateAdmin']);

// Validation des stages
Route::get('/administrateur/validation',          [AdminStageController::class, 'index']);
Route::post('/administrateur/valider/{id}',       [AdminStageController::class, 'valider']);
Route::post('/administrateur/refuser/{id}',       [AdminStageController::class, 'refuser']);
Route::get('/administrateur/convention/{id}',     [AdminStageController::class, 'voirConvention']);

// Inscriptions
Route::get('/administrateur/inscriptions',              [AdminStageController::class, 'inscriptions']);
Route::post('/admin/inscription/accepter/{id}',         [AdminStageController::class, 'accepterInscription']);
Route::post('/admin/inscription/refuser/{id}',          [AdminStageController::class, 'refuserInscription']);

// Gestion des utilisateurs
Route::get('/administrateur/utilisateurs',              [AdminUserController::class, 'listeUtilisateurs']);
Route::get('/administrateur/utilisateurs/creer',        [AdminUserController::class, 'creerUtilisateur']);
Route::post('/administrateur/utilisateurs/creer',       [AdminUserController::class, 'storeUtilisateur']);
Route::get('/administrateur/modifier/{id}',             [AdminUserController::class, 'modifierUtilisateur']);
Route::post('/administrateur/utilisateurs/update/{id}', [AdminUserController::class, 'updateUtilisateur']);
Route::post('/administrateur/supprimer/{id}',           [AdminUserController::class, 'supprimerUtilisateur']);
Route::post('/administrateur/roles/{id}',               [AdminUserController::class, 'modifierRoles']);