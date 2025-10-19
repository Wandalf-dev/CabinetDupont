# 🦷 Cabinet Dupont - Système de Gestion de Cabinet Dentaire

![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)
![PHP](https://img.shields.io/badge/PHP-8.2-purple.svg)
![MySQL](https://img.shields.io/badge/MySQL-10.4-orange.svg)
![Tests](https://img.shields.io/badge/tests-18%20passing-brightgreen.svg)
![Status](https://img.shields.io/badge/status-stable-success.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

Application web complète de gestion de cabinet dentaire développée en PHP natif avec architecture MVC.

## 📋 Table des matières

- [Aperçu](#-aperçu)
- [Fonctionnalités](#-fonctionnalités)
- [Technologies utilisées](#-technologies-utilisées)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Structure du projet](#-structure-du-projet)
- [Utilisation](#-utilisation)
- [Comptes de test](#-comptes-de-test)
- [Qualité et tests](#-qualité-et-tests)
- [Contribuer](#-contribuer)
- [Liens](#-liens)
- [Support](#-support)

## 🎯 Aperçu

Cabinet Dupont est une solution moderne et intuitive pour la gestion complète d'un cabinet dentaire. Elle permet de gérer les rendez-vous, les patients, les services, les actualités et les horaires d'ouverture via une interface web responsive.

### Captures d'écran

- **Page d'accueil** : Présentation du cabinet, services, horaires
- **Espace patient** : Prise de rendez-vous en ligne
- **Espace administrateur** : Gestion complète du cabinet
- **Planning** : Vue calendrier interactive avec drag & drop

## ✨ Fonctionnalités

### Pour les patients
- 📅 **Prise de rendez-vous en ligne** avec sélection du service
- 👤 **Gestion de profil** (informations personnelles, historique)
- 📰 **Consultation des actualités** du cabinet
- 🕐 **Visualisation des horaires** d'ouverture
- 📱 **Interface responsive** (mobile, tablette, desktop)

### Pour l'administrateur
- 📊 **Tableau de bord administratif** centralisé
- 🗓️ **Planning interactif** avec vue hebdomadaire/mensuelle
- 👥 **Gestion des patients** (CRUD complet)
- 💼 **Gestion des services** (tarifs, durée, couleurs)
- 📝 **Gestion des actualités** (création, modification, publication)
- ⏰ **Configuration des horaires** du cabinet
- 🎨 **Génération de créneaux** automatique
- 📋 **Actions en masse** sur les créneaux

### Fonctionnalités avancées
- 🔐 **Système d'authentification** sécurisé (CSRF, sessions)
- 🎨 **Thème personnalisable** par service (couleurs)
- 📧 **Validation des données** côté client et serveur
- 🔍 **Recherche et tri** dans les tableaux
- 💾 **Sauvegarde automatique** de la base de données
- ♿ **Accessibilité** (ARIA, focus visible, navigation clavier)

## 🛠️ Technologies utilisées

### Backend
- **PHP 8.2** - Langage serveur
- **MySQL 10.4** (MariaDB) - Base de données
- **PDO** - Connexion sécurisée à la base de données
- **Architecture MVC** - Organisation du code

### Frontend
- **HTML5** - Structure sémantique
- **CSS3** - Styles modernes (Grid, Flexbox, animations)
- **JavaScript ES6+** - Interactivité
- **Lottie** - Animations vectorielles
- **FontAwesome 6** - Icônes

### Outils
- **XAMPP** - Environnement de développement
- **Git** - Gestion de versions
- **phpMyAdmin** - Administration de la base de données

## 📦 Prérequis

- **XAMPP** (ou équivalent) avec :
  - PHP >= 8.2
  - MySQL/MariaDB >= 10.4
  - Apache >= 2.4
- **Git** (pour cloner le projet)
- Navigateur web moderne (Chrome, Firefox, Edge, Safari)

## 🚀 Installation

### 1. Cloner le projet

```bash
# Via HTTPS
git clone https://github.com/Wandalf-dev/CabinetDupont.git

# Via SSH (si configuré)
git clone git@github.com:Wandalf-dev/CabinetDupont.git
```

### 2. Placer le projet dans le dossier XAMPP

```bash
# Windows
C:\xampp\htdocs\CabinetDupont

# Linux/Mac
/opt/lampp/htdocs/CabinetDupont
```

### 3. Démarrer les services XAMPP

1. Ouvrir le **XAMPP Control Panel**
2. Démarrer **Apache**
3. Démarrer **MySQL**

### 4. Créer la base de données

**Option A : Via phpMyAdmin (Interface graphique)**

1. Accéder à [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Créer une nouvelle base de données nommée `bdd_dupont`
3. Sélectionner la base de données
4. Cliquer sur **Importer**
5. Choisir le fichier `Backup/bdd_dupont.sql`
6. Cliquer sur **Exécuter**

**Option B : Via ligne de commande**

```bash
# Windows (PowerShell)
& "C:\xampp\mysql\bin\mysql.exe" -u root -e "CREATE DATABASE IF NOT EXISTS bdd_dupont CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
& "C:\xampp\mysql\bin\mysql.exe" -u root bdd_dupont < Backup/bdd_dupont.sql

# Linux/Mac
mysql -u root -e "CREATE DATABASE IF NOT EXISTS bdd_dupont CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root bdd_dupont < Backup/bdd_dupont.sql
```

### 5. Configurer la connexion à la base de données

Vérifier le fichier `app/config/database.php` :

```php
<?php
return [
    'host' => 'localhost',
    'dbname' => 'bdd_dupont',
    'username' => 'root',
    'password' => '', // Laisser vide par défaut avec XAMPP
    'charset' => 'utf8mb4'
];
```

### 6. Accéder à l'application

Ouvrir un navigateur et accéder à :
- **Page d'accueil** : [http://localhost/CabinetDupont](http://localhost/CabinetDupont)
- **Connexion** : [http://localhost/CabinetDupont/auth/login](http://localhost/CabinetDupont/auth/login)

## ⚙️ Configuration

### Configuration de la base URL

Si le projet n'est pas dans le dossier racine `CabinetDupont`, modifier `config.php` :

```php
<?php
// Si le projet est dans un sous-dossier différent
define('BASE_URL', '/mon-dossier');
```

### Configuration des chemins

Les chemins sont configurés automatiquement dans `config.php`. Vérifier que :

```php
define('BASE_URL', '/CabinetDupont');
define('ROOT_PATH', __DIR__);
```

### Permissions des dossiers

Assurer que le dossier `public/uploads/` est accessible en écriture :

```bash
# Linux/Mac
chmod -R 755 public/uploads

# Windows : Propriétés > Sécurité > Modifier les autorisations
```

## 📁 Structure du projet

```
CabinetDupont/
├── app/
│   ├── config/              # Configuration (base de données)
│   ├── controllers/         # Contrôleurs MVC
│   ├── core/               # Classes core (App, Controller, Model, Csrf, Utils)
│   ├── models/             # Modèles de données
│   └── views/              # Vues (templates HTML/PHP)
│       ├── actu/           # Actualités
│       ├── admin/          # Administration
│       ├── agenda/         # Planning
│       ├── auth/           # Authentification
│       ├── creneaux/       # Créneaux
│       ├── error/          # Pages d'erreur
│       ├── horaires/       # Horaires
│       ├── patient/        # Patients
│       ├── rendezvous/     # Rendez-vous
│       ├── service/        # Services
│       ├── templates/      # Templates réutilisables (header, footer)
│       └── user/           # Profil utilisateur
├── assets/                 # Ressources (JSON Lottie)
├── Backup/                 # Sauvegardes SQL
├── css/                    # Feuilles de style
│   ├── base/              # Styles de base
│   ├── components/        # Composants réutilisables
│   ├── layouts/           # Layouts (header, footer)
│   ├── modules/           # Modules spécifiques
│   ├── pages/             # Pages spécifiques
│   └── utils/             # Utilitaires
├── js/                     # Scripts JavaScript
│   ├── components/        # Composants JS
│   ├── modules/           # Modules JS (agenda, créneaux, etc.)
│   ├── pages/             # Scripts par page
│   └── utils/             # Fonctions utilitaires
├── public/
│   └── uploads/           # Images uploadées (services, actualités)
├── .htaccess              # Configuration Apache
├── config.php             # Configuration globale
├── index.php              # Point d'entrée
└── README.md              # Ce fichier
```

## 📖 Utilisation

### Connexion

#### En tant que Patient
1. Accéder à [/auth/login](http://localhost/CabinetDupont/auth/login)
2. Créer un compte ou utiliser un compte de test
3. Accéder à l'espace patient

#### En tant qu'Administrateur
1. Se connecter avec un compte administrateur
2. Accéder au panneau d'administration via le menu

### Prise de rendez-vous (Patient)

1. **Connexion** → Se connecter ou créer un compte
2. **Sélectionner un service** → Choisir le type de consultation
3. **Choisir une date** → Sélectionner un créneau disponible
4. **Confirmer** → Valider le rendez-vous

### Gestion du planning (Administrateur)

1. **Accéder au planning** → Menu "Planning"
2. **Générer des créneaux** → Créneaux > Générer
3. **Visualiser les RDV** → Vue hebdomadaire/mensuelle
4. **Actions sur RDV** → Clic droit pour modifier/annuler
5. **Marquer indisponible** → Sélectionner créneaux + actions en masse

### Gestion des services (Administrateur)

1. **Admin** → Onglet "Services"
2. **Ajouter** → Remplir le formulaire (nom, durée, tarif, couleur)
3. **Modifier** → Cliquer sur l'icône d'édition
4. **Supprimer** → Cliquer sur l'icône de suppression

### Gestion des actualités (Administrateur)

1. **Admin** → Onglet "Actualités"
2. **Créer** → Rédiger l'article avec image
3. **Publier** → Changer le statut à "PUBLIE"
4. **Modifier/Supprimer** → Actions disponibles dans la liste

### Configuration des horaires (Administrateur)

1. **Admin** → Onglet "Horaires"
2. **Configurer par jour** → Ajouter plages horaires (matin/après-midi)
3. **Fermeture** → Laisser vide pour un jour fermé
4. **Sauvegarder** → Les horaires s'affichent sur la page d'accueil

## 👥 Comptes de test

### Administrateur
- **Email** : `admin@cabinetdupont.fr`
- **Mot de passe** : `Admin123!`
- **Rôle** : `MEDECIN`

### Patient
- **Email** : `patient@test.fr`
- **Mot de passe** : `Patient123!`
- **Rôle** : `PATIENT`

## ✅ Qualité et tests

Le projet a été **entièrement testé** avec **PHPUnit** et **Composer** pour garantir sa stabilité et sa fiabilité en production.

### Résultats des tests

```
PHPUnit 10.5.58 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: phpunit.xml

..................                                                18 / 18 (100%)

Time: 00:06.049, Memory: 8.00 MB

OK (18 tests, 38 assertions)
```

### Tests réalisés

#### ✅ Tests Unitaires
- **Modèle Utilisateur** : Création, recherche, validation, mise à jour, suppression, hashage des mots de passe
- **Modèle Service** : Création, récupération, validation des données

#### ✅ Tests Fonctionnels
- **Authentification** : Connexion valide/invalide, vérification des rôles
- **Rendez-vous** : Flux complet de prise de rendez-vous, annulation, récupération

### Validation

- ✅ **18 tests passés** sur 18 (100% de réussite)
- ✅ **38 assertions validées**
- ✅ Transmission Frontend ↔ Backend vérifiée
- ✅ Validation des données côté serveur testée
- ✅ Cohérence de la base de données confirmée

> **Note** : Les fichiers de tests ont été retirés du projet de production pour alléger le code déployé. Le code a été validé et est **stable en production**.

## 🗃️ Base de données

### Tables principales

| Table | Description |
|-------|-------------|
| `utilisateur` | Utilisateurs (patients, médecins, administrateurs) |
| `agenda` | Agendas des praticiens |
| `creneau` | Créneaux horaires disponibles |
| `rendezvous` | Rendez-vous confirmés |
| `service` | Services proposés par le cabinet |
| `actualite` | Actualités du cabinet |
| `horaire_cabinet` | Horaires d'ouverture du cabinet |
| `cabinet` | Informations du cabinet |

### Relations
- Un **utilisateur** peut avoir un **agenda**
- Un **agenda** contient plusieurs **créneaux**
- Un **créneau** peut avoir un **rendez-vous**
- Un **rendez-vous** est lié à un **patient** et un **service**

## 🔒 Sécurité

- ✅ Protection CSRF sur tous les formulaires
- ✅ Validation des données côté serveur
- ✅ Requêtes préparées (PDO) contre les injections SQL
- ✅ Hashage des mots de passe (bcrypt)
- ✅ Gestion des sessions sécurisée
- ✅ Protection des routes (middleware)
- ✅ Validation des types de fichiers uploadés
- ✅ Échappement des données affichées (XSS)

## 🎨 Personnalisation

### Modifier les couleurs du thème

Éditer `css/base/style.css` :

```css
:root {
  --bg: #f4f6fb;
  --brand: #3a6ea5;        /* Couleur principale */
  --accent: #00c6ff;       /* Couleur d'accent 1 */
  --accent-2: #0072ff;     /* Couleur d'accent 2 */
  --text: #1e2936;         /* Couleur du texte */
  --white: #fff;
}
```

### Modifier les informations du cabinet

Éditer directement dans les vues ou via la base de données :

```sql
UPDATE cabinet SET nom = 'Votre Cabinet', adresse = 'Votre Adresse' WHERE id = 1;
```

## 🤝 Contribuer

Les contributions sont les bienvenues ! Pour contribuer :

1. **Fork** le projet
2. Créer une branche (`git checkout -b feature/AmazingFeature`)
3. Commit les changements (`git commit -m 'Add AmazingFeature'`)
4. Push vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrir une **Pull Request**

### Guidelines
- Respecter l'architecture MVC
- Commenter le code en français
- Tester les fonctionnalités avant de push
- Suivre les conventions de nommage existantes

## 📝 Changelog

### Version 1.0.0 (Octobre 2025)
- ✅ Système de gestion des rendez-vous
- ✅ Planning interactif avec drag & drop
- ✅ Gestion des patients, services et actualités
- ✅ Interface responsive
- ✅ Système d'authentification sécurisé
- ✅ Configuration des horaires d'ouverture
- ✅ Génération automatique de créneaux
- ✅ Actions en masse sur les créneaux
- ✅ Tableau de bord administrateur

## 🔮 Roadmap

### Futures fonctionnalités
- [ ] Système de notifications par email
- [ ] Export des données (PDF, Excel)
- [ ] Statistiques et rapports avancés
- [ ] Intégration calendrier (Google Calendar, Outlook)
- [ ] Application mobile
- [ ] Paiement en ligne
- [ ] SMS de rappel
- [ ] Téléconsultation

## 🔗 Liens

- **GitHub** : [https://github.com/Wandalf-dev/CabinetDupont.git](https://github.com/Wandalf-dev/CabinetDupont.git)
- **Site web** : [http://localhost/CabinetDupont](http://localhost/CabinetDupont)
- **Documentation** : Ce README
- **Issues** : [GitHub Issues](https://github.com/Wandalf-dev/CabinetDupont/issues)

## 📞 Support

Pour toute question ou problème :

1. Consulter la [documentation](#-utilisation)
2. Vérifier les [issues GitHub](https://github.com/Wandalf-dev/CabinetDupont/issues)
3. Créer une nouvelle issue si nécessaire
4. Contacter l'équipe de développement

## 👨‍💻 Auteur

**Wandalf-dev**
- GitHub : [@Wandalf-dev](https://github.com/Wandalf-dev)
- Projet : Cabinet Dupont

## 📄 License

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

---

## 🚨 Dépannage

### Problème : Page blanche

**Solution** :
1. Vérifier que Apache et MySQL sont démarrés
2. Vérifier les logs d'erreur PHP dans `C:\xampp\apache\logs\error.log`
3. Activer l'affichage des erreurs dans `php.ini` : `display_errors = On`

### Problème : Erreur de connexion à la base de données

**Solution** :
1. Vérifier que la base `bdd_dupont` existe
2. Vérifier les identifiants dans `app/config/database.php`
3. Vérifier que MySQL est bien démarré

### Problème : Images ne s'affichent pas

**Solution** :
1. Vérifier que le dossier `public/uploads/` existe
2. Vérifier les permissions du dossier (755)
3. Vérifier le chemin dans le code (relatif ou absolu)

### Problème : CSS/JS ne se chargent pas

**Solution** :
1. Vérifier la `BASE_URL` dans `config.php`
2. Vider le cache du navigateur (Ctrl + F5)
3. Vérifier la console du navigateur pour les erreurs 404

### Problème : Erreur 404 sur les routes

**Solution** :
1. Vérifier que le fichier `.htaccess` est présent à la racine
2. Vérifier que `mod_rewrite` est activé dans Apache
3. Vérifier la `BASE_URL` dans `config.php`

---

## 🎉 Remerciements

Merci d'utiliser Cabinet Dupont ! N'hésitez pas à ⭐ le projet sur GitHub si vous l'appréciez.

**Bon développement ! 🚀**
