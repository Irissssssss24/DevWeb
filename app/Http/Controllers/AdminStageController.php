<?php

namespace App\Http\Controllers;

use App\Models\Administrateur;
use App\Models\Entreprise;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Jury;
use App\Models\Role;
use App\Models\Stage;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\DB;

class AdminStageController extends Controller
{
    private function verifierAdmin()
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'administrateur') return redirect('/connexion');

        return null;
    }

    public function index()
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $stages = Stage::with(['etudiant.utilisateur', 'offre.entreprise'])
            ->where('statut', 'en attente validation admin')
            ->get();

        include resource_path('views/admin/validationStages.php');
    }

    public function valider($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $stage->update(['statut' => 'validé']);

        return redirect('/administrateur/validation')->with('success', 'Stage validé !');
    }

    public function refuser($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $stage->update(['statut' => 'refusé par admin']);

        return redirect('/administrateur/validation')->with('success', 'Stage refusé.');
    }

    public function inscriptions()
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $inscriptions = Inscription::where('statut', 'en_attente')
            ->latest()
            ->get();

        include resource_path('views/admin/inscriptions.php');
    }

    public function accepterInscription($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $inscription = Inscription::find($id);
        if (!$inscription) abort(404);

        if ($inscription->statut !== 'en_attente') {
            return redirect('/administrateur/inscriptions')->with('error', 'Cette demande a déjà été traitée.');
        }

        $data = $inscription->data ?? [];
        $role = $data['role'] ?? null;

        if (!$role || !in_array($role, ['etudiant', 'entreprise', 'tuteur', 'jury', 'administrateur'], true)) {
            return redirect('/administrateur/inscriptions')->with('error', 'Rôle demandé invalide.');
        }

        if (Utilisateur::where('email', $data['email'] ?? null)->exists()) {
            return redirect('/administrateur/inscriptions')->with('error', 'Un utilisateur existe déjà avec cet email.');
        }

        DB::transaction(function () use ($data, $role, $inscription) {
            $utilisateur = Utilisateur::create([
                'nom' => $data['nom'] ?? null,
                'prenom' => $data['prenom'] ?? null,
                'email' => $data['email'] ?? null,
                'mot_de_passe' => $data['mot_de_passe'] ?? null,
            ]);

            Role::create([
                'id_utilisateur' => $utilisateur->id_utilisateur,
                'administrateur' => $role === 'administrateur' ? 1 : 0,
                'etudiant' => $role === 'etudiant' ? 1 : 0,
                'entreprise' => $role === 'entreprise' ? 1 : 0,
                'tuteur' => $role === 'tuteur' ? 1 : 0,
                'jury' => $role === 'jury' ? 1 : 0,
            ]);

            match ($role) {
                'etudiant' => Etudiant::create([
                    'id_utilisateur' => $utilisateur->id_utilisateur,
                    'filiere' => $data['filiere'] ?? null,
                    'niveau' => $data['niveau'] ?? null,
                    'cv' => '',
                ]),
                'entreprise' => Entreprise::create([
                    'id_utilisateur' => $utilisateur->id_utilisateur,
                    'nom_entreprise' => $data['nom_entreprise'] ?? null,
                    'adresse' => $data['adresse'] ?? null,
                    'secteur' => $data['secteur'] ?? null,
                    'siret' => $data['siret'] ?? null,
                ]),
                'tuteur' => Tuteur::create([
                    'id_utilisateur' => $utilisateur->id_utilisateur,
                    'specialite' => $data['specialite'] ?? null,
                ]),
                'jury' => Jury::create([
                    'id_utilisateur' => $utilisateur->id_utilisateur,
                ]),
                'administrateur' => Administrateur::create([
                    'id_utilisateur' => $utilisateur->id_utilisateur,
                ]),
            };

            $inscription->update(['statut' => 'accepte']);
        });

        return redirect('/administrateur/inscriptions')->with('success', 'Inscription validée.');
    }

    public function refuserInscription($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $inscription = Inscription::find($id);
        if (!$inscription) abort(404);

        $inscription->update(['statut' => 'refuse']);

        return redirect('/administrateur/inscriptions')->with('success', 'Inscription refusée.');
    }

    public function voirConvention($id)
    {
        if ($redirect = $this->verifierAdmin()) return $redirect;

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $chemin = null;
        if ($stage->convention) {
            $chemin = storage_path('app/private/Documents/' . $stage->convention);
        }

        if (!$chemin || !file_exists($chemin)) {
            $idUtilisateur = $stage->etudiant?->id_utilisateur;
            $chemin = $idUtilisateur
                ? storage_path('app/private/Documents/' . $idUtilisateur . '/ConventionDeStage.pdf')
                : null;
        }

        if (!$chemin || !file_exists($chemin)) abort(404, 'Convention de stage non trouvée');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }
}
