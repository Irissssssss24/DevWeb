<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordController extends Controller
{
    public function index()
    {
        include resource_path('views/auth/changerMdp.php');
    }

    public function update(Request $request)
    {
        $email     = trim($request->input('email', ''));
        $role      = $request->input('role', '');
        $nouveau   = $request->input('nouveau', '');
        $confirmer = $request->input('confirmer', '');

        // Vérification des champs obligatoires
        if (empty($email) || empty($role)) {
            return redirect('/changer-mdp')->with('error', 'L\'adresse email et le rôle sont obligatoires.');
        }

        if (strlen($nouveau) < 8) {
            return redirect('/changer-mdp')->with('error', 'Le mot de passe doit contenir au moins 8 caractères.');
        }

        if ($nouveau !== $confirmer) {
            return redirect('/changer-mdp')->with('error', 'Les deux mots de passe ne correspondent pas.');
        }

        // Vérifier que l'utilisateur existe avec cet email
        $user = Utilisateur::where('email', $email)->first();
        if (!$user) {
            return redirect('/changer-mdp')->with('error', 'Aucun compte trouvé avec cette adresse email.');
        }

        // Vérifier que l'utilisateur a bien le rôle indiqué
        $roleEnBase = Role::where('id_utilisateur', $user->id_utilisateur)->first();
        $rolesValides = ['administrateur', 'etudiant', 'entreprise', 'tuteur', 'jury'];

        if (!$roleEnBase || !in_array($role, $rolesValides) || $roleEnBase->$role != 1) {
            return redirect('/changer-mdp')->with('error', 'Le rôle ne correspond pas à ce compte.');
        }

        // Stocker les infos en session
        session()->put('password_change_user_id', $user->id_utilisateur);
        session()->put('password_change_email', $email);
        session()->put('new_password', Hash::make($nouveau));

        // Générer le code (même logique que le 2FA)
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        session()->put('password_change_code', $code);
        session()->put('password_change_expiration', now()->addMinutes(15));

        // Envoyer le code (même Mail::raw que dans LoginController)
        Mail::raw(
            "Votre code de vérification pour changer votre mot de passe : $code\n\nCe code est valable 15 minutes.\n\nSi vous n'avez pas demandé ce changement, ignorez ce message.",
            function ($message) use ($email) {
                $message->to($email)->subject('Vérification du changement de mot de passe');
            }
        );

        return redirect('/changer-mdp/verification');
    }

    public function showVerifyForm()
    {
        if (!session()->has('new_password')) {
            return redirect('/changer-mdp')->with('error', 'Session expirée.');
        }
        include resource_path('views/auth/verify-password-change.php');
    }

    public function verifyAndUpdate(Request $request)
    {
        $code        = trim($request->input('code'));
        $stored      = session('password_change_code');
        $expiry      = session('password_change_expiration');
        $newPassword = session('new_password');
        $userId      = session('password_change_user_id');

        if (!$newPassword || !$stored || !$userId) {
            return redirect('/changer-mdp')->with('error', 'Session expirée. Veuillez recommencer.');
        }

        if (now()->gt($expiry)) {
            session()->forget(['new_password', 'password_change_code', 'password_change_expiration', 'password_change_user_id', 'password_change_email']);
            return redirect('/changer-mdp')->with('error', 'Le code a expiré. Veuillez recommencer.');
        }

        if ($code !== $stored) {
            return redirect('/changer-mdp/verification')->with('error', 'Code incorrect.');
        }

        // Code valide → appliquer le changement
        Utilisateur::where('id_utilisateur', $userId)
            ->update(['mot_de_passe' => $newPassword]);

        session()->forget(['new_password', 'password_change_code', 'password_change_expiration', 'password_change_user_id', 'password_change_email']);

        return redirect('/connexion')->with('success', 'Mot de passe modifié avec succès. Vous pouvez vous connecter.');
    }
}