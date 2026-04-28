<?php

namespace App\Http\Controllers\Auth;

//on importe les classes nécessaires pour le fonctionnement du contrôleur
//classe de base pour les contrôleurs Laravel
use App\Http\Controllers\Controller;
//importe la bdd
use App\Models\Utilisateur;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\Administrateur;
//classe de Laravel 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class RegisterController extends Controller
{
    //fonction qui affiche le formulaire d'inscription
    public function index()
    {
        ob_start();
        include resource_path('views/auth/nvUtil.php');
        return ob_get_clean();
    }

    //fonction qui traite les données du formulaire d'inscription
    //$request contient les données du formulaire envoyées par la méthode POST
    public function register(Request $request)
    {
        // Validator récupère les données du formulaire et vérifie qu'elles respectent les règles définies
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:utilisateur,email',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            'role' => 'required|in:etudiant,entreprise,tuteur,jury,administrateur',
        ]);

        //redirection vers le formulaire d'inscription avec les erreurs et les données précédemment saisies si la validation échoue
        if ($validator->fails()) {
            return redirect('/inscription')->withErrors($validator)->withInput();
        }

        // Création de l'utilisateur dans la table utilisateur
        $user = Utilisateur::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            'mot_de_passe' => Hash::make($request->mot_de_passe),
        ]);

        // Création du rôle qui est un booléen : on passe à 1 le rôle choisi et les autres restent à 0
        $roleData = [
            'id_utilisateur' => $user->id_utilisateur,
            'administrateur' => 0,
            'etudiant' => 0,
            'entreprise' => 0,
            'tuteur' => 0,
            'jury' => 0,
        ];

        $role_actif = session('role_actif');

        // Activer le rôle choisi
        $roleData[$role_actif] = 1;

        // Création de l'entrée dans la table role choisie
        Role::create($roleData);

        // Création de l'entité spécifique selon le rôle
        switch ($request->role) {
            case 'etudiant':
                Etudiant::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'entreprise':
                Entreprise::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'tuteur':
                Tuteur::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'jury':
                Jury::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
            case 'administrateur':
                Administrateur::create(['id_utilisateur' => $user->id_utilisateur]);
                break;
        }

        // Redirection vers la connexion avec un message de succès
        return redirect('/connexion')->with('success', 'Inscription réussie. Vous pouvez maintenant vous connecter.');
    }
}