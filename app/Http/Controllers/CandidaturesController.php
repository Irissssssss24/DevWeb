<?php

namespace App\Http\Controllers;

use App\Models\Stage;
use App\Models\Entreprise;
use App\Models\OffreStage;
use App\Models\Remarque;
use Illuminate\Http\Request;

class CandidaturesController extends Controller
{
    private function entrepriseConnectee()
    {
        return Entreprise::where('id_utilisateur', session('user_id'))->first();
    }

    private function stageEntreprise($idStage, array $with = [])
    {
        $entreprise = $this->entrepriseConnectee();
        if (!$entreprise) abort(404);

        $query = Stage::with($with)
            ->where('id_stage', $idStage)
            ->whereHas('offre', function ($q) use ($entreprise) {
                $q->where('id_entreprise', $entreprise->id_entreprise);
            });

        $stage = $query->first();
        if (!$stage) abort(404);

        return $stage;
    }

    private function etudiantAccessible($idUtilisateur)
    {
        $entreprise = $this->entrepriseConnectee();
        if (!$entreprise) abort(404);

        return Stage::whereHas('offre', function ($q) use ($entreprise) {
                $q->where('id_entreprise', $entreprise->id_entreprise);
            })
            ->whereHas('etudiant', function ($q) use ($idUtilisateur) {
                $q->where('id_utilisateur', $idUtilisateur);
            })
            ->exists();
    }

    private function statutAccepte($statut)
    {
        return in_array($statut, ['accepté', 'en_cours', 'validé']);
    }

    private function etudiantAccepteAccessible($idUtilisateur)
    {
        $entreprise = $this->entrepriseConnectee();
        if (!$entreprise) abort(404);

        return Stage::whereHas('offre', function ($q) use ($entreprise) {
                $q->where('id_entreprise', $entreprise->id_entreprise);
            })
            ->whereHas('etudiant', function ($q) use ($idUtilisateur) {
                $q->where('id_utilisateur', $idUtilisateur);
            })
            ->whereIn('statut', ['accepté', 'en_cours', 'validé'])
            ->exists();
    }

    private function offresEntreprise($entreprise, array $with = [])
    {
        return OffreStage::with($with)
            ->where('id_entreprise', $entreprise->id_entreprise)
            ->orderBy('created_at', 'desc');
    }

    // -------------------------------------------------------
    // Page d'accueil entreprise = candidats acceptés uniquement
    // -------------------------------------------------------
    public function index()
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        // Récupérer l'entreprise liée à l'utilisateur connecté
        $entreprise = $this->entrepriseConnectee();

        if (!$entreprise) {
            // Profil entreprise non encore créé
            $offres = collect();
            $toutesLesOffres = collect();
            $pageMode = 'accueil';
            include resource_path('views/entreprise/entreprise.php');
            return;
        }

        $statutsAcceptes = ['accepté', 'en_cours', 'validé'];
        $pageMode = 'accueil';
        $toutesLesOffres = $this->offresEntreprise($entreprise)->get();

        // Accueil : seulement les offres qui ont au moins un candidat accepté.
        $offres = $this->offresEntreprise($entreprise, [
                'stages' => function ($q) use ($statutsAcceptes) {
                    $q->whereIn('statut', $statutsAcceptes);
                },
                'stages.etudiant.utilisateur',
                'stages.documents',
                'stages.remarques',
            ])
            ->whereHas('stages', function ($q) use ($statutsAcceptes) {
                $q->whereIn('statut', $statutsAcceptes);
            })
            ->get();

        include resource_path('views/entreprise/entreprise.php');
    }

    // -------------------------------------------------------
    // Page voir les candidats = toutes les candidatures avec filtre par offre
    // -------------------------------------------------------
    public function candidats(Request $request)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $entreprise = $this->entrepriseConnectee();

        if (!$entreprise) {
            $offres = collect();
            $toutesLesOffres = collect();
            $filtreOffreId = null;
            $pageMode = 'candidatures';
            include resource_path('views/entreprise/entreprise.php');
            return;
        }

        $toutesLesOffres = $this->offresEntreprise($entreprise)->get();
        $filtreOffreId = $request->input('offre');
        $pageMode = 'candidatures';

        $offres = $this->offresEntreprise($entreprise, [
                'stages.etudiant.utilisateur',
                'stages.documents',
                'stages.remarques',
            ])
            ->when($filtreOffreId, function ($query) use ($filtreOffreId) {
                $query->where('id_offre', $filtreOffreId);
            })
            ->get();

        include resource_path('views/entreprise/entreprise.php');
    }

    // -------------------------------------------------------
    // Accepter une candidature
    // -------------------------------------------------------
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

        $stage = $this->stageEntreprise($id);

        $stage->update([
            'statut'     => 'accepté',
            'date_debut' => $request->date_debut,
            'date_fin'   => $request->date_fin,
        ]);

        return redirect()->back()->with('success', 'Candidature acceptée !');
    }

    // -------------------------------------------------------
    // Refuser une candidature
    // -------------------------------------------------------
    public function refuser($id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $stage = $this->stageEntreprise($id);

        $stage->update(['statut' => 'refusé']);

        return redirect()->back()->with('success', 'Candidature refusée.');
    }

    // -------------------------------------------------------
    // Valider la convention de stage
    // -------------------------------------------------------
    public function validerConvention(Request $request, $id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $stage = $this->stageEntreprise($id);
        if (!$this->statutAccepte($stage->statut)) abort(404);

        $stage->update([
            'convention_validee'   => true,
            'remarque_convention'  => null,
        ]);

        return redirect()->back()->with('success', 'Convention de stage validée !');
    }

    // -------------------------------------------------------
    // Refuser la convention de stage (avec remarque optionnelle)
    // -------------------------------------------------------
    public function refuserConvention(Request $request, $id)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $request->validate([
            'remarque_convention' => 'nullable|string|max:1000',
        ]);

        $stage = $this->stageEntreprise($id);
        if (!$this->statutAccepte($stage->statut)) abort(404);

        $stage->update([
            'convention_validee'  => false,
            'remarque_convention' => $request->remarque_convention,
        ]);

        return redirect()->back()->with('success', 'Convention refusée. L\'étudiant en sera informé.');
    }

    // -------------------------------------------------------
    // Ajouter une remarque pour un étudiant
    // -------------------------------------------------------
    public function ajouterRemarque(Request $request, $idStage)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');

        $request->validate([
            'contenu' => 'required|string|max:2000',
        ], [
            'contenu.required' => 'La remarque ne peut pas être vide.',
        ]);

        $stage = $this->stageEntreprise($idStage);
        if (!$this->statutAccepte($stage->statut)) abort(404);

        Remarque::create([
            'contenu'        => $request->contenu,
            'date'           => now(),
            'id_stage'       => $idStage,
            'id_utilisateur' => session('user_id'),
        ]);

        return redirect()->back()->with('success', 'Remarque ajoutée avec succès.');
    }

    // -------------------------------------------------------
    // Voir le CV d'un étudiant
    // -------------------------------------------------------
    public function voirCV($idUtilisateur)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');
        if (!$this->etudiantAccessible($idUtilisateur)) abort(404);

        $chemin = storage_path('app/private/Documents/' . $idUtilisateur . '/CV.pdf');
        if (!file_exists($chemin)) abort(404, 'CV non trouvé');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }

    // -------------------------------------------------------
    // Voir la lettre de motivation d'un étudiant
    // -------------------------------------------------------
    public function voirLettreMotivation($idUtilisateur)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');
        if (!$this->etudiantAccessible($idUtilisateur)) abort(404);

        $chemin = storage_path('app/private/Documents/' . $idUtilisateur . '/LettreMotivation.pdf');
        if (!file_exists($chemin)) abort(404, 'Lettre de motivation non trouvée');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }

    // -------------------------------------------------------
    // Voir la convention de stage d'un étudiant
    // -------------------------------------------------------
    public function voirConvention($idUtilisateur)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');
        if (!$this->etudiantAccepteAccessible($idUtilisateur)) abort(404);

        $chemin = storage_path('app/private/Documents/' . $idUtilisateur . '/ConventionDeStage.pdf');
        if (!file_exists($chemin)) abort(404, 'Convention de stage non trouvée');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }

    // -------------------------------------------------------
    // Voir un document quelconque d'un étudiant
    // -------------------------------------------------------
    public function voirDocument($idUtilisateur, $type)
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        if (session('role_actif') !== 'entreprise') return redirect('/connexion');
        if (!$this->etudiantAccepteAccessible($idUtilisateur)) abort(404);

        $nomsDoc = [
            'rapport'    => 'RapportDeStage.pdf',
            'convention' => 'ConventionDeStage.pdf',
            'evaluation' => 'FicheEvaluation.pdf',
            'resume'     => 'ResumeDeStage.pdf',
        ];

        if (!isset($nomsDoc[$type])) abort(404, 'Type de document invalide');

        $chemin = storage_path('app/private/Documents/' . $idUtilisateur . '/' . $nomsDoc[$type]);
        if (!file_exists($chemin)) abort(404, 'Document non trouvé');

        return response()->file($chemin, ['Content-Type' => 'application/pdf']);
    }
}
