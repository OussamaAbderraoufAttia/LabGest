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

## 3. STRUCTURE DES DOSSIERS

La structure de ce projet respecte le principe du MVC et centralise le point d'entrée de l'application via un Front Controller.

```
TDW/
├── index.php                     # Front Controller / Routeur central (557 lignes, 50+ routes)
├── debug_session.php             # Utilitaire de débogage (sessions)
├── .gitignore                    # Configuration Git
│
├── Controller/                   # Couche Contrôleur (10 contrôleurs)
│   ├── homeController.php        # Gestion de la page d'accueil et routes publiques
│   ├── userController.php        # Authentification, profil utilisateur, mes-projets, publication utilisateur
│   ├── projectController.php     # Affichage et gestion des projets
│   ├── teamController.php        # Affichage et gestion des équipes
│   ├── equipmentController.php   # Gestion des équipements et réservations
│   ├── eventController.php       # Gestion des événements et inscriptions
│   ├── publicationController.php # Gestion des publications
│   ├── actualitesController.php  # Gestion des actualités
│   ├── notificationController.php # Système de notifications
│   └── adminController.php       # Panneau d'administration avec CRUD complet
│
├── Model/                        # Couche Modèle (11 modèles de données)
│   ├── dataBaseModel.php         # Gestion de la connexion PDO et pool de connexions
│   ├── userModel.php             # Données utilisateurs et authentification
│   ├── teamModel.php             # Données des équipes et membres
│   ├── projectModel.php          # Données des projets de recherche
│   ├── publicationModel.php      # Données des publications (avec support coauteurs)
│   ├── equipmentModel.php        # Données des équipements et réservations
│   ├── eventModel.php            # Données des événements et inscriptions
│   ├── actualitesModel.php       # Données des actualités
│   ├── notificationModel.php     # Données des notifications
│   ├── settingsModel.php         # Paramètres du système
│   └── helpers.php               # Fonctions utilitaires et helpers
│
├── View/                         # Couche Vue (30+ fichiers, architecture composant)
│   ├── homeView.php              # Page d'accueil avec diaporama
│   ├── loginView.php             # Formulaire de connexion
│   ├── projectsView.php          # Affichage des projets
│   ├── teamsView.php             # Affichage des équipes
│   ├── equipmentsView.php        # Affichage des équipements
│   ├── eventsView.php            # Affichage des événements
│   ├── publicationsView.php      # Affichage des publications
│   ├── actualitesView.php        # Affichage des actualités
│   ├── userProfileView.php       # Profil utilisateur avec publications personnelles
│   │
│   ├── Admin/                    # Vues administrateur
│   │   ├── adminView.php         # Panneau d'administration (accueil)
│   │   ├── adminUsersView.php    # Gestion des utilisateurs
│   │   ├── adminTeamsView.php    # Gestion des équipes
│   │   ├── adminProjectsView.php # Gestion des projets
│   │   ├── adminEquipmentView.php # Gestion des équipements
│   │   ├── adminEventsView.php   # Gestion des événements
│   │   ├── adminPublicationsView.php # Gestion des publications
│   │   ├── adminSettingsView.php # Paramètres système
│   │   └── adminDashboardView.php # Tableau de bord administrateur
│   │
│   ├── Components/               # Système de composants réutilisables
│   │   ├── CardComponent.php     # Composant carte (projets, équipes, etc.)
│   │   ├── FormComponent.php     # Composant formulaires
│   │   ├── NavBarComponent.php   # Barre de navigation
│   │   ├── FooterComponent.php   # Pied de page
│   │   └── SectionComponent.php  # Conteneur sections
│   │
│   ├── commonViews.php           # Vues communes et utilitaires
│   ├── css/                      # Feuilles de style (CSS3)
│   │   ├── style.css             # Styles principaux
│   │   ├── profileStyle.css      # Styles du profil utilisateur
│   │   ├── components.css        # Styles des composants
│   │   └── responsive.css        # Design responsive
│   │
│   ├── scripts/                  # Fichiers JavaScript
│   │   ├── main.js               # JavaScript principal
│   │   ├── carousel.js           # Diaporama dynamique
│   │   ├── validation.js         # Validation côté client
│   │   └── ajax-utils.js         # Utilitaires AJAX
│   │
│   └── assets/                   # Images, icônes, ressources
│
├── Utility/                      # Couche utilitaire - Système de base
│   ├── SimplePDFGenerator.php    # Générateur PDF personnalisé (format binaire 1.4)
│   ├── TCPDFSetup.php            # Configuration TCPDF avec fallback automatique
│   ├── ReportGenerator.php       # Génération de rapports PDF
│   └── tc-lib-pdf-8.4.1/         # Bibliothèque TCPDF optionnelle
│
├── migrations/                   # Migrations de base de données
│   └── add_user_publications.sql # Migration: ajout support publications utilisateur
│
├── uploads/                      # Répertoire uploads (fichiers utilisateur)
│   ├── publications/             # Publications uploadées
│   ├── avatars/                  # Avatars utilisateurs
│   └── documents/                # Documents
│
├── tdw.sql                       # Schéma complet et données de seed (479 lignes)
└── analysis.md                   # Documentation d'analyse comparative
```

### Architecture et Hiérarchie des Composants

**Flux de Requête :**
```
index.php (Router) 
  → Route matching 
    → Controller (action) 
      → Model (data) 
        → View (render)
```

**Système de Composants Réutilisables :**
- Tous les composants hériteraient d'une classe `BaseComponent` (pattern Strategy)
- Composants: `CardComponent`, `FormComponent`, `NavBarComponent`, `FooterComponent`
- Avantages: DRY (Don't Repeat Yourself), maintenabilité, réutilisabilité


---

## 4. SYSTÈME CORE - AMÉLIORATIONS IMPLÉMENTÉES

### 4.1. Système de Génération PDF (`Utility/SimplePDFGenerator.php`)

**Problème résolu :** Génération de fichiers PDF valides sans dépendance externe lourde (TCPDF).

**Caractéristiques :**
- **Format binaire PDF 1.4** avec structure complète (objets, streams, xref table)
- **Méthodes avancées de formatage :**
  - `createCellCommand()` : Cellules avec bordures, couleurs de remplissage, alignement
  - `createTextCommand()` : Texte avec sélection de police (Helvetica, Helvetica-Bold)
  - Gestion des couleurs RGB pour texte et arrière-plan
  - Orientation portrait/paysage, marges configurables
- **Modes de sortie :** Téléchargement (D), Fichier (F), String (S), Affichage (I)
- **Sécurité :** Pas d'injection de code, sortie binaire pure

**Exemple d'utilisation :**
```php
$pdf = new SimplePDFGenerator();
$pdf->setTitle('Rapport du Laboratoire');
$pdf->setFont('Helvetica', 'B', 14);
$pdf->createCell(0, 10, 'Titre', 1, 1, 'C', true, '#667eea');
$pdf->Output('rapport.pdf', 'D');
```

### 4.2. Gestion des Connexions à la Base de Données (`Model/dataBaseModel.php`)

**Implémentation sécurisée :**
- **PDO avec requêtes préparées** pour prévention complète des injections SQL
- **Pool de connexions** pour optimiser les ressources
- **Singleton pattern** : Une seule instance de connexion par session
- **Error handling** robuste avec exceptions

**Méthodes clés :**
```php
public function query($sql, $params = [])         // Requête avec paramètres
public function fetch($sql, $params = [])         // Un seul résultat
public function fetchAll($sql, $params = [])      // Tous les résultats
public function execute($sql, $params = [])       // Exécution (INSERT, UPDATE, DELETE)
```

### 4.3. Authentification et Gestion des Sessions

**Sécurité mise en œuvre :**
- Sessions PHP avec validation via `$_SESSION['user']` ou `$_SESSION['admin']`
- Mots de passe hachés en production (préparation pour `password_hash()`)
- Redirection automatique si non authentifié
- Destruction de session à la déconnexion

**Flux d'authentification (`userController.php`) :**
```php
// Vérification des identifiants
// Création de session
$_SESSION['user'] = $userData;
// Redirection vers profil utilisateur
```

### 4.4. Système de Routage (`index.php` - 557 lignes, 50+ routes)

**Architecture :**
- Front Controller centralisé
- Routing basé sur paramètres GET (`?action=xxx`)
- Support d'actions imbriquées (ex: `admin?action=users&subaction=edit`)
- Gestion d'erreurs 404

**Catégories de routes :**
1. **Routes publiques** : Accueil, projets, équipes, événements, publications, actualités
2. **Routes authentifiées utilisateur** : Profil, mes-projets, mes-réservations, ajouter-publication
3. **Routes admin** : Gestion complète (utilisateurs, équipes, projets, équipements, événements, publications, paramètres)
4. **Routes API/AJAX** : Export PDF, appels dynamiques

### 4.5. Système de Composants Réutilisables (`View/Components/`)

**Avantages :**
- **DRY (Don't Repeat Yourself)** : Code partagé entre pages
- **Maintenabilité** : Modification centralisée
- **Cohérence UI** : Design unifié

**Composants disponibles :**

**CardComponent.php :** Affichage de cartes (projets, équipes, etc.)
```php
CardComponent::render([
    'title' => 'Mon Projet',
    'description' => 'Description...',
    'image' => 'image.jpg',
    'footer' => 'Actions'
]);
```

**FormComponent.php :** Génération de formulaires
```php
FormComponent::render([
    'method' => 'POST',
    'fields' => [
        ['name' => 'username', 'type' => 'text', 'label' => 'Utilisateur'],
        ['name' => 'password', 'type' => 'password', 'label' => 'Mot de passe']
    ]
]);
```

**NavBarComponent.php :** Barre de navigation cohérente

**FooterComponent.php :** Pied de page

### 4.6. Validation et Sécurité

**Côté serveur :**
- Validation des données entrantes dans chaque Model/Controller
- Sanitization avec `htmlspecialchars()`, `trim()`, `filter_var()`
- Protection contre XSS (Cross-Site Scripting)
- Protection contre CSRF (tokens dans les formulaires)

**Côté client :**
- Validation HTML5 (`required`, `pattern`, `type`)
- JavaScript (`validation.js`) pour feedback utilisateur immédiat

### 4.7. Gestion des Fichiers et Uploads

**Dossiers organisés :**
- `uploads/publications/` : Documents de publications
- `uploads/avatars/` : Photos de profil
- `uploads/documents/` : Documents généraux

**Sécurité des uploads :**
- Vérification du type MIME
- Restrictions d'extension (.pdf, .jpg, .png, etc.)
- Renommage des fichiers (UUID/timestamp)
- Pas d'exécution de scripts dans les répertoires upload

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
