# Ina Zaoui
#

PRESENTATION DU PROJET :

Ina Zaoui, photographe spécialisée dans les photos de paysages du monde entier, connue pour son mode de déplacement eco-friendly (à dos d'animal, à pied, en vélo ou bateau à voile et montgolfière...), a créé ce site pour présenter le travail de jeunes photographes qu'elle soutient.

Le travail de ces photographes est présenté sous forme :
    d'albums (constitués par Ina)
    de galeries de photos par photographe accessible depuis la liste des photographes
    
Ce site est accessible sans connexion pour une consultation par le grand public.

Les photographes bénéficient d'une session depuis laquelle ils peuvent ajouter ou supprimer leurs photographies.

Ina Zaoui possède une connexion administrateur pour gérer l'ajout ou la suppression de photographies, la constitution d'albums (ajout, modification, suppression) et la gestion des photographes (ajout, modification, suppression). Elle peut "geler" (ROLE_FROZEN) l'accès un photographe qui ne pourra plus accéder à sa session et dont les photographies ne seront plus visibles. En cas de suppression d'un photographe, ces photographie sont également supprimées.

#
INSTALLATION DU PROJET :

Le code du site a été upgradé de la version 5.4 à la version 6.4 de Symfony
Il est développé dans la version php 8.2.12
Les données sont stockées sur une base de données postgreSQL version 17

Récupérer le code depuis le dépôt GitHub via un terminal avec la commande :
$ clone git git@github.com:BDnartreb/InaZaoui.git

Se positionner dans le dossier cloné :
$ cd InaZaoui

Puis taper dans le terminal la commande :
$ composer install

Dans le fichier .env ou .env.local (copie locale du ficher .env) modifier les lignes suivantes pour indiquer :
le nom de la base de données
DATABASE_URL="postgresql://postgres:MOT_DE_PASSE@127.0.0.1:5432/ina_zaoui?serverVersion=16&charset=utf8"
Le MOT_DE_PASSE est à définir par vous même.

Créer la base de données via le terminal:
$ symfony console doctrine:database:create

Créer les tables de la base de données à partir des entity via le terminal :
$ symfony console make:migration
$ symfony console doctrine:migrations:migrate

Implémenter les tables depuis la base de données avec les fichiers .SQL media, guest, album.

EFFECTUER LES TESTS
Créer une base de données de test
Copie du fichier .env.local en .env.test.local
$ symfony console –env=test doctrine:database:create
$ symfony console –env=test doctrine:schema:create

Les images sont disponibles dans le dossier /public/uploads

Dans la base de test le mot de passe générique pour tous les utilisateurs est : "password"
Le login administrateur (ROLE_ADMIN) est : "ina@zaoui.com"

#
NOUVELLES FONCTIONNALITES IMPLEMENTEES DEPUIS LA DERNIERE MISE A JOUR (2025)

Entity User :
    Modification pour :
        Authentification depuis une base de données
        Ajouter de roles permettant la création d'un ROLE_FROZEN (sans droits) afin de "geler" l'accès de certains photographes (Guest).

Ajout de fonctionnalité accessibles à un admin :
    Gestion des photographes (guests), albums, photographies (medias) : affichage en liste, ajout, modification, suppression

Ajout de fonctionnalités accessibles aux users (photographes) :
    Gestion de leurs photographies (medias) : ajout, suppression
    Limitation du poids d'une photographie à 2Mo
    Vérification du type de fichier chargé (format image).

Ajout de tests avec phpunit (cf complément ci-dessous)

Correction de lenteur d'affichage de la page invités (guests) : requête avec un commande JOIN dans UserRepository.php

Ajout d'un fichier Contributing.md définissant les règles de soumission de proposition de modification du code pour de futurs contributeurs.

Mise en place d'une Intégration Continue déclenchée à chaque push sur le repository github

#
POUR EFFECTUER LES TESTS :

Installer PHUnit avec composer
$ composer require –dev phpunit/phpunit ^9
$ composer require –dev symfony/phpunit-bridge
Créer la base de données de test sur le modèle de la base de référence.
Copie .env.local en .env.test.local
Dans un terminal lancer les commandes suivantes :
$ symfony console –env=test doctrine:database:create
$ symfony console –env=test doctrine:schema:create
Charger les fixtures de test
$ composer require orm-fixtures –dev
$ symfony console --env=test doctrine:fixtures:load -n
Lancer les tests
$ vendor/bin/phpunit
Un rapport de couverture de code peut être généré avec la commande
$ vendor/bin/phpunit --coverage-html public/test-coverage

