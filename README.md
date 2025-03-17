# Arcadia
Dossier projet Studi
Arcadia Zoo Application

Description

Arcadia est une application web développée pour le zoo Arcadia, situé près de la forêt de Brocéliande, en Bretagne. L’objectif est de permettre aux visiteurs de découvrir les animaux et services du zoo, tout en intégrant les valeurs écologiques chères au zoo. L’application propose plusieurs fonctionnalités accessibles selon le type d’utilisateur : visiteur, employé, vétérinaire et administrateur.

Fonctionnalités

	•	Visiteur :
	•	Consultation de la page d’accueil avec présentation du zoo et de ses valeurs écologiques.
	•	Visualisation des habitats, des animaux, et des services proposés (restauration, visite guidée, petit train).
	•	Possibilité de laisser un avis, qui doit être validé par un employé avant publication.
	•	Formulaire de contact pour toute demande d’information, envoyé directement par e-mail au zoo.
	•	Espace Utilisateur :
	•	Employé : Validation des avis.
	•	Vétérinaire : Saisie des bilans de santé des animaux, incluant l’état, la nourriture, le grammage et la date.
	•	Administrateur : Gestion des comptes des utilisateurs (employés et vétérinaires uniquement), des services, des habitats, des horaires, des animaux et accès aux statistiques de consultation.
	•	Statistiques : Système de suivi des consultations des animaux, permettant à l’administrateur de connaître la popularité de chaque animal.

Technologies

	•	Front-end : HTML5, CSS (Bootstrap), JavaScript
	•	Back-end : PHP
	•	Base de données : MySQL (relationnelle) et MongoDB (pour les horaires d'ouverture du zoo)
	•	Outils : Git pour la gestion de version, Trello pour la gestion de projet

Installation et Déploiement en Local

Prérequis

	•	Serveur Apache ou Nginx
	•	PHP 7.4 ou supérieur
	•	MySQL
	•	MongoDB (pour la base de données non relationnelle des consultations)
	•	Git

1.	Cloner le projet
    git clone https://github.com/NoSoussaille/DProjet.git

2.	Installation des dépendances
	•	Assurez-vous que PHP, MySQL et MongoDB sont correctement configurés.
3.	Configuration de la base de données
	•	Importez le fichier SQL fourni (database.sql) pour créer les tables nécessaires.
	•	Configurez votre connexion MySQL dans db_connection.php :
            define('DB_HOST', 'localhost');
            define('DB_NAME', 'arcadia');
            define('DB_USER', 'root');
            define('DB_PASS', 'root');

### Importer la base de données

1. Ouvrez **phpMyAdmin** (ou tout autre gestionnaire de base de données).
2. Créez une nouvelle base de données nommée `arcadia`.
3. Allez dans l’onglet **Import** de cette base de données.
4. Importez le fichier `arcadia_database.sql` fourni dans le dossier principal du projet.
5. Assurez-vous que votre fichier `db_connection.php` contient les informations correctes pour la connexion MySQL :
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'arcadia');
   define('DB_USER', 'root');
   define('DB_PASS', 'root');

4.	Lancer l’application en local
	•	Démarrez votre serveur local et accédez à l’application via http://localhost/Arcadia.

Utilisation de Git

Branches

	•	main : branche principale, stable, contenant le code validé.
  •	Develop : branche de développement, contenant le code en cours de développement et de test.
 

Utilisateurs de Test

    •	administrateur : username: josé ; mot de passe: 1aracadia


Documentation et Références

Charte Graphique

	•	Palette de couleurs : tons verts et bruns, pour représenter l’écologie et la nature.
	•	Police principale : Signika Negative.


Gestion de Projet

    La gestion de projet a été organisée sous la forme d’un Kanban avec les colonnes suivantes :
	•	À faire : Recense les fonctionnalités planifiées.
	•	En cours : Fonctions en cours de développement.
	•	Terminées : Fonctionnalités développées et testées sur la branche develop.
