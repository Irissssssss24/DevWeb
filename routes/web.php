<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordController;

Route::get('/', function () {
    return redirect('/accueil');
});

// Routes d'authentification
Route::get('/connexion', [LoginController::class, 'index']);
Route::post('/connexion', [LoginController::class, 'verifier']);
Route::get('/deconnexion', [LoginController::class, 'deconnexion']);

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

Route::get('/admin', function() {
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
