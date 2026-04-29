<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Authentification;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    // Affiche le formulaire de vérification
    public function index()
    {
        ob_start();
        include resource_path('views/auth/verify-2fa.php');
        return ob_get_clean();
    }

    // Vérifie le code 2FA entré par l'utilisateur
    public function verify(Request $request)
    {
        // Vérification du champ code
        if (empty($request->input('code'))) {
            return redirect('/verify-2fa')->with('error', 'Le code est requis.');
        }

        $code = trim($request->input('code'));

        // Récupérer l'id utilisateur stocké en session lors de la connexion
        $userId = session('2fa_user_id');

        if (!$userId) {
            return redirect('/connexion')->with('error', 'Session expirée. Veuillez vous reconnecter.');
        }

        // Chercher le code 2FA dans la table authentification
        $auth = Authentification::where('id_utilisateur', $userId)->first();

        if (!$auth) {
            return redirect('/connexion')->with('error', 'Aucun code trouvé. Veuillez vous reconnecter.');
        }

        // Vérifier si le code a expiré
        if (now()->gt($auth->date_expiration)) {
            $auth->delete();
            return redirect('/connexion')->with('error', 'Le code a expiré. Veuillez vous reconnecter.');
        }

        // Vérifier si le code est correct
        if ($auth->code_2fa !== $code) {
            return redirect('/verify-2fa')->with('error', 'Code incorrect.');
        }

        // supprimer l'entrée 2FA car le code est correct et valide
        $auth->delete();

        // session utilisateur complète
        $rolesActifs = session('2fa_roles');
        session()->put('user_id', $userId);
        session()->put('roles',   $rolesActifs);
        $role_choisi = session('role_actif');

        // Nettoyer les données temporaires 2FA
        session()->forget('2fa_user_id');
        session()->forget('2fa_roles');

        // Redirection selon priorité des rôles
        // sert à éviter de faire plusieurs redirections successives en cas de multi-rôles
        $priorite = ['administrateur', 'tuteur', 'jury', 'entreprise', 'etudiant'];
        
        if (in_array($role_choisi, $priorite)) {
            return redirect('/' . $role_choisi);
        }
        

        return redirect('/connexion')->with('error', 'role_inconnu');
    }
}