<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\TwoFactorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OffreController;
use App\Http\Controllers\VoirOffreController;
use App\Http\Controllers\PostulerController;
use App\Http\Controllers\CandidaturesController;
use App\Http\Controllers\EtudiantStageController;
use App\Http\Controllers\AdminStageController;
use Illuminate\Http\Request;
use App\Models\Suivi;
use App\Models\Inscription;

Route::get('/', function () {
    return redirect('/accueil');
});

// ------------------- Route auth-----------------------

// Route mdp
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

// Routes de vérification d'email
Route::get('/verification', [VerificationController::class, 'index']);
Route::post('/verification', [VerificationController::class, 'verify']);

// Inscription avec vérification email
Route::get('/inscription/verification', [RegisterController::class, 'showVerifyForm']);
Route::post('/inscription/verification', [RegisterController::class, 'verifyAndCreate']);

// Changement de mot de passe avec vérification email
Route::get('/changer-mdp', [PasswordController::class, 'index']);
Route::post('/changer-mdp', [PasswordController::class, 'update']);
Route::get('/changer-mdp/verification', [PasswordController::class, 'showVerifyForm']);
Route::post('/changer-mdp/verification', [PasswordController::class, 'verifyAndUpdate']);

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

// ------------------- Route etudiant-----------------------
Route::get('/etudiant/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    include resource_path('views/etudiant/profil.php');
});

Route::get('/etudiant/avancement', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('etudiant', session('roles', []))) return redirect('/connexion');
    $pageCourante = 'avancement';
    include resource_path('views/etudiant/avancement.php');
});


// Route pour postuler
Route::get('/postuler/{id}', [PostulerController::class, 'index']);
Route::post('/postuler/{id}', [PostulerController::class, 'store']);

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

// Route pour upload un document de stage (rapport, convention, etc.)
Route::post('/upload-document', function(\Illuminate\Http\Request $request) {
    if (!session()->has('user_id')) return redirect('/connexion');
    //IL FAUT QUE LE DOCUMENT A DEPOSER SOIT NOMMER 
    $idUtilisateur = session('user_id');
    $dossier = storage_path('app/private/Documents/' . $idUtilisateur);
    $typedoc = $request->input('type'); // 'rapport', 'convention', etc.
    // Noms de fichiers standardisés pour chaque type de document
    $nomsDoc = [
        'rapport'    => 'RapportDeStage.pdf',
        'convention' => 'ConventionDeStage.pdf',
        'evaluation' => 'FicheEvaluation.pdf',
        'resume'     => 'ResumeDeStage.pdf',
    ];
    //$nomFinal = $nomsDoc[$typedoc];

    if (!file_exists($dossier)) {
        mkdir($dossier, 0755, true);
    }

    if ($request->hasFile('fichier')) {
        $request->file('fichier')->move($dossier, $nomsDoc[$typedoc]);
        //on met à jour ou on insère le document dans la base de données
        \Illuminate\Support\Facades\DB::table('document')->updateOrInsert(
            [
                'id_stage' => $request->input('id_stage'),
                'type'     => $typedoc
            ],
            [
                'fichier' => $idUtilisateur . '/' . $nomsDoc[$typedoc], // On stocke le chemin relatif
                'updated_at' => now(),// <--- Ça met à jour l'heure si le document existe déjà
                'created_at' => now(), // <--- Ça met l'heure de création si c'est un nouveau
            ]
        );
        return redirect('/etudiant/avancement')->with('success', $nomsDoc[$typedoc] . ' déposé avec succès !');
    }
        return redirect('/etudiant/avancement')->with('error', 'Aucun fichier sélectionné pour ' . ucfirst($typedoc) . '.');
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

// Routes étudiant - Gestion de stage
Route::post('/stage/accepter-dates/{id}', [EtudiantStageController::class, 'accepterDates']);
Route::post('/stage/refuser-dates/{id}', [EtudiantStageController::class, 'refuserDates']);
Route::post('/stage/convention/{id}', [EtudiantStageController::class, 'uploadConvention']);




Route::post('/etudiant/ajouter-suivi', function(Request $request) {

    // Sécurité basique
    if (!session()->has('user_id')) return redirect('/connexion');

    // Validation
    $request->validate([
        'id_stage'  => 'required|integer',
        'avancement'=> 'required|string|max:1000',
    ]);

    // Insertion en base
    Suivi::create([
        'id_stage'   => $request->id_stage,
        'date'       => now(),
        'avancement' => $request->avancement,
    ]);

    return redirect('/etudiant')
        ->with('success', 'Entrée ajoutée au carnet de bord !');
});


// ------------------- Route entreprise-----------------------
Route::get('/entreprise/profil', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (!in_array('entreprise', session('roles', []))) return redirect('/connexion');
    include resource_path('views/entreprise/profil.php');
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

// Route pour accepté ou non une candidature
Route::get('/entreprise', function() {
    if (!session()->has('user_id')) return redirect('/connexion');
    if (session('role_actif') !== 'entreprise') return redirect('/connexion');
    $pageCourante = 'entreprise';
    app(CandidaturesController::class)->index();
});

// Routes entreprise - gestion de stage
Route::post('/candidature/proposer-dates/{id}', [CandidaturesController::class, 'proposerDates']);
Route::post('/candidature/convention-signee/{id}', [CandidaturesController::class, 'envoyerConventionSignee']);
Route::get('/candidature/convention/{id}', [CandidaturesController::class, 'voirConvention']);
Route::post('/candidature/refuser/{id}', [CandidaturesController::class, 'refuser']);
Route::get('/candidature/cv/{idUtilisateur}', [CandidaturesController::class, 'voirCV']);
Route::get('/candidature/lettre/{idUtilisateur}', [CandidaturesController::class, 'voirLettreMotivation']);


// ------------------- Route Admin ------------------------

// Routes admin - validation stage
Route::get('/administrateur/validation', [AdminStageController::class, 'index']);
Route::post('/administrateur/valider/{id}', [AdminStageController::class, 'valider']);
Route::post('/administrateur/refuser/{id}', [AdminStageController::class, 'refuser']);

// Route admin - Validation Inscription
Route::post('/admin/inscription/accepter/{id}', function($id) {

    $demande = \App\Models\Inscription::find($id);
    if (!$demande) return back();

    $data = $demande->data; // déjà array grâce au cast

    // 🔥 Création utilisateur
    $user = \App\Models\Utilisateur::create([
        'nom'          => $data['nom'],
        'prenom'       => $data['prenom'],
        'email'        => $data['email'],
        'mot_de_passe' => $data['mot_de_passe'],
    ]);

    // 🔥 Rôle
    $roleData = [
        'id_utilisateur' => $user->id_utilisateur,
        'administrateur' => 0,
        'etudiant' => 0,
        'entreprise' => 0,
        'tuteur' => 0,
        'jury' => 0,
    ];

    $roleData[$data['role']] = 1;
    \App\Models\Role::create($roleData);

    // 🔥 Tables spécifiques
    switch ($data['role']) {
        case 'etudiant':
            \App\Models\Etudiant::create([
                'id_utilisateur' => $user->id_utilisateur,
                'filiere' => $data['filiere'],
                'niveau' => $data['niveau'],
            ]);
            break;

        case 'entreprise':
            \App\Models\Entreprise::create([
                'id_utilisateur' => $user->id_utilisateur,
                'nom_entreprise' => $data['nom_entreprise'],
                'adresse' => $data['adresse'],
                'secteur' => $data['secteur'],
                'siret' => $data['siret'],
            ]);
            break;

        case 'tuteur':
            \App\Models\Tuteur::create([
                'id_utilisateur' => $user->id_utilisateur,
                'specialite' => $data['specialite'],
            ]);
            break;

        case 'jury':
            \App\Models\Jury::create([
                'id_utilisateur' => $user->id_utilisateur
            ]);
            break;

        case 'administrateur':
            \App\Models\Administrateur::create([
                'id_utilisateur' => $user->id_utilisateur
            ]);
            break;
    }

    // ✅ Marquer comme accepté
    $demande->update([
        'statut' => 'accepte'
    ]);

    return back()->with('success', 'Compte validé et créé');
});

Route::post('/administrateur/inscriptions/refuser/{id}', function($id) {
    $demande = \App\Models\Inscription::find($id);
    
    if ($demande) {
        $demande->update(['statut' => 'refuse']);
        return back()->with('success', 'Demande refusée avec succès');
    }

    return back()->with('error', 'Demande introuvable');
});

Route::get('/administrateur/inscriptions', function() {
    $inscriptions = \App\Models\Inscription::where('statut', 'en_attente')->get();


    return view('admin.inscriptions', ['inscriptions' => $inscriptions]);
});

// ------------------- Route commune-----------------------

//Route redirection
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
    if (session('role_actif') !== 'entreprise') return redirect('/connexion');
    return app(CandidaturesController::class)->index();
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

//route pour telecharger un document de stage
Route::get('/download-{type}', function($type) {
    // 1. Sécurité : l'utilisateur doit être connecté
    if (!session()->has('user_id')) return redirect('/connexion');
    $idUtilisateur = session('user_id');

    // 2. Dictionnaire des noms (le même que pour l'upload)
    $nomsDoc = [
        'rapport'    => 'RapportDeStage.pdf',
        'convention' => 'ConventionDeStage.pdf',
        'evaluation' => 'FicheEvaluation.pdf',
        'resume'     => 'ResumeDeStage.pdf',
    ];
    // Vérifie si le type demandé existe dans notre liste
    if (!isset($nomsDoc[$type])){
        abort(404, 'Type de document invalide');
    }
    // 3. Construction du chemin vers le fichier dans le stockage
    $cheminDoc = storage_path('app/private/Documents/' . $idUtilisateur . '/' . $nomsDoc[$type]);
    // 4. Vérification de l'existence du fichier
    if (!file_exists($cheminDoc)) {
        abort(404, 'Document non trouvé');
    }
    // 5. Retour du fichier en réponse
    return response()->file($cheminDoc, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $nomsDoc[$type] . '"',
    ]);
});

// Route pour visualiser la lettre de motivation 
Route::get('/ma-lettre', function() {
    if (!session()->has('user_id')) return redirect('/connexion');

    $cheminLM = storage_path('app/private/Documents/' . session('user_id') . '/LettreMotivation.pdf');

    if (!file_exists($cheminLM)) abort(404, 'Lettre de motivation non trouvée');

    return response()->file($cheminLM, ['Content-Type' => 'application/pdf']);
});
// --------------------------------------------------------