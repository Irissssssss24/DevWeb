<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth/Portail_Connexion');
    }

    public function verifier(Request $request)
    {
        //si les champs sont vides, on redirige vers la page de connexion avec un message d'erreur
        if (empty($request->input('email')) || empty($request->input('password'))) {
            return redirect('/connexion')->with('error', 'champs_vides');
        }

        // Récupération de l'adresse mail et du mot de passe 
        $email = trim($request->input('email'));
        $mdp   = $request->input('password');

        //lie l'adresse mail entré à un utilisateur si il existe déjà
        $user = Utilisateur::where('email', $email)->first();

        //test si le mod de passe entré et haché correspond au bon mot de passe haché
        if ($user && Hash::check($mdp, $user->mot_de_passe)) {

            //si le test est bon on ouvre la session
            $request->session()->regenerate();

            // Récupération du rôle depuis la table role
            $role = Role::where('id_utilisateur', $user->id_utilisateur)->first();

            // Stockage en session
            session()->put('user_id', $user->id_utilisateur);
            session()->put('nom',     $user->nom);
            session()->put('prenom',  $user->prenom);
            session()->put('email',   $user->email);

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
                    return redirect('/connexion')->with('error', 'role_inconnu');
                }

                // Redirection selon priorité
                $priorite = ['administrateur', 'tuteur', 'jury', 'entreprise', 'etudiant'];
                foreach ($priorite as $r) {
                    if (in_array($r, $rolesActifs)) {
                        return redirect('/' . $r);
                    }
                }
            }

            // Aucun rôle trouvé
            return redirect('/connexion')->with('error', 'role_inconnu');
        }

        //si le test n'est pas bon alors soit le mot de passe n'est pas bon, soit l'adresse mail n'est lié à aucun utilisateur
        return redirect('/connexion')->with('error', 'identifiants_invalides');
    }

    public function deconnexion(Request $request)
    {
        $request->session()->flush();
        $request->session()->regenerate();

        return redirect('/accueil');
    }
}