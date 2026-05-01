<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Stage;
use App\Models\Etudiant;
use Illuminate\Http\Request;

class EtudiantStageController extends Controller
{
    private function stageEtudiant($id)
    {
        $etudiant = Etudiant::where('id_utilisateur', session('user_id'))->first();
        if (!$etudiant) abort(404);

        $stage = Stage::where('id_stage', $id)
            ->where('id_etudiant', $etudiant->id_etudiant)
            ->first();

        if (!$stage) abort(404);

        return $stage;
    }

    // Accepter les dates proposées par l'entreprise
    public function accepterDates($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'etudiant') return redirect('/connexion');

        $stage = $this->stageEtudiant($id);
        if ($stage->statut !== 'dates proposées') abort(404);

        $stage->update([
            'statut' => 'en attente convention',
            'date_debut' => $stage->date_debut_proposee,
            'date_fin' => $stage->date_fin_proposee,
        ]);

        return redirect('/etudiant')->with('success', 'Dates acceptées ! Veuillez maintenant déposer votre convention de stage.');
    }

    // Refuser les dates proposées
    public function refuserDates($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'etudiant') return redirect('/connexion');

        $stage = $this->stageEtudiant($id);
        if ($stage->statut !== 'dates proposées') abort(404);

        $stage->update([
            'statut'              => "en attente d'acceptation",
            'date_debut_proposee' => null,
            'date_fin_proposee'   => null,
        ]);

        return redirect('/etudiant')->with('info', 'Dates refusées. L\'entreprise sera notifiée.');
    }

    // Upload de la convention de stage
    public function uploadConvention(Request $request, $id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'etudiant') return redirect('/connexion');

        $stage = $this->stageEtudiant($id);
        if ($stage->statut !== 'en attente convention') abort(404);

        $dossier = storage_path('app/private/Documents/stage_' . $id);
        if (!file_exists($dossier)) mkdir($dossier, 0755, true);

        if ($request->hasFile('convention')) {
            $request->file('convention')->move($dossier, 'Convention.pdf');
            Document::updateOrCreate(
                ['id_stage' => $stage->id_stage, 'type' => 'convention'],
                ['fichier' => 'stage_' . $id . '/Convention.pdf']
            );
            $stage->update([
                'statut'     => 'convention soumise',
                'convention' => 'stage_' . $id . '/Convention.pdf',
            ]);
            return redirect('/etudiant')->with('success', 'Convention déposée ! L\'entreprise doit maintenant la signer.');
        }

        return redirect('/etudiant')->with('error', 'Aucun fichier sélectionné.');
    }
}
