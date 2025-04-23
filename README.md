# Ina Zaoui


#
PRESENTATION DU PROJET :

Ina Zaoui, photographe spécialisée dans les photos de paysages du monde entier, connue pour son mode de déplacement eco-friendly (à dos d'animal, à pied, en vélo ou bateau à voile et montgolfière...), propose ce site pour présenter le travail de jeunes photographes qu'elle soutient.
Il donne accès à la liste des photographes.
Les photos sont regroupées par photographe ou dans des albums constitués par Ina Zaoui.
Ce site est accessible sans connexion pour consultation grand public.
Les photographes bénéficient d'une session depuis laquelle ils peuvent ajouter ou supprimer leurs photographies.
Ina Zaoui possède une connexion administrateur pour ajouter, supprimer un photographe ou éventuellement "geler" (ROLE_FROZEN) l'accès un photographe qui ne pourra plus accéder à sa session et dont les photographies ne seront plus visibles. Elle peut gérer les albums et toutes les phtographies.


#
INSTALLATION DU PROJET :

Récupérer le code depuis le dépot GitHub via un terminal avec la commande :
$ clone git git@github.com:BDnartreb/InaZaoui.git

Se positionner dans le dossier cloné :
$ cd InaZaoui

Puis taper dans le terminal la commande :
$ composer install

Dans le fichier .env ou .env.local (copie locale du ficher .env) modifier les lignes suivantes pour indiquer :
le nom de la base de données
DATABASE_URL="postgresql://postgres:MOT_DE_PASSE@127.0.0.1:5432/ina_zaoui?serverVersion=16&charset=utf8"
Le MOT_DE_PASSE est à definir par vous même.

Créer la base de données via le terminal:
$ symfony console doctrine:database:create

Créer les tables de la base de données à partir des entity via le terminal :
$ symfony console make:migration
$ symfony console doctrine:migrations:migrate

Remplir les tables avec des données fictives générées par les fixtures :
$ symfony console doctrine:fixtures:load -n

Les images sont disponibles dans le dossier /public/uploads

Les codes de connexion se fait avec le mot de passe : password
ROLE_ADMIN : ina@zaoui.com
ROLE_USER : userlambda@zaoui.com
ROLE_FROZEN : userfrozen@zaoui.com


#
IMPLEMENTATIONS DE LA VERSION 2025

Entity User :
    Modification pour :
        Authentification depuis une base de données
        Ajouter de roles permettant la création d'un ROLE_FROZEN (sans droits) afin de "geler" l'accès de certains photographes (Guest).

Ajout de fonctionnalité accessibles à un admin :
    Gestion des photographes (guests), albums, photographies (medias) : affichage en list, ajout, modification, suppression

AJout de fonctionnaité accessibles aux users (photographes) :
    Gestion de leurs phtotgraphies (medias) : ajout, suppression.
    Limitation du poids d'une photographie à 2Mo.

Ajout de tests avec phpunit (cf complément ci-dessous)

Correction de lenteur d'affichage de la page invités (guests)

Ajout d'un fichier contributing.md définissant les règles d'évolution du code pour de futurs contributeurs.

Mise en place d'une Intégration Continue déclanché à chaque push sur le repository github


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
$ symfony console --env=test doctrine:fixtures:load -n
Lancer les tests
$ vendor/bin/phpunit
Un rapport de couverture de code peut être généré avec ls commande
$ vendor/bin/phpunit --coverage-html public/test-coverage


