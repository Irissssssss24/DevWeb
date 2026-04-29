<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use Illuminate\Http\Request;

class VoirOffreController extends Controller
{
    public function index(Request $request)
    {
        $recherche = $request->input('recherche', '');

        // Si une recherche est effectuée, on filtre les offres
        $offres = OffreStage::with('entreprise')
            ->when($recherche, function ($query) use ($recherche) {
                $query->where('titre', 'ilike', "%$recherche%")
                      ->orWhere('description', 'ilike', "%$recherche%")
                      ->orWhere('competences', 'ilike', "%$recherche%")
                      ->orWhere('missions', 'ilike', "%$recherche%")
                      ->orWhereHas('entreprise', function ($q) use ($recherche) {
                          $q->where('nom_entreprise', 'ilike', "%$recherche%")
                            ->orWhere('secteur', 'ilike', "%$recherche%");
                      });
            })
            ->get();

        include resource_path('views/general/voirOffre.php');
    }
}