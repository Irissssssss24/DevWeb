<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Authentification;
use App\Models\Role;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TwoFactorController extends Controller
{
    /**
     * Affiche la page de vérification du code 2FA
     */
    public function showVerifyForm()
    {
        // Vérifier que l'utilisateur a une session 2FA en attente
        if (!session()->has('pending_2fa_user_id')) {
            return redirect('/connexion')->with('error', 'Session expirée');
        }

        return view('auth/verify-2fa');
    }

    /**
     * Vérifie le code 2FA entré par l'utilisateur
     */
    public function verify(Request $request)
    {
        // Validation du code
        $request->validate([
            'code' => 'required|string|size:6'
        ], [
            'code.required' => 'Le code est requis',
            'code.size' => 'Le code doit contenir 6 chiffres'
        ]);

        // Récupérer l'ID utilisateur depuis la session 2FA
        $userId = session('pending_2fa_user_id');

        if (!$userId) {
            return redirect('/connexion')->with('error', 'Session expirée');
        }

        // Récupérer le record d'authentification
        $auth = Authentification::where('id_utilisateur', $userId)->first();

        if (!$auth) {
            return redirect('/verification')->with('error', 'Code invalide ou expiré');
        }

        $inputCode = trim($request->input('code'));
        $storedCode = trim($auth->code_2fa);

        // Vérifier si le code correspond (comparaison stricte)
        if ($inputCode !== $storedCode) {
            return redirect('/verification')->with('error', 'Code incorrect');
        }

        // Vérifier si le code n'a pas expiré
        if (Carbon::now() > $auth->date_expiration) {
            // Réinitialiser le code expiré
            $auth->update([
                'code_2fa' => null,
                'date_expiration' => null
            ]);
            return redirect('/verification')->with('error', 'Code expiré. Veuillez vous reconnecter.');
        }

        // Code valide - établir la session complète
        $user = $auth->utilisateur;

        // Récupération du rôle depuis la table role
        $role = Role::where('id_utilisateur', $userId)->first();

        // Stocker les infos en session
        session()->put('user_id', $user->id_utilisateur);
        session()->put('nom', $user->nom);
        session()->put('prenom', $user->prenom);
        session()->put('email', $user->email);

        // Nettoyer la session 2FA
        session()->forget('pending_2fa_user_id');

        // Détermination du rôle et redirection
        if ($role) {
            // Récupération de tous les rôles actifs
            $rolesActifs = [];
            if ($role->administrateur == 1) $rolesActifs[] = 'administrateur';
            if ($role->etudiant == 1)       $rolesActifs[] = 'etudiant';
            if ($role->entreprise == 1)     $rolesActifs[] = 'entreprise';
            if ($role->tuteur == 1)         $rolesActifs[] = 'tuteur';
            if ($role->jury == 1)           $rolesActifs[] = 'jury';

            session()->put('roles', $rolesActifs);

            if (empty($rolesActifs)) {
                session()->flush();
                return redirect('/connexion')->with('error', 'role_inconnu');
            }

            // Redirection selon priorité
            $priorite = ['administrateur', 'tuteur', 'jury', 'entreprise', 'etudiant'];
            foreach ($priorite as $r) {
                if (in_array($r, $rolesActifs)) {
                    // Réinitialiser le code 2FA après utilisation
                    $auth->update([
                        'code_2fa' => null,
                        'date_expiration' => null
                    ]);
                    return redirect('/' . $r);
                }
            }
        }

        session()->flush();
        return redirect('/connexion')->with('error', 'role_inconnu');
    }

    /**
     * Retourner à la connexion (annuler la vérification 2FA)
     */
    public function cancel()
    {
        session()->forget('pending_2fa_user_id');
        return redirect('/connexion')->with('info', 'Vérification annulée');
    }
}
