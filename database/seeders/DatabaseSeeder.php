<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use App\Models\Role;
use App\Models\Etudiant;
use App\Models\Entreprise;
use App\Models\Tuteur;
use App\Models\Jury;
use App\Models\Administrateur;
use App\Models\OffreStage;


class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Etudiant
        $user = Utilisateur::firstOrCreate(
            ['email' => 'killianrollet@gmail.com'],
            ['nom' => 'Rollet', 'prenom' => 'Killian', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::updateOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            [
                'etudiant' => 1,
                'entreprise' => 1,
                'administrateur' => 1,
                'tuteur' => 1,
                'jury' => 1,
            ]
        );
    
        Etudiant::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['filiere' => 'GI', 'niveau' => 'ING1', 'cv' => '']
        );
        Entreprise::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['nom_entreprise' => 'GEYER & CO', 'adresse' => '9 Avenue Pierre Massé', 'secteur' => 'Informatique']
        );
        Administrateur::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur]
        );
        Tuteur::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur],
            ['specialite' => 'Programmation procédurale']
        );
        Jury::firstOrCreate(
            ['id_utilisateur' => $user->id_utilisateur]
            
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
            ['administrateur' => 1]
        );
        Administrateur::firstOrCreate(
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

        

        // Offres de stage de test
        $entreprise = Entreprise::where('id_utilisateur', 
            Utilisateur::where('email', 'iris.geyer@etu.cyu.fr')->first()->id_utilisateur
        )->first();

        OffreStage::firstOrCreate(
            ['titre' => 'Développeur web fullstack'],
            [
                'description'   => 'Stage de développement web au sein de notre équipe technique.',
                'missions'      => 'Développer de nouvelles fonctionnalités, corriger des bugs, participer aux réunions d\'équipe.',
                'competences'   => 'PHP, Laravel, PostgreSQL, HTML, CSS',
                'duree'         => '3 mois',
                'id_entreprise' => $entreprise->id_entreprise,
            ]
        );

        OffreStage::firstOrCreate(
            ['titre' => 'Analyste données'],
            [
                'description'   => 'Stage en analyse de données et business intelligence.',
                'missions'      => 'Analyser les données clients, créer des tableaux de bord, rédiger des rapports.',
                'competences'   => 'Python, SQL, Excel, PowerBI',
                'duree'         => '6 mois',
                'id_entreprise' => $entreprise->id_entreprise,
            ]
        );
    }
}