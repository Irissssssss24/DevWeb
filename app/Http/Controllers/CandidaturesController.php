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

    // Accepter une candidature
    public function accepter(Request $request, $id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $request->validate([
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ], [
            'date_debut.required' => 'La date de début est obligatoire.',
            'date_fin.required'   => 'La date de fin est obligatoire.',
            'date_fin.after'      => 'La date de fin doit être après la date de début.',
        ]);

        $stage = Stage::find($id);

        if (!$stage) abort(404);

        $stage->update([
            'statut'     => 'accepté',
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
        ]);

        return redirect('/entreprise')->with('success', 'Candidature acceptée !');
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
}