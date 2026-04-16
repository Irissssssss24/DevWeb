<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/connexion');
});

// Routes d'authentification
Route::get('/connexion', [LoginController::class, 'index']);
Route::post('/connexion', [LoginController::class, 'verifier']);
Route::get('/deconnexion', [LoginController::class, 'deconnexion']);

// Routes protégées — la vérification de session se fait ici uniquement
Route::get('/etudiant', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    include resource_path('views/etudiant/etudiant.php');
});

Route::get('/entreprise', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('entreprise', session('roles', []))) return redirect('/connexion');
    include resource_path('views/entreprise/entreprise.php');
});

Route::get('/tuteur', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('tuteur', session('roles', []))) return redirect('/connexion');
    include resource_path('views/tuteur/tuteur.php');
});

Route::get('/jury', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('jury', session('roles', []))) return redirect('/connexion');
    include resource_path('views/jury/jury.php');
});

Route::get('/admin', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('administrateur', session('roles', []))) return redirect('/connexion');
    include resource_path('views/admin/admin.php');
});

