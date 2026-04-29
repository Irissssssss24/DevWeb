<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\VoirOffreController;

Route::get('/', function () {
    return redirect('/accueil');
});

// Routes d'authentification
Route::get('/connexion', [LoginController::class, 'index']);
Route::post('/connexion', [LoginController::class, 'verifier']);
Route::get('/deconnexion', [LoginController::class, 'deconnexion']);

// Routes de vérification 2FA
Route::get('/verify-2fa', [TwoFactorController::class, 'showVerifyForm']);
Route::post('/verify-2fa', [TwoFactorController::class, 'verify']);
Route::get('/cancel-2fa', [TwoFactorController::class, 'cancel']);

// Routes d'inscription
//nom de la classe et de la méthode à appeler 
Route::get('/inscription', [RegisterController::class, 'index']);
Route::post('/inscription', [RegisterController::class, 'register']);

Route::get('accueil', function() {
    include resource_path('views/auth/accueil.php');
});
// Routes protégées — la vérification de session se fait ici uniquement
Route::get('/etudiant', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'etudiant';
    include resource_path('views/etudiant/etudiant.php');
});

Route::get('/entreprise', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('entreprise', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'entreprise';

    include resource_path('views/entreprise/entreprise.php');
});

Route::get('/tuteur', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('tuteur', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'tuteur';
    include resource_path('views/tuteur/tuteur.php');
});

Route::get('/jury', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('jury', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'jury';
    include resource_path('views/jury/jury.php');
});

Route::get('/administrateur', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('administrateur', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'admin';
    include resource_path('views/admin/admin.php');
});

Route::get('/etudiant/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    include resource_path('views/etudiant/profil.php');
});

Route::get('/changer_mdp', function() {
    $pageCourante = 'changermdp';
    include resource_path('views/auth/changerMdp.php');


});

// Inscription
Route::get('/inscription', [RegisterController::class, 'index']);
Route::post('/inscription', [RegisterController::class, 'register']);

// Changement de mot de passe
Route::get('/changer-mdp', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    include resource_path('views/auth/changerMdp.php');
});
Route::post('/changer-mdp', [PasswordController::class, 'update']);


// Routes de vérification d'email
Route::get('/verification', [VerificationController::class, 'index']);
Route::post('/verification', [VerificationController::class, 'verify']);

// Inscription avec vérification email
Route::get('/inscription', [RegisterController::class, 'index']);
Route::post('/inscription', [RegisterController::class, 'register']);
Route::get('/inscription/verification', [RegisterController::class, 'showVerifyForm']);
Route::post('/inscription/verification', [RegisterController::class, 'verifyAndCreate']);

// Changement de mot de passe avec vérification email
Route::get('/changer-mdp', [PasswordController::class, 'index']);
Route::post('/changer-mdp', [PasswordController::class, 'update']);
Route::get('/changer-mdp/verification', [PasswordController::class, 'showVerifyForm']);
Route::post('/changer-mdp/verification', [PasswordController::class, 'verifyAndUpdate']);

// Route pour upload le CV
Route::post('/upload-cv', function(\Illuminate\Http\Request $request) {
    if (!session()->has('user_id')) return redirect('/connexion');

    $idUtilisateur = session('user_id');
    $dossier = storage_path('app/private/Documents/' . $idUtilisateur);

    if (!file_exists($dossier)) {
        mkdir($dossier, 0755, true);
    }

    if ($request->hasFile('cv')) {
        $request->file('cv')->move($dossier, 'CV.pdf');
    }

    return redirect('/etudiant/profil')->with('success', 'CV déposé avec succès !');
});

// Route pour visualiser le cv de manière sécurisé
Route::get('/mon-cv', function() {
    if (!session()->has('user_id')) return redirect('/connexion');

    $idUtilisateur = session('user_id');
    $cheminCV = storage_path('app/private/Documents/' . $idUtilisateur . '/CV.pdf');

    if (!file_exists($cheminCV)) {
        abort(404, 'CV non trouvé');
    }

    return response()->file($cheminCV, [
        'Content-Type' => 'application/pdf',
    ]);
});

// Route pour creer / voir les offres de stage
Route::get('/publierOffre', function() {
    return redirect('/entreprise/publierOffre');
});

Route::get('/creer-offre', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (session('role_actif') !== 'entreprise') return redirect('/connexion');
    include resource_path('views/entreprise/publierOffre.php');
});

Route::post('/creer-offre', [OffreController::class, 'store']);


Route::get('/offres', [VoirOffreController::class, 'index']);


//Route pour le switch de role
Route::get('/switch-role/{role}', function($role) {
    if (!session()->has('user_id')) return redirect('/connexion');

    // Vérifier que l'utilisateur a bien ce rôle
    if (!in_array($role, session('roles', []))) {
        return redirect('/connexion')->with('error', 'Rôle invalide');
    }

    // Changer le rôle actif
    session()->put('role_actif', $role);

    // Rediriger vers la page d'accueil du nouveau rôle
    $redirections = [
        'etudiant'      => '/etudiant',
        'entreprise'    => '/entreprise',
        'tuteur'        => '/tuteur',
        'jury'          => '/jury',
        'administrateur'=> '/administrateur',
    ];

    return redirect($redirections[$role] ?? '/accueil');
});