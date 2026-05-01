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
use App\Models\Stage;
use App\Models\Document;
use App\Models\Suivi;
use App\Models\Remarque;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // UTILISATEURS EXISTANTS 
        // ============================================================

        $userKillian = Utilisateur::firstOrCreate(
            ['email' => 'killianrollet@gmail.com'],
            ['nom' => 'Rollet', 'prenom' => 'Killian', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::updateOrCreate(
            ['id_utilisateur' => $userKillian->id_utilisateur],
            ['etudiant' => 1, 'entreprise' => 1, 'administrateur' => 1, 'tuteur' => 1, 'jury' => 1]
        );
        Etudiant::firstOrCreate(
            ['id_utilisateur' => $userKillian->id_utilisateur],
            ['filiere' => 'GI', 'niveau' => 'ING1', 'cv' => '']
        );
        Entreprise::firstOrCreate(
            ['id_utilisateur' => $userKillian->id_utilisateur],
            ['nom_entreprise' => 'ROLLET & CO', 'adresse' => '9 Avenue Pierre Massé', 'secteur' => 'Informatique']
        );
        Administrateur::firstOrCreate(['id_utilisateur' => $userKillian->id_utilisateur]);
        Tuteur::firstOrCreate(
            ['id_utilisateur' => $userKillian->id_utilisateur],
            ['specialite' => 'Programmation procédurale']
        );
        Jury::firstOrCreate(['id_utilisateur' => $userKillian->id_utilisateur]);

        $userIris = Utilisateur::firstOrCreate(
            ['email' => 'iris.geyer@etu.cyu.fr'],
            ['nom' => 'Geyer', 'prenom' => 'Iris', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userIris->id_utilisateur],
            ['entreprise' => 1]
        );
        Entreprise::firstOrCreate(
            ['id_utilisateur' => $userIris->id_utilisateur],
            ['nom_entreprise' => 'GEYER & CO', 'adresse' => '9 Avenue Pierre Massé', 'secteur' => 'Informatique', 'siret' => '123 123 123 12345']
        );

        $userKillian2 = Utilisateur::firstOrCreate(
            ['email' => 'killian.rollet@etu.cyu.fr'],
            ['nom' => 'Rollet', 'prenom' => 'Killian', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userKillian2->id_utilisateur],
            ['administrateur' => 1]
        );
        Administrateur::firstOrCreate(['id_utilisateur' => $userKillian2->id_utilisateur]);

        $userSylia = Utilisateur::firstOrCreate(
            ['email' => 'sylia.meddour@etu.cyu.fr'],
            ['nom' => 'Meddour', 'prenom' => 'Sylia', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userSylia->id_utilisateur],
            ['tuteur' => 1]
        );
        Tuteur::firstOrCreate(
            ['id_utilisateur' => $userSylia->id_utilisateur],
            ['specialite' => 'Programmation procédurale']
        );

        // Offres existantes
        $entrepriseIris = Entreprise::where('id_utilisateur', $userIris->id_utilisateur)->first();

        $offreDev = OffreStage::firstOrCreate(
            ['titre' => 'Développeur web fullstack'],
            [
                'description'   => 'Stage de développement web au sein de notre équipe technique.',
                'missions'      => 'Développer de nouvelles fonctionnalités, corriger des bugs, participer aux réunions d\'équipe.',
                'competences'   => 'PHP, Laravel, PostgreSQL, HTML, CSS',
                'duree'         => '3 mois',
                'id_entreprise' => $entrepriseIris->id_entreprise,
            ]
        );

        OffreStage::firstOrCreate(
            ['titre' => 'Analyste données'],
            [
                'description'   => 'Stage en analyse de données et business intelligence.',
                'missions'      => 'Analyser les données clients, créer des tableaux de bord, rédiger des rapports.',
                'competences'   => 'Python, SQL, Excel, PowerBI',
                'duree'         => '6 mois',
                'id_entreprise' => $entrepriseIris->id_entreprise,
            ]
        );

        // ============================================================
        // STAGE DE TEST
        // ============================================================

        $etudiant = Etudiant::where('id_utilisateur', $userKillian->id_utilisateur)->first();
        $tuteur   = Tuteur::where('id_utilisateur', $userSylia->id_utilisateur)->first();

        $stage = Stage::firstOrCreate(
            [
                'id_etudiant' => $etudiant->id_etudiant,
                'id_offre'    => $offreDev->id_offre,
            ],
            [
                'id_tuteur'   => $tuteur->id_tuteur,
                'statut'      => 'en_cours',
                'date_debut'  => '2025-02-01 08:00:00',
                'date_fin'    => '2025-04-30 18:00:00',
            ]
        );

        // ---- Documents ----
        // Rapport de stage
        Document::firstOrCreate(
            ['type' => 'rapport', 'id_stage' => $stage->id_stage],
            ['fichier' => 'rapports/rapport_stage_killian_rollet.pdf']
        );

        // Convention de stage
        Document::firstOrCreate(
            ['type' => 'convention', 'id_stage' => $stage->id_stage],
            ['fichier' => 'conventions/convention_killian_rollet.pdf']
        );

        // ---- Suivi (journal d'avancement) ----
        Suivi::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'date' => '2025-02-07 10:00:00'],
            ['avancement' => 'Prise en main de l\'environnement de développement, installation des outils, lecture de la documentation interne.']
        );

        Suivi::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'date' => '2025-02-28 10:00:00'],
            ['avancement' => 'Développement du module d\'authentification. Mise en place du système 2FA par email. Tests unitaires réalisés.']
        );

        Suivi::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'date' => '2025-03-31 10:00:00'],
            ['avancement' => 'Intégration de l\'interface étudiant et entreprise. Correction de bugs sur le formulaire d\'inscription.']
        );

        // ---- Remarques (tuteur + jury) ----
        Remarque::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'id_utilisateur' => $userSylia->id_utilisateur],
            [
                'contenu' => 'Bon investissement dans le projet. L\'étudiant a fait preuve d\'autonomie et de rigueur. Quelques efforts à faire sur la documentation du code.',
                'date'    => '2025-04-15 14:00:00',
            ]
        );

        Remarque::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'id_utilisateur' => $userKillian2->id_utilisateur],
            [
                'contenu' => 'Rapport de stage bien structuré. La soutenance a été claire et bien préparée. Note finale : 16/20.',
                'date'    => '2025-04-28 10:00:00',
            ]
        );
    }
}
