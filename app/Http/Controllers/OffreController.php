<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use App\Models\Entreprise;
use Illuminate\Http\Request;

class OffreController extends Controller
{
    // Affiche le formulaire
    public function index()
    {
        include resource_path('views/entreprise/creerOffre.php');
    }

    // Traite la création de l'offre
    public function store(Request $request)
    {
        // Validation des champs
        $request->validate([
            'titre'       => 'required|string|max:150',
            'description' => 'required|string',
            'missions'    => 'required|string',
            'competences' => 'nullable|string',
            'duree'       => 'required|string|max:50',
        ], [
            'titre.required'       => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'missions.required'    => 'Les missions sont obligatoires.',
            'duree.required'       => 'La durée est obligatoire.',
        ]);

        // Récupérer l'entreprise liée à l'utilisateur connecté
        $entreprise = Entreprise::where('id_utilisateur', session('user_id'))->first();

        if (!$entreprise) {
            return redirect('/creer-offre')->with('error', 'Entreprise introuvable.');
        }

        // Créer l'offre en base
        OffreStage::create([
            'titre'        => $request->titre,
            'description'  => $request->description,
            'missions'     => $request->missions,
            'competences'  => $request->competences,
            'duree'        => $request->duree,
            'id_entreprise'=> $entreprise->id_entreprise,
        ]);

        return redirect('/entreprise')->with('success', 'Offre publiée avec succès !');
    }
}