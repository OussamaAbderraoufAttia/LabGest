-- =========================================================
-- FICHIER : tdw_enriched.sql
-- PROJET : Gestion d'un Laboratoire Informatique Universitaire
-- VERSION : Enrichie avec données réalistes ESI + Notifications/Reservations/Events
-- ANNEE : 2025/2026
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- 1. CREATION DE LA BASE DE DONNEES
DROP DATABASE IF EXISTS TDW;
CREATE DATABASE TDW CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE TDW;

-- =========================================================
-- 2. CREATION DES TABLES
-- =========================================================

-- TABLE: users
CREATE TABLE users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    photo VARCHAR(255) DEFAULT 'View/assets/default_avatar.png',
    grade VARCHAR(100),
    poste VARCHAR(100),
    biographie TEXT,
    specialite VARCHAR(100),
    domaine_recherche VARCHAR(255),
    role ENUM('admin','enseignant-chercheur','doctorant','etudiant','invite') DEFAULT 'enseignant-chercheur',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- TABLE: teams
CREATE TABLE teams (
    id_team INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL UNIQUE,
    description TEXT,
    photo VARCHAR(255),
    chef_id INT,
    FOREIGN KEY (chef_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: team_members
CREATE TABLE team_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL,
    user_id INT NOT NULL,
    role_dans_equipe VARCHAR(100),
    FOREIGN KEY (team_id) REFERENCES teams(id_team) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: partners
CREATE TABLE partners (
    id_partner INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    type ENUM('universite', 'entreprise', 'organisme') NOT NULL,
    logo VARCHAR(255),
    site_web VARCHAR(255)
) ENGINE=InnoDB;

-- TABLE: projects
CREATE TABLE projects (
    id_project INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    responsable_id INT,
    thematique VARCHAR(100),
    type_financement VARCHAR(100),
    statut ENUM('en_cours', 'termine', 'soumis') DEFAULT 'en_cours',
    date_debut DATE,
    date_fin DATE,
    lien_externe VARCHAR(255),
    FOREIGN KEY (responsable_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: project_members
CREATE TABLE project_members (
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    role_projet VARCHAR(100),
    PRIMARY KEY (project_id, user_id),
    FOREIGN KEY (project_id) REFERENCES projects(id_project) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: project_partners
CREATE TABLE project_partners (
    project_id INT NOT NULL,
    partner_id INT NOT NULL,
    PRIMARY KEY (project_id, partner_id),
    FOREIGN KEY (project_id) REFERENCES projects(id_project) ON DELETE CASCADE,
    FOREIGN KEY (partner_id) REFERENCES partners(id_partner) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: publications
CREATE TABLE publications (
    id_pub INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(300) NOT NULL,
    resume TEXT,
    type ENUM('article', 'conference', 'these', 'memoire', 'rapport', 'cours', 'livre') NOT NULL,
    date_publication DATE NOT NULL,
    conference VARCHAR(200),
    doi VARCHAR(100),
    fichier_pdf VARCHAR(255),
    project_id INT,
    team_id INT,
    statut ENUM('valide', 'en_attente', 'refuse', 'rejete') DEFAULT 'valide',
    FOREIGN KEY (project_id) REFERENCES projects(id_project) ON DELETE SET NULL,
    FOREIGN KEY (team_id) REFERENCES teams(id_team) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: publication_authors
CREATE TABLE publication_authors (
    pub_id INT NOT NULL,
    user_id INT NOT NULL,
    ordre_auteur INT DEFAULT 1,
    PRIMARY KEY (pub_id, user_id),
    FOREIGN KEY (pub_id) REFERENCES publications(id_pub) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: equipments
CREATE TABLE equipments (
    id_equip INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    type VARCHAR(100),
    description TEXT,
    localisation VARCHAR(150),
    image_url VARCHAR(255) DEFAULT 'View/assets/img/default_equip.jpg',
    etat ENUM('disponible', 'en_maintenance', 'hors_service', 'reserve') DEFAULT 'disponible'
) ENGINE=InnoDB;

-- TABLE: reservations
CREATE TABLE reservations (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    equip_id INT NOT NULL,
    user_id INT NOT NULL,
    date_debut DATETIME NOT NULL,
    date_fin DATETIME NOT NULL,
    status ENUM('confirme', 'annule', 'en_attente', 'refuse', 'termine') DEFAULT 'en_attente',
    FOREIGN KEY (equip_id) REFERENCES equipments(id_equip) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: events
CREATE TABLE events (
    id_event INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    date_event DATETIME NOT NULL,
    lieu VARCHAR(150),
    image_url VARCHAR(255) DEFAULT 'View/assets/img/default_event.jpg',
    type ENUM('conference', 'atelier', 'soutenance', 'seminaire', 'reunion', 'hackathon'),
    public_cible ENUM('interne', 'public') DEFAULT 'public'
) ENGINE=InnoDB;

-- TABLE: event_registrations
CREATE TABLE event_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT NOT NULL,
    user_id INT NULL,
    guest_email VARCHAR(150) NULL,
    guest_name VARCHAR(100) NULL,
    motivation TEXT NULL,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
    registration_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id_event) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: notifications
CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    type ENUM('event', 'team', 'article', 'reservation', 'publication', 'info') DEFAULT 'info',
    link VARCHAR(255),
    is_read BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id_user) ON DELETE CASCADE
) ENGINE=InnoDB;

-- TABLE: offers
CREATE TABLE offers (
    id_offer INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT,
    type ENUM('stage', 'these', 'bourse', 'collaboration'),
    date_limite DATE,
    fichier_pdf VARCHAR(255)
) ENGINE=InnoDB;

-- TABLE: carousel_items
CREATE TABLE carousel_items (
    id_slide INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100),
    description_courte VARCHAR(255),
    image_url VARCHAR(255) NOT NULL,
    lien_cible VARCHAR(255),
    ordre INT DEFAULT 0,
    active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- TABLE: actualites
CREATE TABLE actualites (
    id_actualite INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    contenu_complet TEXT,
    image_url VARCHAR(255),
    date_publication DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME ON UPDATE CURRENT_TIMESTAMP,
    categorie VARCHAR(100),
    auteur_id INT,
    statut ENUM('publiee', 'brouillon', 'archivee') DEFAULT 'publiee',
    FOREIGN KEY (auteur_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- TABLE: settings
CREATE TABLE settings (
    id INT PRIMARY KEY,
    nom_laboratoire VARCHAR(200) DEFAULT 'Laboratoire Informatique ESI',
    logo_url VARCHAR(255) DEFAULT 'logo.png',
    theme_color VARCHAR(50) DEFAULT '#007bff',
    about_labo TEXT,
    contact_email VARCHAR(150),
    contact_phone VARCHAR(50),
    directeur_labo_id INT,
    FOREIGN KEY (directeur_labo_id) REFERENCES users(id_user) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ==================================================================
-- 3. INSERTION DES DONNEES REALISTES (ESI-Inspired)
-- ==================================================================

-- A. UTILISATEURS (15+ membres variés)
INSERT INTO users (username, password, nom, prenom, email, photo, role, grade, specialite, domaine_recherche, biographie, poste) VALUES 
-- Admin et direction
('admin', 'admin', 'System', 'Administrator', 'admin@esi.dz', 'View/assets/default_avatar.png', 'admin', 'N/A', 'Administration', 'Gestion', 'Compte administrateur système.', 'Administrateur'),
('user', 'user', 'Lambda', 'User', 'user@esi.dz', 'View/assets/default_avatar.png', 'etudiant', 'Master 2', 'IL', 'Développement Web', 'Compte utilisateur standard pour démonstration.', NULL),
('b.bensaber', 'password123', 'Ben Saber', 'Brahim', 'b.bensaber@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'Professeur', 'Intelligence Artificielle', 'IA et Vision par Ordinateur', 'Professeur expert en IA avec 20 ans d\'expérience académique et industrielle.', 'Directeur du Laboratoire'),

-- Enseignants-chercheurs
('s.amrouche', 'password123', 'Amrouche', 'Sarah', 's.amrouche@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'MCA', 'Sécurité Informatique', 'Sécurité des réseaux IoT', 'Maître de conférences spécialisée en cybersécurité et cryptographie.', 'Chef Équipe SOC'),
('m.kaci', 'password123', 'Kaci', 'Mohamed', 'm.kaci@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'MCB', 'Systèmes Distribués', 'Cloud Computing et Virtualisation', 'Spécialiste en architectures cloud et optimisation de conteneurs.', 'Chef Équipe DASE'),
('n.belkacem', 'password123', 'Belkacem', 'Nadia', 'n.belkacem@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'MCA', 'Intelligence Artificielle', 'Apprentissage Automatique', 'Experte en deep learning et traitement d\'images médicales.', 'Chef Équipe CISCO'),
('l.meziani', 'password123', 'Meziani', 'Lyes', 'l.meziani@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'Professeur', 'Ingénierie Web', 'Services Web et Big Data', 'Professeur en ingénierie web et systèmes d\'information.', 'Chef Équipe ISEWED'),

('a.berkane', 'password123', 'Berkane', 'Amel', 'a.berkane@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'MCA', 'Réseaux', 'Protocoles IoT', 'Spécialiste en protocoles de communication sans fil.', NULL),
('k.djebbar', 'password123', 'Djebbar', 'Karim', 'k.djebbar@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'MCB', 'Génie Logiciel', 'Méthodologies Agiles', 'Expert en qualité logicielle et DevOps.', NULL),
('f.hadj', 'password123', 'Hadj Said', 'Farid', 'f.hadjsaid@esi.dz', 'View/assets/default_avatar.png', 'enseignant-chercheur', 'MCA', 'Intelligence Artificielle', 'NLP et Text Mining', 'Chercheur en traitement automatique du langage naturel.', NULL),

-- Doctorants
('y.brahim', 'password123', 'Brahim', 'Youcef', 'y.brahim@esi.dz', 'View/assets/default_avatar.png', 'doctorant', 'Doctorant', 'IA', 'Computer Vision', 'Thèse sur la reconnaissance d\'objets en temps réel.', NULL),
('s.khelif', 'password123', 'Khelif', 'Samia', 's.khelif@esi.dz', 'View/assets/default_avatar.png', 'doctorant', 'Doctorant', 'Sécurité', 'Blockchain', 'Thèse sur les applications blockchain en santé.', NULL),
('r.messaoudi', 'password123', 'Messaoudi', 'Riadh', 'r.messaoudi@esi.dz', 'View/assets/default_avatar.png', 'doctorant', 'Doctorant', 'Cloud', 'Serverless Computing', 'Thèse sur l\'optimisation des fonctions serverless.', NULL),
('h.benali', 'password123', 'Benali', 'Hana', 'h.benali@esi.dz', 'View/assets/default_avatar.png', 'doctorant', 'Doctorant', 'Big Data', 'Stream Processing', 'Thèse sur le traitement de flux de données massives.', NULL),
('a.ziani', 'password123', 'Ziani', 'Amine', 'a.ziani@esi.dz', 'View/assets/default_avatar.png', 'doctorant', 'Doctorant', 'IA', 'Reinforcement Learning', 'Thèse sur l\'apprentissage par renforcement profond.', NULL),
('l.benabdallah', 'password123', 'Benabdallah', 'Lydia', 'l.benabdallah@esi.dz', 'View/assets/default_avatar.png', 'doctorant', 'Doctorant', 'Web', 'Progressive Web Apps', 'Thèse sur les architectures PWA et performance.', NULL);

-- B. SETTINGS
INSERT INTO settings (id, nom_laboratoire, about_labo, directeur_labo_id, contact_email, contact_phone) VALUES
(1, 'Laboratoire de Recherche ESI (LRE)', 
'Le LRE est un pôle d\'excellence dédié à la recherche en informatique avancée. Nos domaines couvrent l\'IA, la sécurité informatique, le cloud computing, et l\'ingénierie web. Nous comptons 4 équipes de recherche actives, plus de 15 chercheurs et 30+ publications internationales par an.',
3, 'contact@lre-esi.dz', '+213 21 24 58 59');

-- C. EQUIPES
INSERT INTO teams (nom, description, chef_id) VALUES
('ISEWED - Information Systems Engineering and Web Data', 'Équipe spécialisée en ingénierie des systèmes d\'information, services web, Big Data et ontologies.', 7),
('SOC - Service Oriented Computing', 'Équipe focalisée sur les architectures orientées services, sécurité des réseaux et IoT.', 4),
('DASE - Data and Software Engineering', 'Équipe dédiée à l\'ingénierie logicielle, qualité du code, cloud computing et DevOps.', 5),
('CISCO - Computational Intelligence and Soft Computing', 'Équipe travaillant sur l\'intelligence artificielle, machine learning, vision par ordinateur et NLP.', 6);

-- D. MEMBRES DES EQUIPES
INSERT INTO team_members (team_id, user_id, role_dans_equipe) VALUES
(1, 7, 'Chef d\'équipe'), (1, 16, 'Doctorant'),
(2, 4, 'Chef d\'équipe'), (2, 8, 'Membre permanent'), (2, 12, 'Doctorant'),
(3, 5, 'Chef d\'équipe'), (3, 9, 'Membre permanent'), (3, 13, 'Doctorant'),
(4, 6, 'Chef d\'équipe'), (4, 3, 'Membre senior'), (4, 10, 'Membre permanent'), (4, 11, 'Doctorant'), (4, 14, 'Doctorant'), (4, 15, 'Doctorant'), (4, 2, 'Etudiant');

-- E. PARTENAIRES
INSERT INTO partners (nom, type, site_web) VALUES
('Université de Sorbonne', 'universite', 'https://www.sorbonne-universite.fr'),
('CERIST', 'organisme', 'https://www.cerist.dz'),
('Condor Electronics', 'entreprise', 'https://www.condor.dz'),
('Algérie Télécom', 'entreprise', 'https://www.algerietelecom.dz'),
('USTHB', 'universite', 'https://www.usthb.dz'),
('Sonatrach', 'entreprise', 'https://sonatrach.com'),
('INRIA France', 'organisme', 'https://www.inria.fr');

-- F. PROJETS
INSERT INTO projects (titre, description, responsable_id, thematique, type_financement, statut, date_debut, date_fin) VALUES
('DeepVision-DZ - Reconnaissance d\'Objets Temps Réel', 'Développement d\'un système de reconnaissance d\'objets utilisant le deep learning pour applications de surveillance intelligente.', 3, 'IA', 'PRFU', 'en_cours', '2024-01-15', '2026-12-31'),
('SecureIoT - Sécurisation des Réseaux IoT', 'Projet de recherche sur les protocoles de sécurité pour objets connectés dans le contexte médical.', 4, 'Sécurité Informatique', 'DGRSDT', 'en_cours', '2023-09-01', '2025-08-31'),
('CloudOpt - Optimisation Cloud Computing', 'Optimisation des ressources dans les environnements cloud hybrides avec focus sur les conteneurs.', 5, 'Cloud Computing', 'PNR', 'en_cours', '2024-03-01', '2027-02-28'),
('NLPArabic - Traitement Automatique de l\'Arabe', 'Système de traitement du langage naturel spécialisé pour la langue arabe et ses dialectes.', 10, 'IA', 'PRFU', 'en_cours', '2023-06-01', '2025-05-31'),
('BlockHealth - Blockchain pour la Santé', 'Application de la technologie blockchain pour sécuriser les dossiers médicaux électroniques.', 12, 'Sécurité Informatique', 'Entreprise', 'en_cours', '2024-02-01', '2026-01-31'),
('SmartCity-Alger - Ville Intelligente', 'Plateforme IoT pour la gestion intelligente des services urbains à Alger.', 8, 'IoT', 'Ministériel', 'en_cours', '2024-06-01', '2027-05-31'),
('WebPerf - Performance des Applications Web', 'Étude et optimisation des performances des Progressive Web Apps.', 7, 'Ingénierie Web', 'PRFU', 'en_cours', '2024-01-01', '2025-12-31'),
('AIEducation - IA pour l\'Enseignement', 'Système d\'apprentissage adaptatif basé sur l\'IA pour l\'enseignement supérieur.', 6, 'IA', 'DGRSDT', 'termine', '2021-09-01', '2024-08-31'),
('CyberDefense - Détection d\'Intrusions', 'Système de détection d\'intrusions utilisant le machine learning.', 4, 'Sécurité Informatique', 'PNR', 'termine', '2020-01-01', '2023-12-31'),
('DataStream - Traitement de Flux', 'Framework pour le traitement temps réel de flux de données massives.', 5, 'Big Data', 'PRFU', 'en_cours', '2023-10-01', '2025-09-30'),
('RobotVision - Vision pour Robots Autonomes', 'Système de vision par ordinateur pour robots autonomes en environnement industriel.', 3, 'IA', 'Entreprise', 'soumis', '2025-03-01', '2028-02-28'),
('5G-Security - Sécurité des Réseaux 5G', 'Analyse et amélioration de la sécurité des réseaux 5G.', 4, 'Sécurité', 'PNR', 'soumis', '2025-06-01', '2028-05-31');

-- G. MEMBRES DES PROJETS
INSERT INTO project_members (project_id, user_id, role_projet) VALUES
(1, 3, 'Responsable'), (1, 11, 'Développeur'), (1, 15, 'Chercheur'), (1, 2, 'Etudiant'),
(2, 4, 'Responsable'), (2, 8, 'Co-responsable'), (2, 12, 'Développeur'),
(3, 5, 'Responsable'), (3, 13, 'Chercheur'),
(4, 10, 'Responsable'), (4, 11, 'Développeur'),
(5, 4, 'Encadrant'), (5, 12, 'Doctorant principal'),
(6, 8, 'Responsable'), (6, 4, 'Consultant sécurité'),
(7, 7, 'Responsable'), (7, 16, 'Développeur'),
(8, 6, 'Responsable'), (8, 14, 'Chercheur'),
(9, 4, 'Responsable'),
(10, 5, 'Responsable'), (10, 14, 'Développeur'),
(11, 3, 'Responsable'), (11, 15, 'Chercheur'),
(12, 4, 'Responsable'), (12, 8, 'Co-responsable');

-- H. PARTENAIRES DES PROJETS
INSERT INTO project_partners (project_id, partner_id) VALUES
(1, 3), (2, 4), (3, 2), (5, 6), (6, 4), (8, 1), (9, 2), (11, 3), (12, 7);

-- I. PUBLICATIONS
INSERT INTO publications (titre, resume, type, date_publication, conference, doi, fichier_pdf, project_id, team_id, statut) VALUES
('Deep Learning for Real-Time Object Detection in Surveillance Systems', 'Novel approach using YOLO architecture.', 'conference', '2024-11-15', 'IEEE ICPR', '10.1109/ICPR.2024.12345', 'uploads/publications/sample1.pdf', 1, 4, 'valide'),
('Secure Communication Protocols for Medical IoT Devices', 'Comprehensive study of encryption methods for healthcare wearables.', 'article', '2024-09-20', 'IEEE IoT Journal', '10.1109/JIOT.2024.67890', 'uploads/publications/sample2.pdf', 2, 2, 'valide'),
-- Nouvelles publications enrichies
('Efficient Serverless Architectures for Big Data Processing', 'Evaluating performance of AWS Lambda vs Azure Functions.', 'article', '2024-05-10', 'ACM Computing Surveys', '10.1145/367890', 'uploads/publications/sample3.pdf', 3, 3, 'valide'),
('Arabic NLP: State of the Art and Future Directions', 'Survey of NLP techniques applied to Arabic language.', 'livre', '2024-01-15', 'Springer', NULL, 'uploads/publications/book1.pdf', 4, 4, 'valide'),
('Blockchain-Based Electronic Health Records', 'Architecture for secure EHR sharing.', 'conference', '2023-12-05', 'IEEE Blockchain', '10.1109/BLOCK.2023.112', 'uploads/publications/sample4.pdf', 5, 2, 'valide'),
('Smart Traffic Control System using IoT', 'Implementation in Algiers city center.', 'these', '2024-06-30', NULL, NULL, 'uploads/publications/these1.pdf', 6, 2, 'valide'),
('Optimizing Progressive Web Apps for Low Bandwidth Networks', 'Techniques for caching and offline usage.', 'conference', '2023-10-22', 'WWW 2023', '10.1145/WWW.2023.77', 'uploads/publications/sample5.pdf', 7, 1, 'valide'),
('AI-Driven Adaptive Learning Platforms', 'Case study at USTHB.', 'article', '2022-11-18', 'Computers & Education', '10.1016/j.compedu.2022.10', 'uploads/publications/sample6.pdf', 8, 4, 'valide'),
('Intrusion Detection in Industrial Control Systems', 'Using Random Forest and SVM.', 'rapport', '2021-05-15', NULL, NULL, 'uploads/publications/report1.pdf', 9, 2, 'valide'),
('Real-Time Data Stream Processing Frameworks', 'Benchmarking Apache Flink vs Storm.', 'memoire', '2024-06-20', NULL, NULL, 'uploads/publications/memo1.pdf', 10, 3, 'valide');


INSERT INTO publication_authors (pub_id, user_id, ordre_auteur) VALUES
(1, 3, 1), (1, 11, 2),
(2, 4, 1), (2, 8, 2),
(3, 5, 1), (3, 13, 2),
(4, 10, 1), (4, 11, 2),
(5, 12, 1), (5, 4, 2),
(6, 8, 1),
(7, 7, 1), (7, 16, 2),
(8, 6, 1), (8, 14, 2),
(9, 4, 1),
(10, 14, 1);

-- ==========================================================
-- J. DONNEES ENRICHIES (30 EQUIPEMENTS, EVENTS, RESERVATIONS)
-- ==========================================================

-- EQUIPEMENTS (30+ articles)
INSERT INTO equipments (nom, type, description, localisation, etat, image_url) VALUES
('Serveur GPU NVIDIA A100', 'Serveur', 'Serveur de calcul haute performance pour Deep Learning.', 'Salle Serveur A', 'disponible', 'View/assets/img/default_equip.jpg'),
('Cluster Raspberry Pi 4 (8 nœuds)', 'IoT', 'Cluster pour calcul distribué Edge Computing.', 'Labo IoT', 'disponible', 'View/assets/img/default_equip.jpg'),
('Imprimante 3D Prusa i3 MK3S+', 'Imprimante', 'Imprimante 3D FDM de précision.', 'FabLab', 'en_maintenance', 'View/assets/img/default_equip.jpg'),
('Imprimante Résine Formlabs Form 3', 'Imprimante', 'Imprimante SLA haute résolution.', 'FabLab', 'disponible', 'View/assets/img/default_equip.jpg'),
('Scanner 3D EinScan-SP', 'Scanner', 'Scanner 3D de bureau.', 'FabLab', 'disponible', 'View/assets/img/default_equip.jpg'),
('Casque VR Oculus Quest 2 #1', 'VR', 'Casque autonome 128GB.', 'Labo Interaction', 'reserve', 'View/assets/img/default_equip.jpg'),
('Casque VR Oculus Quest 2 #2', 'VR', 'Casque autonome 128GB.', 'Labo Interaction', 'disponible', 'View/assets/img/default_equip.jpg'),
('Casque HTC Vive Pro 2', 'VR', 'Casque VR PC haute fidélité.', 'Labo Interaction', 'disponible', 'View/assets/img/default_equip.jpg'),
('Hololens 2', 'AR', 'Casque de réalité mixte.', 'Labo Interaction', 'hors_service', 'View/assets/img/default_equip.jpg'),
('Oscilloscope Tektronix TBS1052B', 'Electronique', 'Oscilloscope numérique 50MHz.', 'Labo IoT', 'disponible', 'View/assets/img/default_equip.jpg'),
('Analyseur de Spectre Siglent', 'Electronique', 'Analyseur de spectre 1.5GHz.', 'Labo IoT', 'disponible', 'View/assets/img/default_equip.jpg'),
('Kit FPGA Xilinx Spartan-7', 'Electronique', 'Kit développement FPGA.', 'Labo IoT', 'en_maintenance', 'View/assets/img/default_equip.jpg'),
('Kit FPGA Altera Cyclone V', 'Electronique', 'Kit développement FPGA.', 'Labo IoT', 'disponible', 'View/assets/img/default_equip.jpg'),
('Station Soudage Weller', 'Electronique', 'Station de soudage professionnelle.', 'Labo IoT', 'disponible', 'View/assets/img/default_equip.jpg'),
('PC Portable Dell XPS 15 #1', 'Ordinateur', 'i9, 32GB RAM, 1TB SSD.', 'Stock', 'disponible', 'View/assets/img/default_equip.jpg'),
('PC Portable Dell XPS 15 #2', 'Ordinateur', 'i9, 32GB RAM, 1TB SSD.', 'Stock', 'reserve', 'View/assets/img/default_equip.jpg'),
('PC Portable MacBook Pro M1', 'Ordinateur', 'M1 Pro, 16GB.', 'Stock', 'disponible', 'View/assets/img/default_equip.jpg'),
('Workstation HP Z4', 'Ordinateur', 'Xeon W, 64GB ECC, Quadro P2200.', 'Labo IA', 'disponible', 'View/assets/img/default_equip.jpg'),
('Serveur Stockage Synology NAS', 'Serveur', 'NAS 40TB RAID 6.', 'Salle Serveur B', 'disponible', 'View/assets/img/default_equip.jpg'),
('Switch Cisco Catalyst 9200', 'Reseau', 'Switch 48 ports PoE+.', 'Salle Reseau', 'disponible', 'View/assets/img/default_equip.jpg'),
('Routeur Cisco ISR 4000', 'Reseau', 'Routeur de bordure.', 'Salle Reseau', 'disponible', 'View/assets/img/default_equip.jpg'),
('Kit Capteurs IoT (Temp/Hum/Gaz)', 'IoT', 'Lot de 50 capteurs divers.', 'Labo IoT', 'disponible', 'View/assets/img/default_equip.jpg'),
('Drone DJI Mavic 2 Enterprise', 'Robotique', 'Drone avec caméra thermique.', 'Labo Robotique', 'reserve', 'View/assets/img/default_equip.jpg'),
('Robot Mobile TurtleBot 3', 'Robotique', 'Robot ROS pour éducation.', 'Labo Robotique', 'disponible', 'View/assets/img/default_equip.jpg'),
('Bras Robotique Niryo One', 'Robotique', 'Bras 6 axes éducatif.', 'Labo Robotique', 'en_maintenance', 'View/assets/img/default_equip.jpg'),
('Tablette Graphique Wacom Cintiq', 'Peripherique', 'Ecran interactif 24 pouces.', 'Labo Design', 'disponible', 'View/assets/img/default_equip.jpg'),
('Projecteur 4K Sony', 'Audiovisuel', 'Projecteur laser 4K.', 'Salle Conférence', 'disponible', 'View/assets/img/default_equip.jpg'),
('Système Visioconférence Logitech', 'Audiovisuel', 'Camera PTZ + Micro.', 'Salle Réunion A', 'disponible', 'View/assets/img/default_equip.jpg'),
('Salle de Réunion A (10 pers)', 'Salle', 'Salle avec tableau blanc interactif.', 'Bloc Admin', 'reserve', 'View/assets/img/default_equip.jpg'),
('Salle de Réunion B (6 pers)', 'Salle', 'Petite salle de travail.', 'Bloc Admin', 'disponible', 'View/assets/img/default_equip.jpg'),
('Auditorium (200 places)', 'Salle', 'Grand amphithéâtre.', 'RDC', 'reserve', 'View/assets/img/default_equip.jpg');

-- EVENEMENTS (Mixte Passé/Futur pour News/Events)
INSERT INTO events (titre, description, date_event, lieu, type, public_cible, image_url) VALUES
-- FUTUR (2026) -> Upcoming Events
('Conférence Nationale sur l\'IA', 'Une grande conférence réunissant les experts nationaux en Intelligence Artificielle.', '2026-05-15 09:00:00', 'Auditorium ESI', 'conference', 'public', 'View/assets/img/default_event.jpg'),
('Atelier Docker et Kubernetes', 'Introduction pratique à l\'orchestration de conteneurs pour les étudiants.', '2026-03-20 14:00:00', 'Labo 03', 'atelier', 'interne', 'View/assets/img/default_event.jpg'),
('Hackathon HealthTech 2026', 'Compétition de 48h pour innover dans la santé numérique.', '2026-04-10 18:00:00', 'Bibliothèque', 'hackathon', 'public', 'View/assets/img/default_event.jpg'),
('Soutenance de Thèse: Y. Brahim', 'Sujet: Deep Learning for Computer Vision.', '2026-06-05 10:00:00', 'Salle de Conférences', 'soutenance', 'public', 'View/assets/img/default_event.jpg'),
('Réunion d\'équipe CISCO', 'Point mensuel sur les travaux en cours.', '2026-02-01 13:30:00', 'Salle Réunion A', 'reunion', 'interne', 'View/assets/img/default_event.jpg'),
('Workshop Sécurité Offensive', 'Techniques de Pentesting avancées.', '2026-03-05 09:00:00', 'Labo Sécurité', 'atelier', 'interne', 'View/assets/img/default_event.jpg'),

-- PASSÉ (2025) -> Actualités Scientifiques
('Journée Portes Ouvertes LRE 2025', 'Présentation des travaux du laboratoire aux nouveaux étudiants.', '2025-09-15 09:00:00', 'Hall ESI', 'seminaire', 'public', 'View/assets/img/default_event.jpg'),
('Séminaire: Le futur du Cloud', 'Invité: Dr. X de AWS.', '2025-11-25 11:00:00', 'Amphi AP1', 'seminaire', 'public', 'View/assets/img/default_event.jpg'),
('Formation Rédaction Scientifique', 'Comment publier dans des conférences A*?', '2025-10-15 14:00:00', 'Salle Réunion B', 'atelier', 'interne', 'View/assets/img/default_event.jpg'),
('Soutenance PFE Master 2 2025', 'Session de soutenances des projets de fin d\'études.', '2025-06-25 08:00:00', 'Salles Bloc Pédagogique', 'soutenance', 'public', 'View/assets/img/default_event.jpg'),
('Symposium Web Engineering 2025', 'Rencontre avec les industriels du web.', '2025-12-20 09:00:00', 'Auditorium', 'conference', 'public', 'View/assets/img/default_event.jpg');

-- CAROUSEL ITEMS
INSERT INTO carousel_items (titre, description_courte, image_url, lien_cible, ordre, active) VALUES
('Bienvenue au LRE', 'Laboratoire de Recherche ESI - Innovation et Excellence', 'View/assets/slide1.jpg', '#', 1, 1),
('Recherche Avancée', 'Intelligence Artificielle, Sécurité et Systèmes Distribués', 'View/assets/slide2.jpg', '#', 2, 1),
('Collaboration Industrielle', 'Partenariats avec les leaders technologiques', 'View/assets/slide3.jpg', '#', 3, 1);

-- OFFRES (5+)
INSERT INTO offers (titre, type, date_limite, fichier_pdf) VALUES
('Stage PFE : Développement Fullstack', 'stage', '2026-12-31', 'offre1.pdf'),
('Bourse Doctorale : Vision par ordinateur', 'bourse', '2026-09-01', 'offre2.pdf'),
('Partenariat R&D avec Sonelgaz', 'collaboration', '2026-04-30', 'offre3.pdf'),
('Ingénieur DevOps Junior', 'collaboration', '2026-06-30', 'offre4.pdf'),
('Sujet de Thèse: Cryptographie Post-Quantique', 'these', '2026-10-15', 'offre5.pdf');

-- RESERVATIONS (20+)
INSERT INTO reservations (equip_id, user_id, date_debut, date_fin, status) VALUES
(6, 12, '2026-02-10 09:00:00', '2026-02-10 12:00:00', 'confirme'), -- VR Headset
(16, 13, '2026-02-15 08:00:00', '2026-02-20 18:00:00', 'confirme'), -- Laptop
(23, 15, '2026-03-01 10:00:00', '2026-03-01 14:00:00', 'en_attente'), -- Drone
(29, 6, '2026-02-05 13:30:00', '2026-02-05 15:30:00', 'confirme'), -- Salle Reunion A
(1, 11, '2026-02-12 00:00:00', '2026-02-19 23:59:00', 'termine'), -- GPU Server
(31, 2, '2026-05-15 08:00:00', '2026-05-15 18:00:00', 'confirme'), -- Auditorium
(30, 4, '2026-02-02 09:00:00', '2026-02-02 11:00:00', 'annule'), -- Salle Reunion B
(8, 11, '2026-02-20 14:00:00', '2026-02-20 16:00:00', 'en_attente'), -- HTC Vive
(2, 12, '2026-03-10 09:00:00', '2026-03-15 17:00:00', 'refuse'), -- Raspberry Pi Cluster
(27, 7, '2026-02-25 10:00:00', '2026-02-25 12:00:00', 'confirme'), -- Projecteur 4K
(6, 16, '2026-02-11 09:00:00', '2026-02-11 12:00:00', 'en_attente'),
(15, 9, '2026-04-01 08:00:00', '2026-04-30 17:00:00', 'confirme'),
(28, 5, '2026-01-20 09:00:00', '2026-01-20 10:00:00', 'termine'),
(11, 13, '2026-02-28 14:00:00', '2026-02-28 17:00:00', 'en_attente'),
(3, 10, '2026-02-10 09:00:00', '2026-02-12 17:00:00', 'en_maintenance'), -- Status handled by equip state usually but here reservation history
(24, 15, '2026-03-20 09:00:00', '2026-03-22 17:00:00', 'confirme'),
(29, 3, '2026-02-08 10:00:00', '2026-02-08 12:00:00', 'confirme'),
(1, 14, '2026-03-01 00:00:00', '2026-03-07 23:59:00', 'en_attente'),
(9, 11, '2026-01-15 14:00:00', '2026-01-15 16:00:00', 'termine'),
(25, 15, '2026-04-05 10:00:00', '2026-04-05 16:00:00', 'en_attente');


-- NOTIFICATIONS (Exemples divers)
INSERT INTO notifications (user_id, message, type, is_read, created_at) VALUES
(2, 'Bienvenue sur la plateforme LabGest !', 'info', 0, NOW()),
(2, 'Nouvel événement public : Conférence Nationale IA.', 'event', 0, NOW()),
(3, 'Votre réservation pour Salle de Réunion C a été confirmée.', 'reservation', 1, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(11, 'Votre demande pour le Serveur GPU est terminée.', 'reservation', 0, NOW()),
(12, 'Vous avez été ajouté à l\'équipe SOC.', 'team', 0, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(4, 'Nouvelle publication ajoutée par Y. Brahim dans votre projet.', 'article', 0, NOW()),
(15, 'Rappel: Hackathon HealthTech 2025 approche.', 'event', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(13, 'Maintenance prévue sur le Cluster Raspberry Pi.', 'info', 0, NOW()),
(6, 'Votre réservation de la Salle Réunion A est confirmée.', 'reservation', 0, NOW()),
(12, 'Votre réservation du Cluster Raspberry Pi a été refusée.', 'reservation', 0, NOW()),
(7, 'Félicitations pour votre nouvelle publication dans WWW 2023.', 'article', 1, DATE_SUB(NOW(), INTERVAL 10 MONTH)),
(16, 'Nouvelle opportunité : Stage PFE disponible.', 'info', 0, DATE_SUB(NOW(), INTERVAL 1 WEEK)),
(11, 'Votre demande pour HTC Vive est en attente de validation.', 'reservation', 0, NOW()),
(3, 'Nouvel événement interne : Réunion d\'équipe CISCO.', 'event', 0, NOW());

-- ACTUALITES
INSERT INTO actualites (titre, description, contenu_complet, categorie, auteur_id, statut, date_publication) VALUES 
('Nouvelle collaboration avec MIT', 'Partenariat stratégique pour la recherche en Intelligence Artificielle', 'Nous sommes heureux d\'annoncer un nouveau partenariat avec le Massachusetts Institute of Technology (MIT) pour développer ensemble des solutions innovantes en IA et apprentissage automatique.', 'Partenariats', 1, 'publiee', '2025-12-10 10:00:00'),
('Lancement du projet DeepVision-DZ', 'Projet phare en vision par ordinateur et détection d\'anomalies', 'Le projet DeepVision-DZ a officiellement commencé. Ce projet vise à développer des systèmes de vision par ordinateur avancés pour les applications industrielles en Algérie.', 'Projets', 3, 'publiee', '2025-11-15 14:30:00'),
('Séminaire: Tendances en Cloud Computing', 'Conférence avec des experts internationaux', 'Rejoignez-nous le 25 novembre pour un séminaire spécial sur les dernières tendances en Cloud Computing. Intervenants: Dr. Sarah Chen (AWS), Prof. Michel Dupont (Université Paris-Saclay).', 'Événements', 1, 'publiee', '2025-11-01 09:00:00'),
('Prix d\'Excellence Scientifique 2025', 'Reconnaissance des meilleures publications de notre équipe', 'L\'équipe SOC a remporté le prix d\'excellence scientifique 2025 pour leurs contributions remarquables en cybersécurité. Félicitations à tous les contributeurs!', 'Reconnaissances', 5, 'publiee', '2025-10-20 16:45:00'),
('Acquisition de nouveaux équipements', 'Installation de serveurs haute performance et systèmes VR', 'Le laboratoire s\'enrichit de nouveaux équipements: 4 serveurs GPU haute performance, 2 systèmes VR dernière génération, et une plateforme de test robotique complète.', 'Infrastructure', 1, 'publiee', '2025-09-28 11:20:00'),
('Journée Portes Ouvertes 2025', 'Venez découvrir nos laboratoires et nos projets', 'Le 15 septembre 2025, nous organisons une journée portes ouvertes pour présenter nos travaux, nos équipements et nos opportunités de stages aux étudiants et aux partenaires industriels.', 'Événements', 1, 'publiee', '2025-08-15 08:00:00');

COMMIT;
