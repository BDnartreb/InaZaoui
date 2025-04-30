# Ina Zaoui Web site Contributing recommandations

Bonjour,

Si vous avez ouvert ce document c'est que vous souhaitez apporter votre contribution à l'amélioration de ce site. Merci à vous.

Ce document a pour but de définir des règles d'organisation afin de rendre votre contribution la plus efficace possible.

COMMENT CONTRIBUER ?

En premier lieu, si vous souhaitez contribuer, prenez contact avec le propriétaire du site à l'adresse suivante ina@zaoui.com pour un premier échange sur les modifications que vous souhaitez apporter.
Faire un Fork du repository et créer une nouvelle branche selon la nomenclature ci-dessous depuis la branche principale main.

Lisez et suivre les instructions du fichier ReadMe.md du projet.
Faire vos modifications. 
Créer un fichier ReadMe.md pour documenter et expliquer les modifications proposées.
Lancer les tests existants pour vérifier que vos modifications n'ont pas créé de nouveaux dysfonctionnements
Réaliser si nécessaire des tests spécifiques avec phpUnit pour tester vos modifications.
Intégrer ces tests dans une intégration continue pour faciliter la maintenance du code.
Vérifier que le code respecte les exigences phpstan niveau 6.
Faire un Pull Request une fois votre proposition finalisée.
Le merge de votre Pull Request dans la branche main sera réalisé par le propriétaire du site après validation de votre proposition.

Dernière précision
Même si cela ne devrait pas être nécessaire; il est toujours préférable de rappeler que ce travail collaboratif doit être fait dans le respect des personnes et des règles de politesse élémentaires. Aucun propos discriminatoire ou écart de langage ne sera accepté. Si vous désirez plus d'information sur le comportement attendu, vous pouvez vous référer au code de bonne conduite suivant : https://www.contributor-covenant.org/.

Au plaisir de recevoir votre contribution.

NOMENCLATURE DE NOMMAGE DES BRANCHES

Structure de nommage de la branche :
[type]/[issue-id]-[description]

Type    Utilisation
feature Pour une nouvelle fonctionnalité
bugfix  Pour corriger un bug
hotfix  Correction urgente à déployer rapidement (ex : prod)
chore   Pour du code de maintenance, refactoring, mise à jour de dépendances
test    Pour ajouter ou modifier des tests
docs    Pour la documentation

exemples :
feature/123-ajout-login
bugfix/237-fix-erreur-affichage
hotfix/500-plantage-demarrage
docs/145-mise-a-jour-readme
chore/update-dependencies

#

