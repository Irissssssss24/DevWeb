<?php

namespace App\Http\Controllers;

use App\Models\Entreprise;
use App\Models\Etudiant;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    private function verifierConnexion()
    {
        if (!session()->has('user_id')) return redirect('/connexion');
        return null;
    }

    // ─── MISE À JOUR PROFIL ÉTUDIANT ────────────────────────────────────────
    public function updateEtudiant(Request $request)
    {
        if ($redirect = $this->verifierConnexion()) return $redirect;

        $request->validate([
            'nom'     => 'required|string|max:255',
            'prenom'  => 'required|string|max:255',
            'email'   => 'required|email|unique:utilisateur,email,' . session('user_id') . ',id_utilisateur',
            'filiere' => 'nullable|string|max:255',
            'niveau'  => 'nullable|string|max:50',
        ]);

        $userId = session('user_id');

        Utilisateur::where('id_utilisateur', $userId)->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
        ]);

        Etudiant::where('id_utilisateur', $userId)->updateOrCreate(
            ['id_utilisateur' => $userId],
            ['filiere' => $request->filiere ?? '', 'niveau' => $request->niveau ?? '']
        );

        session(['nom' => $request->nom, 'prenom' => $request->prenom, 'email' => $request->email]);

        return redirect('/etudiant/profil')->with('success', 'Profil mis à jour avec succès !');
    }

    // ─── MISE À JOUR PROFIL ENTREPRISE ─────────────────────────────────────
    public function updateEntreprise(Request $request)
    {
        if ($redirect = $this->verifierConnexion()) return $redirect;

        $request->validate([
            'nom'            => 'required|string|max:255',
            'prenom'         => 'required|string|max:255',
            'email'          => 'required|email|unique:utilisateur,email,' . session('user_id') . ',id_utilisateur',
            'nom_entreprise' => 'nullable|string|max:255',
            'adresse'        => 'nullable|string|max:500',
            'secteur'        => 'nullable|string|max:255',
            'siret'          => 'nullable|string|max:14',
        ]);

        $userId = session('user_id');

        Utilisateur::where('id_utilisateur', $userId)->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
        ]);

        Entreprise::where('id_utilisateur', $userId)->update([
            'nom_entreprise' => $request->nom_entreprise ?? '',
            'adresse'        => $request->adresse ?? '',
            'secteur'        => $request->secteur ?? '',
            'siret'          => $request->siret ?? '',
        ]);

        session(['nom' => $request->nom, 'prenom' => $request->prenom, 'email' => $request->email]);

        return redirect('/entreprise/profil')->with('success', 'Profil mis à jour avec succès !');
    }

    // ─── MISE À JOUR PROFIL TUTEUR ──────────────────────────────────────────
    public function updateTuteur(Request $request)
    {
        if ($redirect = $this->verifierConnexion()) return $redirect;

        $request->validate([
            'nom'        => 'required|string|max:255',
            'prenom'     => 'required|string|max:255',
            'email'      => 'required|email|unique:utilisateur,email,' . session('user_id') . ',id_utilisateur',
            'specialite' => 'nullable|string|max:255',
        ]);

        $userId = session('user_id');

        Utilisateur::where('id_utilisateur', $userId)->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
        ]);

        Tuteur::where('id_utilisateur', $userId)->update([
            'specialite' => $request->specialite ?? '',
        ]);

        session(['nom' => $request->nom, 'prenom' => $request->prenom, 'email' => $request->email]);

        return redirect('/tuteur/profil')->with('success', 'Profil mis à jour avec succès !');
    }

    // ─── MISE À JOUR PROFIL ADMIN ───────────────────────────────────────────
    public function updateAdmin(Request $request)
    {
        if ($redirect = $this->verifierConnexion()) return $redirect;

        $request->validate([
            'nom'    => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email'  => 'required|email|unique:utilisateur,email,' . session('user_id') . ',id_utilisateur',
        ]);

        $userId = session('user_id');

        Utilisateur::where('id_utilisateur', $userId)->update([
            'nom'    => $request->nom,
            'prenom' => $request->prenom,
            'email'  => $request->email,
        ]);

        session(['nom' => $request->nom, 'prenom' => $request->prenom, 'email' => $request->email]);

        return redirect('/administrateur/profil')->with('success', 'Profil mis à jour avec succès !');
    }
}
