# PROJET D'APPLICATION WEB : GESTION D'UN LABORATOIRE INFORMATIQUE UNIVERSITAIRE

Ce projet est réalisé dans le cadre du module **Technologie et Développement Web (TDW)** de la 2ème Année Cycle Supérieur (2CS) à l'**École Supérieure d'Informatique (ESI)**.

**Encadrant :** Monsieur Dellys El-Hachemi  
**Nom de la plateforme suggéré :** LabGest (Gestion de Laboratoire de l'ESI)

---

## 1. ARCHITECTURE DU PROJET : MVC

L'application est développée en suivant le patron de conception **Modèle-Vue-Contrôleur (MVC)**. Cette architecture, obligatoire pour le projet, assure une séparation claire des préoccupations (Séparation des Couches) :

* **Modèle (Models) :** Gère la logique des données et interagit avec la base de données.
* **Vue (Views) :** Responsable de l'affichage (HTML, CSS, JavaScript).
* **Contrôleur (Controllers) :** Sert de pont entre le Modèle et la Vue, gérant les requêtes utilisateur et exécutant la logique métier.

L'ensemble du code est basé sur des **classes** et des **constructeurs (`__construct()`)** en PHP, sans l'utilisation de frameworks lourds comme Laravel.

---

## 2. TECHNOLOGIES ET CONCEPTS CLÉS

Ce projet met en œuvre les concepts et technologies enseignés dans le cours TDW :

* **Langages Côté Serveur :** **PHP** (avec Programmation Orientée Objet - POO).
* **Base de Données :** **MySQL**. La base de données doit impérativement être nommée **`TDW`**.
* **Connexion DB Sécurisée :** Utilisation de l'extension **PDO** (PHP Data Objects) avec des **requêtes préparées** pour prévenir les attaques par injection SQL.
* **Interface Utilisateur :** **HTML5** et **CSS3** (mise en page responsive recommandée).
* **Interactivité :** **JavaScript** et potentiellement **JQuery/AJAX** pour la manipulation dynamique du DOM (Diaporama, Filtrage dynamique).
* **Gestion des Utilisateurs :** Utilisation des **Sessions PHP** (`session_start()`, `$_SESSION`) pour l'authentification et l'accès à la zone administrative.

---

## 3. STRUCTURE DES DOSSIERS (Template de Démarrage)

La structure suivante permet de respecter le principe du MVC et de centraliser le point d'entrée de l'application (Front Controller).

```
project_root/
├── app/                          # Le cœur de l'application
│   ├── Controllers/              # Classes gérant les actions (ex: HomeController)
│   ├── Models/                   # Classes de gestion des données (ex: Database.php, TeamModel.php)
│   └── Views/                    # Fichiers de présentation (HTML/PHP)
│       └── Layout/               # Fichiers communs (header.php, footer.php)
├── config/                       # Fichiers de configuration (ex: config.php pour les infos DB)
├── core/                         # Fichiers utilitaires du cœur (ex: Autoloader.php)
└── public/                       # Le seul dossier accessible directement via l'URL
    ├── index.php                 # Le Routeur / Contrôleur frontal
    └── .htaccess                 # Règles de réécriture d'URL
```

---

## 4. FONCTIONNALITÉS À IMPLÉMENTER (Objectifs du TP)

Le système doit offrir deux interfaces distinctes et gérer des données dynamiques stockées dans la base de données `TDW`.

### 4.1. Interface Publique (Visiteurs & Membres)

* **Page d'Accueil :** Présentation du laboratoire, mission, actualités. Doit inclure un **Diaporama dynamique** (utilisant JavaScript/JQuery).
* **Présentation des Activités/Projets :** Affichage dynamique des projets de recherche du laboratoire.
* **Liste des Équipes et Membres :** Affichage de la liste des équipes et des chercheurs.
* **Formulaire de Contact/Inscription (Facultatif).**

### 4.2. Interface Administrative (Gestionnaire Labo)

* **Authentification :** Accès sécurisé via un formulaire de connexion.
    * **Identifiants par défaut :** `user=admin` et `password=admin`.
* **Gestion des Ressources :** Implémentation complète des opérations **CRUD** (Création, Lecture, Mise à jour, Suppression) pour les entités clés du laboratoire :
    * Projets de Recherche
    * Équipes
    * Membres
    * Actualités / Publications
* **Sécurité et Validation :** Tous les formulaires de gestion doivent inclure :
    * **Validation des données** côté serveur (filtrage et assainissement).
    * Protection contre les failles (XSS, Injection SQL).
