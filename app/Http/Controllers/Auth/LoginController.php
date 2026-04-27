<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Role;
use App\Models\Authentification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth/Portail_Connexion');
    }

    public function verifier(Request $request)
    {
        // Si les champs sont vides, on redirige vers la page de connexion avec un message d'erreur
        if (empty($request->input('email')) || empty($request->input('password'))) {
            return redirect('/connexion')->with('error', 'champs_vides');
        }

        // Récupération de l'adresse mail et du mot de passe
        $email = trim($request->input('email'));
        $mdp   = $request->input('password');

        // Lie l'adresse mail entrée à un utilisateur s'il existe déjà
        $user = Utilisateur::where('email', $email)->first();

        // Test si le mot de passe entré et haché correspond au bon mot de passe haché
        if ($user && Hash::check($mdp, $user->mot_de_passe)) {

            // Si le test est bon on ouvre la session
            $request->session()->regenerate();

            // Récupération du rôle depuis la table role
            $role = Role::where('id_utilisateur', $user->id_utilisateur)->first();

            // Détermination des rôles actifs
            $rolesActifs = [];
            if ($role) {
                if ($role->administrateur == 1) $rolesActifs[] = 'administrateur';
                if ($role->etudiant == 1)       $rolesActifs[] = 'etudiant';
                if ($role->entreprise == 1)     $rolesActifs[] = 'entreprise';
                if ($role->tuteur == 1)         $rolesActifs[] = 'tuteur';
                if ($role->jury == 1)           $rolesActifs[] = 'jury';
            }

            if (empty($rolesActifs)) {
                return redirect('/connexion')->with('error', 'role_inconnu');
            }

            // --- 2FA : générer et envoyer le code ---

            // Supprimer tout ancien code existant pour cet utilisateur
            Authentification::where('id_utilisateur', $user->id_utilisateur)->delete();

            // Générer un code à 6 chiffres
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Stocker le code en base avec expiration 15 min
            Authentification::create([
                'id_utilisateur' => $user->id_utilisateur,
                'code_2fa'       => $code,
                'date_expiration'=> now()->addMinutes(15),
            ]);

            // Stocker les infos en session (sans encore valider la connexion)
            session()->put('2fa_user_id',  $user->id_utilisateur);
            session()->put('2fa_roles',    $rolesActifs);
            session()->put('nom',          $user->nom);
            session()->put('prenom',       $user->prenom);
            session()->put('email',        $user->email);

            // Envoyer le code par mail
            Mail::raw("Votre code de vérification : $code\n\nCe code est valable 15 minutes.", function ($message) use ($user) {
            $message->to($user->email)->subject('Votre code de vérification');
            });

            return redirect('/verification');
        }

        // Si le test n'est pas bon : mot de passe incorrect ou email inconnu
        return redirect('/connexion')->with('error', 'identifiants_invalides');
    }

    public function deconnexion(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect('/accueil');
    }
}