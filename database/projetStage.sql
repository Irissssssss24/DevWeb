-- Création de la base (à exécuter séparément si besoin)
-- CREATE DATABASE projetstage;

-- Donne le droit de se connecter à la base
--ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT SELECT, INSERT, UPDATE, DELETE ON TABLES TO iris;

-- =========================
-- TABLE UTILISATEUR
-- =========================
CREATE TABLE utilisateur (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    mot_de_passe TEXT,
    role VARCHAR(50)
);

-- =========================
-- TABLE ETUDIANT
-- =========================
CREATE TABLE etudiant (
    id_etudiant SERIAL PRIMARY KEY,
    id_utilisateur INTEGER,
    filiere VARCHAR(100),
    niveau VARCHAR(50),
    cv TEXT, -- chemin vers le CV de l'étudiant
    CONSTRAINT fk_etudiant_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

-- =========================
-- TABLE ENTREPRISE
-- =========================
CREATE TABLE entreprise (
    id_entreprise SERIAL PRIMARY KEY,
    id_utilisateur INTEGER,
    nom_entreprise VARCHAR(150),
    adresse TEXT,
    secteur VARCHAR(100),
    CONSTRAINT fk_entreprise_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

-- =========================
-- TABLE TUTEUR
-- =========================
CREATE TABLE tuteur (
    id_tuteur SERIAL PRIMARY KEY,
    id_utilisateur INTEGER,
    specialite VARCHAR(100),
    CONSTRAINT fk_tuteur_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

-- =========================
-- TABLE JURY
-- =========================
CREATE TABLE jury (
    id_jury SERIAL PRIMARY KEY,
    id_utilisateur INTEGER,
    CONSTRAINT fk_jury_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

-- =========================
-- TABLE OFFRE DE STAGE
-- =========================
CREATE TABLE offre_stage (
    id_offre SERIAL PRIMARY KEY,
    titre VARCHAR(150),
    description TEXT,
    competences TEXT,
    duree VARCHAR(50),
    missions TEXT,
    id_entreprise INTEGER,
    CONSTRAINT fk_offre_entreprise FOREIGN KEY (id_entreprise)
        REFERENCES entreprise(id_entreprise)
        ON DELETE CASCADE
);

-- =========================
-- TABLE STAGE
-- =========================
CREATE TABLE stage (
    id_stage SERIAL PRIMARY KEY,
    id_etudiant INTEGER,
    id_offre INTEGER,
    id_tuteur INTEGER,
    statut VARCHAR(50),
    date_debut TIMESTAMP,
    date_fin TIMESTAMP,
    CONSTRAINT fk_stage_etudiant FOREIGN KEY (id_etudiant)
        REFERENCES etudiant(id_etudiant)
        ON DELETE CASCADE,
    --lien entre stage et offre de stage
    CONSTRAINT fk_stage_offre FOREIGN KEY (id_offre)
        REFERENCES offre_stage(id_offre)
        ON DELETE CASCADE,
    CONSTRAINT fk_stage_tuteur FOREIGN KEY (id_tuteur)
        REFERENCES tuteur(id_tuteur)
        ON DELETE SET NULL
);

-- =========================
-- TABLE DOCUMENT
-- =========================
-- peut être un rapport, un cahier de stage,...
CREATE TABLE document (
    id_document SERIAL PRIMARY KEY,
    type VARCHAR(50),
    fichier TEXT,
    id_stage INTEGER,
    CONSTRAINT fk_document_stage FOREIGN KEY (id_stage)
        REFERENCES stage(id_stage)
        ON DELETE CASCADE
);

-- =========================
-- TABLE REMARQUE
-- =========================
CREATE TABLE remarque (
    id_remarque SERIAL PRIMARY KEY,
    contenu TEXT,
    date TIMESTAMP,
    id_stage INTEGER,
    id_utilisateur INTEGER,
    CONSTRAINT fk_remarque_stage FOREIGN KEY (id_stage)
        REFERENCES stage(id_stage)
        ON DELETE CASCADE,
    -- peu importe le rôle il peut laisser une remarque
    CONSTRAINT fk_remarque_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

-- =========================
-- TABLE SUIVI
-- =========================
CREATE TABLE suivi (
    id_suivi SERIAL PRIMARY KEY,
    avancement TEXT,
    date TIMESTAMP,
    id_stage INTEGER,
    CONSTRAINT fk_suivi_stage FOREIGN KEY (id_stage)
        REFERENCES stage(id_stage)
        ON DELETE CASCADE
);

-- =========================
-- TABLE AUTHENTIFICATION
-- =========================
-- pour la 2FA, on stocke le code et sa date d'expiration
-- sert lors de la création d'un compte UNIQUEMENT
CREATE TABLE authentification (
    id_auth SERIAL PRIMARY KEY,
    id_utilisateur INTEGER,
    code_2fa VARCHAR(10),
    date_expiration TIMESTAMP,
    CONSTRAINT fk_auth_utilisateur FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);

-- =========================
-- INSERTS DE TEST
-- =========================

-- les mots de passe sont mdp_test
-- Utilisateur
INSERT INTO utilisateur (nom, prenom, email, mot_de_passe, role) VALUES
('Rollet', 'Killian', 'killianrollet@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('GEYER', 'Iris', 'iris.geyer@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'entreprise'),
('Rollet', 'Killian', 'killian.rollet@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'jury'),
('Meddour', 'Sylia', 'sylia.meddour@etu.cyu.fr', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tuteur');

-- Etudiant
INSERT INTO etudiant (id_utilisateur, filiere, niveau, cv) VALUES
(1, 'GI', 'ING1', './document/utilisateur/1/ROLLET_Killian_Keio.pdf');

-- Entreprise
INSERT INTO entreprise (id_utilisateur, nom_entreprise, adresse, secteur) VALUES
(2, 'GEYER & CO', '9 Avenue Pierre Massé', 'Informatique');

-- Tuteur
INSERT INTO tuteur (id_utilisateur, specialite) VALUES
(4, 'Programmation procédurale');

-- Jury
INSERT INTO jury (id_utilisateur) VALUES
(3);

-- Offre de stage
INSERT INTO offre_stage (titre, description, competences, duree, missions, id_entreprise) VALUES
('TOTAL', 'stage chez total', 'HTML5,CSS,PHP,JS', '1 mois', 'faire un site web', 1);

-- Stage
INSERT INTO stage (id_etudiant, id_offre, id_tuteur, statut, date_debut, date_fin) VALUES
(1, 1, 1, 'En cours', '2026-06-26 08:00:00', '2026-07-27 17:00:00');

-- Document
INSERT INTO document (type, fichier, id_stage) VALUES
('rapport', './document/stage/01/rapport.txt', 1);

-- Remarque
INSERT INTO remarque (contenu, date, id_stage, id_utilisateur) VALUES
('travaille bien', '2026-06-30 16:45:00', 1, 2);

-- Suivi
INSERT INTO suivi (avancement, date, id_stage) VALUES
('à fini la page de connexion', '2026-06-28 08:15:15', 1);