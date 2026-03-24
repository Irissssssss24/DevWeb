CREATE database IF NOT EXISTS projetStage;

DROP TABLE Utilisateur;
DROP TABLE Etudiant;
DROP TABLE Entreprise;
DROP TABLE Tuteur;
DROP TABLE Jury;
DROP TABLE Offre_stage;
DROP TABLE Stage;
DROP TABLE Document;
DROP TABLE Remarque;
DROP TABLE Suivi;
DROP TABLE Authentification;

use projetStage;

-- TABLE UTILISATEUR
CREATE TABLE Utilisateur (
    id_utilisateur SERIAL PRIMARY KEY,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    mot_de_passe TEXT,
    role VARCHAR(50)
);

-- TABLE ETUDIANT
CREATE TABLE Etudiant (
    id_etudiant SERIAL PRIMARY KEY,
    id_utilisateur INT REFERENCES utilisateur(id_utilisateur),
    filiere VARCHAR(100),
    niveau VARCHAR(50),
    cv TEXT
);

-- TABLE ENTREPRISE
CREATE TABLE Entreprise (
    id_entreprise SERIAL PRIMARY KEY,
    id_utilisateur INT REFERENCES utilisateur(id_utilisateur),
    nom_entreprise VARCHAR(150),
    adresse TEXT,
    secteur VARCHAR(100)
);

-- TABLE TUTEUR
CREATE TABLE Tuteur (
    id_tuteur SERIAL PRIMARY KEY,
    id_utilisateur INT REFERENCES utilisateur(id_utilisateur),
    specialite VARCHAR(100)
);

-- TABLE JURY
CREATE TABLE Jury (
    id_jury SERIAL PRIMARY KEY,
    id_utilisateur INT REFERENCES utilisateur(id_utilisateur)
);

-- TABLE OFFRE DE STAGE
CREATE TABLE Offre_stage (
    id_offre SERIAL PRIMARY KEY,
    titre VARCHAR(150),
    description TEXT,
    competences TEXT,
    duree VARCHAR(50),
    missions TEXT,
    id_entreprise INT REFERENCES entreprise(id_entreprise)
);

-- TABLE STAGE
CREATE TABLE Stage (
    id_stage SERIAL PRIMARY KEY,
    id_etudiant INT REFERENCES etudiant(id_etudiant),
    id_offre INT REFERENCES offre_stage(id_offre),
    id_tuteur INT REFERENCES tuteur(id_tuteur),
    statut VARCHAR(50),
    date_debut DATE,
    date_fin DATE
);

-- TABLE DOCUMENT
CREATE TABLE Document (
    id_document SERIAL PRIMARY KEY,
    type VARCHAR(50),
    fichier TEXT,
    id_stage INT REFERENCES stage(id_stage)
);

-- TABLE REMARQUE
CREATE TABLE Remarque (
    id_remarque SERIAL PRIMARY KEY,
    contenu TEXT,
    date TIMESTAMP,
    id_stage INT REFERENCES stage(id_stage),
    id_utilisateur INT REFERENCES utilisateur(id_utilisateur)
);

-- TABLE SUIVI
CREATE TABLE Suivi (
    id_suivi SERIAL PRIMARY KEY,
    avancement TEXT,
    date TIMESTAMP,
    id_stage INT REFERENCES stage(id_stage)
);

-- TABLE AUTHENTIFICATION (2FA)
CREATE TABLE Authentification (
    id_auth SERIAL PRIMARY KEY,
    id_utilisateur INT REFERENCES utilisateur(id_utilisateur),
    code_2fa VARCHAR(10),
    date_expiration TIMESTAMP
);