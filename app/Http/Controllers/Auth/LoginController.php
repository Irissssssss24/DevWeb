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
        // Si les champs sont vides
        if (empty($request->input('email')) || empty($request->input('password')) || empty($request->input('role'))) {
            return redirect('/connexion')->with('error', 'champs_vides');
        }

        $email       = trim($request->input('email'));
        $mdp         = $request->input('password');
        $roleChoisi  = $request->input('role'); // string : "etudiant", "tuteur"...

        // Récupération de l'utilisateur
        $user = Utilisateur::where('email', $email)->first();

        if (!$user || !Hash::check($mdp, $user->mot_de_passe)) {
            return redirect('/connexion')->with('error', 'identifiants_invalides');
        }

        // Récupération du rôle en base
        $roleEnBase = Role::where('id_utilisateur', $user->id_utilisateur)->first();

        if (!$roleEnBase) {
            return redirect('/connexion')->with('error', 'role_inconnu');
        }

        // Vérification que l'utilisateur a bien le rôle sélectionné
        if (!$roleEnBase->$roleChoisi || $roleEnBase->$roleChoisi != 1) {
            return redirect('/connexion')->with('error', 'role_invalide');
        }

        // Récupération de tous les rôles actifs
        $rolesActifs = [];
        if ($roleEnBase->administrateur == 1) $rolesActifs[] = 'administrateur';
        if ($roleEnBase->etudiant == 1)       $rolesActifs[] = 'etudiant';
        if ($roleEnBase->entreprise == 1)     $rolesActifs[] = 'entreprise';
        if ($roleEnBase->tuteur == 1)         $rolesActifs[] = 'tuteur';
        if ($roleEnBase->jury == 1)           $rolesActifs[] = 'jury';

        if (empty($rolesActifs)) {
            return redirect('/connexion')->with('error', 'role_inconnu');
        }

        // Régénérer la session
        $request->session()->regenerate();

        // --- 2FA : générer et envoyer le code ---
        Authentification::where('id_utilisateur', $user->id_utilisateur)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Authentification::create([
            'id_utilisateur'  => $user->id_utilisateur,
            'code_2fa'        => $code,
            'date_expiration' => now()->addMinutes(15),
        ]);

        // Stocker les infos en session temporaire (avant validation 2FA)
        session()->put('2fa_user_id', $user->id_utilisateur);
        session()->put('2fa_roles',   $rolesActifs);
        session()->put('pending_2fa_role_choisi', $roleChoisi);
        session()->put('nom',    $user->nom);
        session()->put('prenom', $user->prenom);
        session()->put('email',  $user->email);

        // Envoyer le code par mail
        Mail::raw("Votre code de vérification : $code\n\nCe code est valable 15 minutes.", function ($message) use ($user) {
            $message->to($user->email)->subject('Votre code de vérification');
        });

        return redirect('/verification');
    }

    public function deconnexion(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();
        return redirect('/accueil');
    }
}