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
        // SUPERUSER (tous les rôles)
        // ============================================================
        $superuser = Utilisateur::firstOrCreate(
            ['email' => 'superuser@gmail.com'],
            ['nom' => 'Super', 'prenom' => 'User', 'mot_de_passe' => bcrypt('superuser')]
        );
        Role::updateOrCreate(
            ['id_utilisateur' => $superuser->id_utilisateur],
            ['etudiant' => 1, 'entreprise' => 1, 'administrateur' => 1, 'tuteur' => 1, 'jury' => 1]
        );
        Etudiant::firstOrCreate(
            ['id_utilisateur' => $superuser->id_utilisateur],
            ['filiere' => 'GI', 'niveau' => 'ING1', 'cv' => '']
        );
        Entreprise::firstOrCreate(
            ['id_utilisateur' => $superuser->id_utilisateur],
            ['nom_entreprise' => 'SuperCorp', 'adresse' => '1 Rue du Superuser', 'secteur' => 'Informatique']
        );
        Administrateur::firstOrCreate(['id_utilisateur' => $superuser->id_utilisateur]);
        Tuteur::firstOrCreate(
            ['id_utilisateur' => $superuser->id_utilisateur],
            ['specialite' => 'Toutes spécialités']
        );
        Jury::firstOrCreate(['id_utilisateur' => $superuser->id_utilisateur]);

        // ============================================================
        // UTILISATEUR ENTREPRISE
        // ============================================================
        $userEntreprise = Utilisateur::firstOrCreate(
            ['email' => 'entreprise@test.com'],
            ['nom' => 'Dupont', 'prenom' => 'Marie', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userEntreprise->id_utilisateur],
            ['entreprise' => 1]
        );
        Entreprise::firstOrCreate(
            ['id_utilisateur' => $userEntreprise->id_utilisateur],
            ['nom_entreprise' => 'TechCorp', 'adresse' => '10 Rue de la Paix, Paris', 'secteur' => 'Informatique', 'siret' => '00000000000000']
        );

        // ============================================================
        // UTILISATEUR ADMINISTRATEUR
        // ============================================================
        $userAdmin = Utilisateur::firstOrCreate(
            ['email' => 'admin@test.com'],
            ['nom' => 'Martin', 'prenom' => 'Jean', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userAdmin->id_utilisateur],
            ['administrateur' => 1]
        );
        Administrateur::firstOrCreate(['id_utilisateur' => $userAdmin->id_utilisateur]);

        // ============================================================
        // UTILISATEUR TUTEUR
        // ============================================================
        $userTuteur = Utilisateur::firstOrCreate(
            ['email' => 'tuteur@test.com'],
            ['nom' => 'Bernard', 'prenom' => 'Sophie', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userTuteur->id_utilisateur],
            ['tuteur' => 1]
        );
        Tuteur::firstOrCreate(
            ['id_utilisateur' => $userTuteur->id_utilisateur],
            ['specialite' => 'Développement web']
        );

        // ============================================================
        // UTILISATEUR ETUDIANT
        // ============================================================
        $userEtudiant = Utilisateur::firstOrCreate(
            ['email' => 'etudiant@test.com'],
            ['nom' => 'Durand', 'prenom' => 'Lucas', 'mot_de_passe' => bcrypt('mdp_test')]
        );
        Role::firstOrCreate(
            ['id_utilisateur' => $userEtudiant->id_utilisateur],
            ['etudiant' => 1]
        );
        Etudiant::firstOrCreate(
            ['id_utilisateur' => $userEtudiant->id_utilisateur],
            ['filiere' => 'Génie Informatique', 'niveau' => 'ING2', 'cv' => '']
        );

        // ============================================================
        // OFFRES DE STAGE
        // ============================================================
        $entreprise = Entreprise::where('id_utilisateur', $userEntreprise->id_utilisateur)->first();

        $offreDev = OffreStage::firstOrCreate(
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

        // ============================================================
        // STAGE DE TEST
        // ============================================================
        $etudiant = Etudiant::where('id_utilisateur', $userEtudiant->id_utilisateur)->first();
        $tuteur   = Tuteur::where('id_utilisateur', $userTuteur->id_utilisateur)->first();

        $stage = Stage::firstOrCreate(
            [
                'id_etudiant' => $etudiant->id_etudiant,
                'id_offre'    => $offreDev->id_offre,
            ],
            [
                'id_tuteur'  => $tuteur->id_tuteur,
                'statut'     => 'en_cours',
                'date_debut' => '2025-02-01 08:00:00',
                'date_fin'   => '2025-04-30 18:00:00',
            ]
        );

        // Documents
        Document::firstOrCreate(
            ['type' => 'rapport', 'id_stage' => $stage->id_stage],
            ['fichier' => 'rapports/rapport_stage_test.pdf']
        );
        Document::firstOrCreate(
            ['type' => 'convention', 'id_stage' => $stage->id_stage],
            ['fichier' => 'conventions/convention_test.pdf']
        );

        // Suivi
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

        // Remarques
        Remarque::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'id_utilisateur' => $userTuteur->id_utilisateur],
            [
                'contenu' => 'Bon investissement dans le projet. L\'étudiant a fait preuve d\'autonomie et de rigueur.',
                'date'    => '2025-04-15 14:00:00',
            ]
        );
        Remarque::firstOrCreate(
            ['id_stage' => $stage->id_stage, 'id_utilisateur' => $userAdmin->id_utilisateur],
            [
                'contenu' => 'Rapport de stage bien structuré. La soutenance a été claire et bien préparée. Note finale : 16/20.',
                'date'    => '2025-04-28 10:00:00',
            ]
        );
    }
}