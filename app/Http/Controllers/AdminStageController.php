<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use Illuminate\Http\Request;

class AdminStageController extends Controller
{
    public function index()
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'administrateur') return redirect('/connexion');

        $stages = Stage::with(['etudiant.utilisateur', 'offre.entreprise'])
            ->where('statut', 'en attente validation admin')
            ->get();

        include resource_path('views/admin/validationStages.php');
    }

    public function valider($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'administrateur') return redirect('/connexion');

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $stage->update(['statut' => 'accepté']);

        return redirect('/administrateur/validation')->with('success', 'Stage validé !');
    }

    public function refuser($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'administrateur') return redirect('/connexion');

        $stage = Stage::find($id);
        if (!$stage) abort(404);

        $stage->update(['statut' => 'refusé par admin']);

        return redirect('/administrateur/validation')->with('success', 'Stage refusé.');
    }
}