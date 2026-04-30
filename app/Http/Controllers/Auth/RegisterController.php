<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\Administrateur;
use App\Models\Authentification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function index()
    {
        ob_start();
        include resource_path('views/auth/nvUtil.php');
        return ob_get_clean();
    }

    public function register(Request $request)
    {
        $data = [
            'nom'          => $request->nom,
            'prenom'       => $request->prenom,
            'email'        => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
            'role'         => $request->role,
        ];

        // Ajouter les champs spécifiques
        switch ($request->role) {
            case 'etudiant':
                $data['filiere'] = $request->filiere;
                $data['niveau']  = $request->niveau;
                break;

            case 'entreprise':
                $data['nom_entreprise'] = $request->nom_entreprise;
                $data['adresse']        = $request->adresse;
                $data['secteur']        = $request->secteur;
                $data['siret']          = $request->siret;
                break;

            case 'tuteur':
                $data['specialite'] = $request->specialite;
                break;
        }

        session()->put('register_pending', $data);

        $rules = [
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => 'required|string|email|max:255|unique:utilisateur,email',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            'role'         => 'required|in:etudiant,entreprise,tuteur,jury,administrateur',
        ];

        // règles spécifiques
        if ($request->role === 'etudiant') {
            $rules['filiere'] = 'required|string';
            $rules['niveau']  = 'required|string';
        }

        if ($request->role === 'entreprise') {
            $rules['nom_entreprise'] = 'required|string';
            $rules['adresse']        = 'required|string';
            $rules['secteur']        = 'required|string';
            $rules['siret']          = 'required|string';
        }

        if ($request->role === 'tuteur') {
            $rules['specialite'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);

        // Générer et stocker le code (même logique que le 2FA)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        session()->put('register_code', $code);
        session()->put('register_code_expiration', now()->addMinutes(15));

        // Envoyer le code (même Mail::raw que dans LoginController)
        Mail::raw("Votre code de vérification pour créer votre compte : $code\n\nCe code est valable 15 minutes.", function ($message) use ($request) {
            $message->to($request->email)->subject('Vérification de votre adresse email');
        });

        return redirect('/inscription/verification');
    }

    // ✅ NOUVEAU : affiche le formulaire de vérification du code
    public function showVerifyForm()
    {
        if (!session()->has('register_pending')) {
            return redirect('/inscription')->with('error', 'Session expirée. Veuillez recommencer l\'inscription.');
        }
        ob_start();
        include resource_path('views/auth/verify-register.php');
        return ob_get_clean();
    }

    // ✅ NOUVEAU : vérifie le code et crée le compte
    public function verifyAndCreate(Request $request)
    {
        $code    = trim($request->input('code'));
        $stored  = session('register_code');
        $expiry  = session('register_code_expiration');
        $pending = session('register_pending');

        if (!$pending || !$stored) {
            return redirect('/inscription')->with('error', 'Session expirée. Veuillez recommencer.');
        }

        if (now()->gt($expiry)) {
            session()->forget(['register_pending', 'register_code', 'register_code_expiration']);
            return redirect('/inscription')->with('error', 'Le code a expiré. Veuillez recommencer.');
        }

        if ($code !== $stored) {
            return redirect('/inscription/verification')->with('error', 'Code incorrect.');
        }

        // ✅ Code valide → créer le compte
        $user = Utilisateur::create([
            'nom'          => $pending['nom'],
            'prenom'       => $pending['prenom'],
            'email'        => $pending['email'],
            'mot_de_passe' => $pending['mot_de_passe'],
        ]);

        $roleData = [
            'id_utilisateur' => $user->id_utilisateur,
            'administrateur' => 0, 'etudiant' => 0,
            'entreprise'     => 0, 'tuteur'   => 0, 'jury' => 0,
        ];
        $roleData[$pending['role']] = 1;
        Role::create($roleData);

        switch ($pending['role']) {

            case 'etudiant':
                Etudiant::create([
                    'id_utilisateur' => $user->id_utilisateur,
                    'filiere'        => $pending['filiere'],
                    'niveau'         => $pending['niveau'],
                ]);
                break;

            case 'entreprise':
                Entreprise::create([
                    'id_utilisateur' => $user->id_utilisateur,
                    'nom_entreprise' => $pending['nom_entreprise'],
                    'adresse'        => $pending['adresse'],
                    'secteur'        => $pending['secteur'],
                    'siret'          => $pending['siret'],
                ]);
                break;

            case 'tuteur':
                Tuteur::create([
                    'id_utilisateur' => $user->id_utilisateur,
                    'specialite'     => $pending['specialite'],
                ]);
                break;

            case 'jury':
                Jury::create([
                    'id_utilisateur' => $user->id_utilisateur
                ]);
                break;

            case 'administrateur':
                Administrateur::create([
                    'id_utilisateur' => $user->id_utilisateur
                ]);
                break;
        }

        // Nettoyer la session
        session()->forget(['register_pending', 'register_code', 'register_code_expiration']);

        return redirect('/connexion')->with('success', 'Compte créé avec succès. Vous pouvez maintenant vous connecter.');
    }
}