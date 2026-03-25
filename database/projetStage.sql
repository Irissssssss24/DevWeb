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
    id_utilisateur PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100),
    prenom VARCHAR(100),
    email VARCHAR(150) UNIQUE,
    mot_de_passe TEXT,
    role VARCHAR(50)
);

-- TABLE ETUDIANT
CREATE TABLE Etudiant (
    id_etudiant INTEGER(20) primary key AUTO_INCREMENT,
    id_utilisateur INTEGER(20),
    filiere VARCHAR(100),
    niveau VARCHAR(50),
    cv TEXT,
    FOREIGN KEY fk_util(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)

);

-- TABLE ENTREPRISE
CREATE TABLE Entreprise (
    id_entreprise INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INTEGER(20),
    nom_entreprise VARCHAR(150),
    adresse TEXT,
    secteur VARCHAR(100),
    FOREIGN KEY fk_util(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- TABLE TUTEUR
CREATE TABLE Tuteur (
    id_tuteur INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INTEGER(20),
    specialite VARCHAR(100),
    FOREIGN KEY fk_util(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)

);

-- TABLE JURY
CREATE TABLE Jury (
    id_jury INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INTEGER(20),
    FOREIGN KEY fk_util(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- TABLE OFFRE DE STAGE
CREATE TABLE Offre_stage (
    id_offre INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    titre VARCHAR(150),
    description TEXT,
    competences TEXT,
    duree VARCHAR(50),
    missions TEXT,
    id_entreprise INTEGER(20,)
    FOREIGN KEY fk_entr(id_entreprise) REFERENCES Entreprise(id_entreprise)
);

-- TABLE STAGE
CREATE TABLE Stage (
    id_stage INTGER(20) PRIMARY KEY AUTO_INCREMENT,
    id_etudiant INTEGER(20),
    id_offre INTEGER(20),
    id_tuteur INTEGER(20),
    statut VARCHAR(50),
    date_debut DATE,
    date_fin DATE,
    FOREIGN KEY fk_etu(id_etudiant) REFERENCES Etudiant(id_etudiant),
    FOREIGN KEY fk_ofr(id_offre) REFERENCES Offre_stage(id_offre)
);

-- TABLE DOCUMENT
CREATE TABLE Document (
    id_document INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    type VARCHAR(50),
    fichier TEXT,
    id_stage INTEGER(20),
    FOREIGN KEY fk_st(id_stage) REFERENCES Stage(id_stage)
);

-- TABLE REMARQUE
CREATE TABLE Remarque (
    id_remarque INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    contenu TEXT,
    date TIMESTAMP,
    id_stage INTEGER(20),
    id_utilisateur INTEGER(20),
    FOREIGN KEY fk_st(id_stage) REFERENCES Stage(id_stage),
    FOREIGN KEY fk_util(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);

-- TABLE SUIVI
CREATE TABLE Suivi (
    id_suivi INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    avancement TEXT,
    date TIMESTAMP,
    id_stage INTEGER(20),
    FOREIGN KEY fk_st(id_stage) REFERENCES Stage(id_stage)

);

-- TABLE AUTHENTIFICATION (2FA)
CREATE TABLE Authentification (
    id_auth INTEGER(20) PRIMARY KEY AUTO_INCREMENT,
    id_utilisateur INTEGER(20),
    code_2fa VARCHAR(10),
    date_expiration TIMESTAMP,
    FOREIGN KEY fk_util(id_utilisateur) REFERENCES Utilisateur(id_utilisateur)
);


-- Ajout test

-- Ajout Utilisateur
INSERT INTO Utilisateur VALUES(null,"Rollet", "Killian", "killianrollet@gmail.com", "mdp_test", "etudiant");
INSERT INTO Utilisateur VALUES(null,"GEYER", "Iris", "iris.geyer@etu.cyu.fr", "mdp_test", "entreprise");
INSERT INTO Utilisateur VALUES(null,"Rollet", "Killian", "killian.rollet@etu.cyu.fr", "mdp_test", "jury");
INSERT INTO Utilisateur VALUES(null,"Meddour", "Sylia", "sylia.meddour@etu.cyu.fr", "mdp_test", "tuteur");

--Ajout Etudiant
INSERT INTO Etudiant VALUES(null,1,"GI","ING1","./document/utilisateur/1/ROLLET_Killian_Keio.pdf");

--Ajout ENTREPRISE
INSERT INTO Entreprise VALUES(null,2,"GEYER & CO","9 Avenue Pierre Massé", "Informatique");

--Ajout Tuteur
INSERT INTO Tuteur Values(null,4,"Programation procédurale");

--Ajout Jury
INSERT INTO Jury VALUES(null,3);

--Ajout OFFRE DE STAGE
INSERT INTO Offre_stage VALUES (null,"TOTAL","stage chez total","HTML5,CSS,PHP,JS","1 mois","faire un site web",1);

--Ajout Stage
INSERT INTO Stage VALUES(null,1,1,1,"En cours",FROM_UNIXTIME(UNIX_TIMESTAMP('2026-06-26 8:00:00')),FROM_UNIXTIME(UNIX_TIMESTAMP('2026-07-27 17:00:00')));

--Ajout Document
INSERT INTO Document VALUES(null,"rapport","./document/stage/01/rapport.txt",1);

--Ajout Remarque
INSERT INTO Remarque VALUES(null,"travaille bien",FROM_UNIXTIME(UNIX_TIMESTAMP('2026-06-30 16:45:00')),1,2);

--Ajout SUIVI
INSERT INTO Suivi VALUES(null,"à fini la page de connexion",FROM_UNIXTIME(UNIX_TIMESTAMP('2026-06-28 8:15:15')),1);

