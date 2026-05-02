<?php

namespace App\Http\Controllers;

use App\Models\Administrateur;
use App\Models\Entreprise;
use App\Models\Etudiant;
use App\Models\Jury;
use App\Models\Role;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    private function verifierAdmin()
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'administrateur') return redirect('/connexion');
        return null;
    }

    // PAGE D'ACCUEIL ADMIN
    public function dashboard()
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $totalUtilisateurs = Utilisateur::count();
        $totalEtudiants    = Etudiant::count();
        $totalEntreprises  = Entreprise::count();
        $totalTuteurs      = Tuteur::count();
        $totalJurys        = Jury::count();

        // On récupère les utilisateurs pour le tableau de bord
        $utilisateurs = Utilisateur::with('role')->orderBy('nom')->get();

        include resource_path('views/admin/admin.php');
    }

    // LISTE DE TOUS LES UTILISATEURS
    public function listeUtilisateurs(Request $request)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $recherche  = $request->input('recherche', '');
        $filtreRole = $request->input('role', '');

        $query = Utilisateur::with('role');

        if ($recherche) {
            $query->where(function ($q) use ($recherche) {
                $q->where('nom', 'ilike', "%$recherche%")
                  ->orWhere('prenom', 'ilike', "%$recherche%")
                  ->orWhere('email', 'ilike', "%$recherche%");
            });
        }

        if ($filtreRole && in_array($filtreRole, ['etudiant', 'entreprise', 'tuteur', 'jury', 'administrateur'])) {
            $query->whereHas('role', function ($q) use ($filtreRole) {
                $q->where($filtreRole, 1);
            });
        }

        $utilisateurs = $query->orderBy('nom')->get();

        include resource_path('views/admin/utilisateurs.php');
    }

    // FORMULAIRE CREATION
    public function creerUtilisateur()
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;
        include resource_path('views/admin/creerUtilisateur.php');
    }

    // TRAITEMENT CREATION
    public function storeUtilisateur(Request $request)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $request->validate([
            'nom'          => 'required|string|max:255',
            'prenom'       => 'required|string|max:255',
            'email'        => 'required|email|unique:utilisateur,email',
            'mot_de_passe' => 'required|string|min:8|confirmed',
            'role'         => 'required|in:etudiant,entreprise,tuteur,jury,administrateur',
        ], [
            'email.unique'           => 'Cet email est deja utilise.',
            'mot_de_passe.min'       => 'Le mot de passe doit faire au moins 8 caracteres.',
            'mot_de_passe.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        DB::transaction(function () use ($request) {
            $utilisateur = Utilisateur::create([
                'nom'          => $request->nom,
                'prenom'       => $request->prenom,
                'email'        => $request->email,
                'mot_de_passe' => Hash::make($request->mot_de_passe),
            ]);

            $roleData = [
                'id_utilisateur' => $utilisateur->id_utilisateur,
                'etudiant'       => 0,
                'entreprise'     => 0,
                'tuteur'         => 0,
                'jury'           => 0,
                'administrateur' => 0,
            ];
            $roleData[$request->role] = 1;
            Role::create($roleData);

            switch ($request->role) {
                case 'etudiant':
                    Etudiant::create([
                        'id_utilisateur' => $utilisateur->id_utilisateur,
                        'filiere'        => $request->filiere ?? '',
                        'niveau'         => $request->niveau ?? '',
                    ]);
                    break;
                case 'entreprise':
                    Entreprise::create([
                        'id_utilisateur' => $utilisateur->id_utilisateur,
                        'nom_entreprise' => $request->nom_entreprise ?? '',
                        'adresse'        => $request->adresse ?? '',
                        'secteur'        => $request->secteur ?? '',
                        'siret'          => $request->siret ?? '',
                    ]);
                    break;
                case 'tuteur':
                    Tuteur::create([
                        'id_utilisateur' => $utilisateur->id_utilisateur,
                        'specialite'     => $request->specialite ?? '',
                    ]);
                    break;
                case 'jury':
                    Jury::create(['id_utilisateur' => $utilisateur->id_utilisateur]);
                    break;
                case 'administrateur':
                    Administrateur::create(['id_utilisateur' => $utilisateur->id_utilisateur]);
                    break;
            }
        });

        return redirect('/administrateur/utilisateurs')->with('success', 'Utilisateur cree avec succes !');
    }

    // FORMULAIRE MODIFICATION
    public function modifierUtilisateur($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $utilisateur = Utilisateur::with(['role', 'etudiant', 'entreprise', 'tuteur'])->find($id);
        if (!$utilisateur) abort(404);

        include resource_path('views/admin/modifierUtilisateur.php');
    }

    // TRAITEMENT MODIFICATION
    public function updateUtilisateur(Request $request, $id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) abort(404);

        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email|unique:utilisateur,email,' . $id . ',id_utilisateur',
        ]);

        $utilisateur->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
        ]);

        if ($request->filled('mot_de_passe')) {
            $request->validate(['mot_de_passe' => 'min:8|confirmed']);
            $utilisateur->update(['mot_de_passe' => Hash::make($request->mot_de_passe)]);
        }

        $role = Role::where('id_utilisateur', $id)->first();
        if ($role) {
            if ($role->etudiant) {
                Etudiant::where('id_utilisateur', $id)->update([
                    'filiere' => $request->filiere ?? '',
                    'niveau'  => $request->niveau ?? '',
                ]);
            }
            if ($role->entreprise) {
                Entreprise::where('id_utilisateur', $id)->update([
                    'nom_entreprise' => $request->nom_entreprise ?? '',
                    'adresse'        => $request->adresse ?? '',
                    'secteur'        => $request->secteur ?? '',
                    'siret'          => $request->siret ?? '',
                ]);
            }
            if ($role->tuteur) {
                Tuteur::where('id_utilisateur', $id)->update([
                    'specialite' => $request->specialite ?? '',
                ]);
            }
        }

        return redirect('/administrateur/utilisateurs')->with('success', 'Utilisateur modifie avec succes !');
    }

    // SUPPRESSION
    public function supprimerUtilisateur($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        if ($id === session('user_id')) {
            return redirect('/administrateur/utilisateurs')->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) abort(404);

        DB::transaction(function () use ($id) {
            Role::where('id_utilisateur', $id)->delete();
            Etudiant::where('id_utilisateur', $id)->delete();
            Entreprise::where('id_utilisateur', $id)->delete();
            Tuteur::where('id_utilisateur', $id)->delete();
            Jury::where('id_utilisateur', $id)->delete();
            Administrateur::where('id_utilisateur', $id)->delete();
            Utilisateur::where('id_utilisateur', $id)->delete();
        });

        return redirect('/administrateur/utilisateurs')->with('success', 'Utilisateur supprime.');
    }

    // ATTRIBUTION / REVOCATION DE ROLES
    public function modifierRoles(Request $request, $id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        if ($id === session('user_id')) {
            return redirect('/administrateur/utilisateurs')->with('error', 'Vous ne pouvez pas modifier vos propres roles.');
        }

        $utilisateur = Utilisateur::find($id);
        if (!$utilisateur) abort(404);

        $roles = ['etudiant', 'entreprise', 'tuteur', 'jury', 'administrateur'];
        $roleData = [];
        foreach ($roles as $r) {
            $roleData[$r] = $request->has('role_' . $r) ? 1 : 0;
        }

        if (array_sum($roleData) === 0) {
            return redirect('/administrateur/modifier/' . $id)->with('error', "L'utilisateur doit avoir au moins un role.");
        }

        Role::updateOrCreate(
            ['id_utilisateur' => $id],
            $roleData
        );

        return redirect('/administrateur/utilisateurs')->with('success', 'Roles mis a jour avec succes !');
    }
}
