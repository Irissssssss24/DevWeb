<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Etudiant;
use App\Models\Utilisateur;
use App\Models\OffreStage;
use Illuminate\Http\Request;

class CandidaturesController extends Controller
{
    // Liste des candidatures pour l'entreprise connectée
    public function index()
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        // Récupérer toutes les offres de l'entreprise avec les candidatures
        $stages = Stage::with(['etudiant.utilisateur', 'offre'])
            ->whereHas('offre.entreprise', function($q) {
                $q->where('id_utilisateur', session('user_id'));
            })
            ->orderBy('created_at', 'desc')
            ->get();

        include resource_path('views/entreprise/candidatures.php');
    }

    //Proposer dates
    public function proposerDates(Request $request, $id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $request->validate([
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $stage->update([
            'statut'              => 'dates proposées',
            'date_debut_proposee' => $request->date_debut,
            'date_fin_proposee'   => $request->date_fin,
        ]);

        return redirect('/entreprise')->with('success', 'Dates proposées à l\'étudiant !');
    }

    // Accepter la convention signée par l'entreprise et envoyer à l'admin
    public function envoyerConventionSignee(Request $request, $id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $dossier = storage_path('app/private/Documents/stage_' . $id);
        if (!file_exists($dossier)) mkdir($dossier, 0755, true);

        if ($request->hasFile('convention_signee')) {
            $request->file('convention_signee')->move($dossier, 'ConventionSignee.pdf');
            $stage->update([
                'statut'           => 'en attente validation admin',
                'convention_signee'=> 'stage_' . $id . '/ConventionSignee.pdf',
                'date_debut'       => $stage->date_debut_proposee,
                'date_fin'         => $stage->date_fin_proposee,
            ]);
        }

        return redirect('/entreprise')->with('success', 'Convention envoyée à l\'administrateur !');
    }

    // Refuser une candidature
    public function refuser($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $stage->update(['statut' => 'refusé']);

        return redirect('/entreprise')->with('success', 'Candidature refusée.');
    }

    // Voir le CV d'un étudiant
    public function voirCV($idUtilisateur)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $chemin = storage_path('app/private/Documents/' . $idUtilisateur . '/CV.pdf');
        if (!file_exists($chemin)) abort(404, 'CV non trouvé');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }

    // Voir la lettre de motivation d'un étudiant
    public function voirLettreMotivation($idUtilisateur)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $chemin = storage_path('app/private/Documents/' . $idUtilisateur . '/LettreMotivation.pdf');
        if (!file_exists($chemin)) abort(404, 'Lettre de motivation non trouvée');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }

    // Voir Convention
    public function voirConvention($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');

        $stage = Stage::find($id);
        if (!$stage || !$stage->convention_signee) abort(404);

        $chemin = storage_path('app/private/Documents/' . $stage->convention_signee);
        if (!file_exists($chemin)) abort(404);

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }
}