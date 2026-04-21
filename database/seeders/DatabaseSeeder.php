<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Tuteur;
use App\Models\Jury;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Etudiant
        $user = Utilisateur::firstOrCreate(
            ['email' => 'killianrollet@gmail.com'],
            ['nom' => 'Rollet', 'prenom' => 'Killian', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['etudiant' => 1]
        );
        Etudiant::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['filiere' => 'GI', 'niveau' => 'ING1', 'cv' => '']
        );

        // Entreprise
        $user = Utilisateur::firstOrCreate(
            ['email' => 'iris.geyer@etu.cyu.fr'],
            ['nom' => 'Geyer', 'prenom' => 'Iris', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['entreprise' => 1]
        );
        Entreprise::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['nom_entreprise' => 'GEYER & CO', 'adresse' => '9 Avenue Pierre Massé', 'secteur' => 'Informatique']
        );

        // Jury
        $user = Utilisateur::firstOrCreate(
            ['email' => 'killian.rollet@etu.cyu.fr'],
            ['nom' => 'Rollet', 'prenom' => 'Killian', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['jury' => 1]
        );
        Jury::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur]
        );

        // Tuteur
        $user = Utilisateur::firstOrCreate(
            ['email' => 'sylia.meddour@etu.cyu.fr'],
            ['nom' => 'Meddour', 'prenom' => 'Sylia', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['tuteur' => 1]
        );
        Tuteur::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['specialite' => 'Programmation procédurale']
        );
    }
}