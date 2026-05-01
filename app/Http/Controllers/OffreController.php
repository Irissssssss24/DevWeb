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
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        // Validation des champs
        $request->validate([
            'titre'       => 'required|string|max:150',
            'description' => 'required|string',
            'missions'    => 'required|string',
            'competences' => 'nullable|string',
            'duree'       => 'required|string|max:150',
        ], [
            'titre.required'       => 'Le titre est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'missions.required'    => 'Les missions sont obligatoires.',
            'duree.required'       => 'La durée est obligatoire.',
            'duree.max'            => 'La durée ne doit pas dépasser 150 caractères.',
        ]);

        // Récupérer l'entreprise liée à l'utilisateur connecté
        $entreprise = Entreprise::where('id_utilisateur', session('user_id'))->first();

        if (!$entreprise) {
            return redirect('/creer-offre')
                ->withInput()
                ->with('error', 'Profil entreprise introuvable. Complétez ou recréez votre profil entreprise avant de publier une offre.');
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

        return redirect('/entreprise/candidatures')->with('success', 'Offre publiée avec succès !');
    }
}
