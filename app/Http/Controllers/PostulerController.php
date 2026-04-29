<?php

namespace App\Http\Controllers;

use App\Models\OffreStage;
use App\Models\Etudiant;
use App\Models\Stage;
use Illuminate\Http\Request;

class PostulerController extends Controller
{
    public function index($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'etudiant') return redirect('/connexion');

        $offre = OffreStage::with('entreprise')->find($id);
        if (!$offre) abort(404);

        $idUtilisateur = session('user_id');

        // Vérifier si le CV existe
        $cheminCV = storage_path('app/private/Documents/' . $idUtilisateur . '/CV.pdf');
        $cvExiste = file_exists($cheminCV);

        // Vérifier si la lettre de motivation existe
        $cheminLM = storage_path('app/private/Documents/' . $idUtilisateur . '/LettreMotivation.pdf');
        $lmExiste = file_exists($cheminLM);

        // Vérifier si l'étudiant a déjà postulé à cette offre
        $etudiant = Etudiant::where('id_utilisateur', $idUtilisateur)->first();
        $dejaPostule = false;
        if ($etudiant) {
            $dejaPostule = Stage::where('id_etudiant', $etudiant->id_etudiant)
                               ->where('id_offre', $id)
                               ->exists();
        }

        include resource_path('views/etudiant/postuler.php');
    }

    public function store(Request $request, $id)
    {

        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'etudiant') return redirect('/connexion');

        $idUtilisateur = session('user_id');
        $dossier = storage_path('app/private/Documents/' . $idUtilisateur);

        if (!file_exists($dossier)) {
            mkdir($dossier, 0755, true);
        }

        // Sauvegarder le CV si fourni
        if ($request->hasFile('cv')) {
            $request->file('cv')->move($dossier, 'CV.pdf');
        }

        // Sauvegarder la lettre de motivation si fournie
        if ($request->hasFile('lettre_motivation')) {
            $request->file('lettre_motivation')->move($dossier, 'LettreMotivation.pdf');
        }

        // Vérifier que les deux documents existent
        $cvExiste = file_exists($dossier . '/CV.pdf');
        $lmExiste = file_exists($dossier . '/LettreMotivation.pdf');

        if (!$cvExiste || !$lmExiste) {
            return redirect('/postuler/' . $id)
                ->with('error', 'Veuillez fournir votre CV et votre lettre de motivation.');
        }

        // Récupérer l'étudiant
        $etudiant = Etudiant::where('id_utilisateur', $idUtilisateur)->first();

        if (!$etudiant) {
            return redirect('/postuler/' . $id)
                ->with('error', 'Profil étudiant introuvable.');
        }

        // Vérifier si l'étudiant a déjà postulé
        $dejaPostule = Stage::where('id_etudiant', $etudiant->id_etudiant)
                            ->where('id_offre', $id)
                            ->exists();

        if ($dejaPostule) {
            return redirect('/postuler/' . $id)
                ->with('error', 'Vous avez déjà postulé à cette offre.');
        }

        // Chemin relatif de la lettre de motivation
        $cheminLM = 'Documents/' . $idUtilisateur . '/LettreMotivation.pdf';

        // Créer la candidature avec le statut "en attente d'acceptation"
        Stage::create([
            'id_etudiant'        => $etudiant->id_etudiant,
            'id_offre'           => $id,
            'id_tuteur'          => null,
            'statut'             => "en attente d'acceptation",
            'lettre_motivation'  => $cheminLM,
            'date_debut'         => null,
            'date_fin'           => null,
        ]);

        return redirect('/offres')->with('success', 'Votre candidature a été envoyée avec succès !');
    }
}