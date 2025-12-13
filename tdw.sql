-- =========================================================
-- FICHIER : tdw.sql
-- PROJET : Gestion d'un Laboratoire Informatique Universitaire
-- ANNEE : 2025/2026
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. CREATION DE LA BASE DE DONNEES (Page 5 : Nom obligatoire 'TDW')
DROP DATABASE IF EXISTS TDW;
CREATE DATABASE TDW CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE TDW;

-- =========================================================
-- 2. CREATION DES TABLES
-- =========================================================

-- TABLE: users
-- Contient les comptes administrateurs, enseignants, étudiants, etc.
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- Stocké en clair pour respecter la consigne "password=admin"
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    photo VARCHAR(255) DEFAULT 'default_avatar.png',
    grade VARCHAR(100), -- Ex: Professeur, MAA, MAB
    poste VARCHAR(100), -- Ex: Directeur, Chef d'équipe
    biographie TEXT,    -- Requis Page 2 (Présentation membre)
    specialite VARCHAR(100), -- Requis Page 3 (Filtres admin)
    domaine_recherche VARCHAR(255),
    role ENUM('admin','enseignant-chercheur','doctorant','etudiant','invite') DEFAULT 'enseignant-chercheur',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE: teams (Equipes de recherche)
CREATE TABLE teams (
    id_team INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    photo VARCHAR(255),
    chef_id INT,
    FOREIGN KEY (chef_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: team_members (Lien Utilisateurs <-> Equipes)
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    role_dans_equipe VARCHAR(100), -- Ex: Membre, Doctorant associé
    FOREIGN KEY (team_id) REFERENCES teams(id_team) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: partners (Partenaires institutionnels et industriels)
CREATE TABLE partners (
    id_partner INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    type ENUM('universite', 'entreprise', 'organisme') NOT NULL,
    logo VARCHAR(255),
    site_web VARCHAR(255)
) ENGINE=InnoDB;

-- TABLE: projects (Catalogue des projets)
CREATE TABLE projects (
    id_project INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    responsable_id INT, -- Chef de projet
    thematique VARCHAR(100), -- Ex: IA, Sécurité, Cloud
    type_financement VARCHAR(100), -- Requis Page 2
    statut ENUM('en_cours', 'termine', 'soumis') DEFAULT 'en_cours',
    date_debut DATE,
    date_fin DATE,
    lien_externe VARCHAR(255),
    FOREIGN KEY (responsable_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: project_members (Membres travaillant sur un projet)
CREATE TABLE project_members (
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role_projet VARCHAR(100), -- Ex: Développeur, Encadrant
    PRIMARY KEY (project_id, user_id),
    FOREIGN KEY (project_id) REFERENCES projects(id_project) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: project_partners (Partenaires associés à un projet)
CREATE TABLE project_partners (
    project_id INT NOT NULL,
    partner_id INT NOT NULL,
    PRIMARY KEY (project_id, partner_id),
    FOREIGN KEY (project_id) REFERENCES projects(id_project) ON DELETE CASCADE,
    FOREIGN KEY (partner_id) REFERENCES partners(id_partner) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: publications (Base documentaire)
CREATE TABLE publications (
    id_pub INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    resume TEXT,
    date_publication DATE,
    type VARCHAR(50), -- Ex: Article, Thèse, Poster
    lien_telechargement VARCHAR(255),
    doi VARCHAR(100),
    statut ENUM('valide', 'en_attente') DEFAULT 'en_attente', -- Requis Page 4 (Validation Admin)
    project_id INT,
    FOREIGN KEY (project_id) REFERENCES projects(id_project) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: publication_authors (Auteurs multiples pour une publication)
CREATE TABLE publication_authors (
    pub_id INT NOT NULL,
    user_id INT NOT NULL,
    PRIMARY KEY (pub_id, user_id),
    FOREIGN KEY (pub_id) REFERENCES publications(id_pub) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: equipments (Gestion des ressources matérielles)
CREATE TABLE equipments (
    id_equip INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    reference VARCHAR(50) UNIQUE,
    type VARCHAR(50), -- Ex: Serveur, Salle, Robot
    etat ENUM('libre', 'reserve', 'maintenance') DEFAULT 'libre',
    photo VARCHAR(255),
    date_achat DATE
) ENGINE=InnoDB;

-- TABLE: reservations (Système de réservation)
CREATE TABLE reservations (
    id_res INT AUTO_INCREMENT PRIMARY KEY,
    equip_id INT NOT NULL,
    user_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    status ENUM('confirme', 'en_attente', 'annule') DEFAULT 'en_attente',
    FOREIGN KEY (equip_id) REFERENCES equipments(id_equip) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: events (Evénements scientifiques)
CREATE TABLE events (
    id_event INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    date_event DATETIME NOT NULL,
    lieu VARCHAR(150),
    type ENUM('conference', 'atelier', 'soutenance', 'seminaire'),
    public_cible ENUM('interne', 'public') DEFAULT 'public'
) ENGINE=InnoDB;

-- TABLE: offers (Offres et opportunités)
CREATE TABLE offers (
    id_offer INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    type ENUM('stage', 'these', 'bourse', 'collaboration'),
    date_limite DATE,
    fichier_pdf VARCHAR(255)
) ENGINE=InnoDB;

-- TABLE: carousel_items (Gestion dynamique du diaporama Page 1)
CREATE TABLE carousel_items (
    id_slide INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100),
    description_courte VARCHAR(255),
    image_url VARCHAR(255) NOT NULL,
    lien_cible VARCHAR(255),
    ordre INT DEFAULT 0,
    active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- TABLE: settings (Paramètres généraux Page 4)
CREATE TABLE settings (
    id INT PRIMARY KEY,
    nom_laboratoire VARCHAR(200) DEFAULT 'Laboratoire Informatique ESI',
    logo_url VARCHAR(255) DEFAULT 'logo.png',
    theme_color VARCHAR(50) DEFAULT '#007bff',
    about_labo TEXT, -- Texte de présentation (Page 2)
    contact_email VARCHAR(150),
    contact_phone VARCHAR(50),
    directeur_labo_id INT, -- Pour l'organigramme (Page 2)
    FOREIGN KEY (directeur_labo_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- =========================================================
-- 3. INSERTION DES DONNEES (Jeux de données requis Page 5)
-- =========================================================

-- A. UTILISATEURS (Minimum 3 + Admin + User requis)
INSERT INTO users (username, password, nom, prenom, email, role, grade, specialite, domaine_recherche, biographie) VALUES 
('admin', 'admin', 'System', 'Administrator', 'admin@esi.dz', 'admin', 'N/A', 'Administration', 'Gestion', 'Compte administrateur système.'),
('user', 'user', 'Lambda', 'User', 'user@esi.dz', 'etudiant', 'Master 2', 'IL', 'Développement Web', 'Compte utilisateur standard pour démonstration.'),
('b.bensaber', 'password123', 'Ben Saber', 'Brahim', 'b_bensaber@esi.dz', 'enseignant-chercheur', 'Professeur', 'IA', 'Intelligence Artificielle et Vision', 'Professeur expert en IA avec 20 ans d\'expérience.'),
('s.amrouche', 'password123', 'Amrouche', 'Sarah', 's_amrouche@esi.dz', 'enseignant-chercheur', 'MCA', 'Réseaux', 'Sécurité des réseaux IoT', 'Maître de conférences spécialisée en cyber-sécurité.'),
('m.kaci', 'password123', 'Kaci', 'Mohamed', 'm_kaci@esi.dz', 'doctorant', 'Doctorant', 'Systèmes', 'Cloud Computing', 'Thèse sur l\'optimisation des conteneurs Docker.');

-- B. SETTINGS (Configuration initiale)
INSERT INTO settings (id, nom_laboratoire, about_labo, directeur_labo_id, contact_email) VALUES
(1, 'Laboratoire de Recherche ESI (LRE)', 'Le LRE est un pôle d\'excellence dédié à la recherche en informatique avancée, couvrant l\'IA, la sécurité et les systèmes distribués.', 3, 'contact@lre-esi.dz');

-- C. EQUIPES
INSERT INTO teams (nom, description, chef_id) VALUES
('Equipe IA & Data', 'Recherche sur le Machine Learning et le Big Data.', 3),
('Equipe CyberSec', 'Sécurité offensive et défensive des systèmes critiques.', 4),
('Equipe Cloud & Edge', 'Architectures distribuées et calcul haute performance.', 3);

-- D. PARTENAIRES
INSERT INTO partners (nom, type, site_web) VALUES
('Sonatrach', 'entreprise', 'https://sonatrach.com'),
('Ministère Enseignement Supérieur', 'organisme', 'https://mesrs.dz'),
('USTHB', 'universite', 'https://usthb.dz');

-- E. PROJETS
INSERT INTO projects (titre, description, responsable_id, thematique, type_financement, statut, date_debut) VALUES
('Smart City Algiers', 'Optimisation du trafic urbain via IA.', 3, 'IA', 'Etatique (PNR)', 'en_cours', '2024-01-01'),
('Secu-IoT', 'Protocole sécurisé pour objets connectés.', 4, 'Sécurité', 'Partenariat Industriel', 'en_cours', '2024-03-15'),
('Cloud-EDU', 'Plateforme éducative haute disponibilité.', 3, 'Cloud', 'Fonds Propre', 'soumis', '2025-01-01');

-- F. PUBLICATIONS
INSERT INTO publications (titre, date_publication, type, statut, project_id) VALUES
('Deep Learning for Traffic Control', '2024-06-10', 'Article', 'valide', 1),
('Vulnerabilities in ZigBee Protocols', '2024-09-20', 'Article', 'valide', 2),
('Container Orchestration at Scale', '2023-11-05', 'Thèse', 'valide', 3),
('Draft: New AI Model', '2025-01-10', 'Poster', 'en_attente', 1);

-- G. EQUIPEMENTS
INSERT INTO equipments (nom, type, etat, reference) VALUES
('Serveur GPU NVIDIA A100', 'Serveur', 'libre', 'SRV-AI-01'),
('Imprimante 3D Prusa', 'Imprimante', 'maintenance', 'PRT-3D-02'),
('Salle de Conférence A', 'Salle', 'reserve', 'ROOM-A');

-- H. EVENEMENTS
INSERT INTO events (titre, date_event, type, lieu) VALUES
('Conférence Nationale IA', '2025-05-20 09:00:00', 'conference', 'Auditorium ESI'),
('Workshop Cyber-Defense', '2025-06-10 14:00:00', 'atelier', 'Labo 4'),
('Soutenance de Thèse M. Kaci', '2025-07-01 10:00:00', 'soutenance', 'Salle B');

-- I. OFFRES
INSERT INTO offers (titre, type, date_limite) VALUES
('Stage PFE : Développement Fullstack', 'stage', '2025-12-31'),
('Bourse Doctorale : Vision par ordinateur', 'bourse', '2025-09-01'),
('Partenariat R&D avec Sonelgaz', 'collaboration', '2025-04-30');

-- J. CAROUSEL
INSERT INTO carousel_items (titre, image_url, description_courte, ordre) VALUES
('Bienvenue au LRE', 'slide1.jpg', 'Découvrez nos dernières recherches en IA', 1),
('Partenariat Sonatrach', 'slide2.jpg', 'Nouveau contrat de recherche signé', 2),
('Soutenances 2025', 'slide3.jpg', 'Planning des soutenances de fin d\'année', 3);

-- Liaison tables M-to-M (Exemples)
INSERT INTO team_members (team_id, user_id, role_dans_equipe) VALUES (1, 3, 'Chef'), (1, 5, 'Doctorant'), (2, 4, 'Chef');
INSERT INTO publication_authors (pub_id, user_id) VALUES (1, 3), (1, 5), (2, 4);
INSERT INTO project_members (project_id, user_id, role_projet) VALUES (1, 3, 'Responsable'), (1, 5, 'Développeur IA');

COMMIT;