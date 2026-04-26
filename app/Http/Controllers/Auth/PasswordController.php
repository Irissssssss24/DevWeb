<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    // Affiche le formulaire de changement de mot de passe
    public function index()
    {
        include resource_path('views/auth/changerMdp.php');
    }

    // Traite le changement de mot de passe
    public function update(Request $request)
    {
        $nouveau  = $request->input('nouveau', '');
        $confirmer = $request->input('confirmer', '');

        // Vérification de la longueur du mot de passe
        if (strlen($nouveau) < 8) {
            return redirect('/changer-mdp')->with('error', 'Le mot de passe doit contenir au moins 8 caractères.');
        }

        // Vérification que les deux mots de passe correspondent
        if ($nouveau !== $confirmer) {
            return redirect('/changer-mdp')->with('error', 'Les deux mots de passe ne correspondent pas.');
        }

        // Mise à jour du mot de passe en base de données
        Utilisateur::where('id_utilisateur', session('user_id'))
            ->update(['mot_de_passe' => Hash::make($nouveau)]);

        // Redirection selon le rôle
        $roles = session('roles', []);
        $redirections = [
            'administrateur' => '/admin',
            'etudiant'       => '/etudiant',
            'entreprise'     => '/entreprise',
            'tuteur'         => '/tuteur',
            'jury'           => '/jury',
        ];

        foreach ($redirections as $role => $url) {
            if (in_array($role, $roles)) {
                return redirect($url)->with('success', 'Mot de passe modifié avec succès.');
            }
        }

        return redirect('/connexion');
    }
}