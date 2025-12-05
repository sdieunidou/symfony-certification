# Symfony Flex

## Concept clé
Symfony Flex est un **plugin Composer** qui modernise et automatise la gestion des applications Symfony. Il remplace l'édition manuelle fastidieuse du `AppKernel.php` et de la configuration lors de l'installation de paquets.

## Fonctionnalités Principales

### 1. Aliasing
Permet d'installer des paquets via des noms courts.
*   `composer require logger` => `composer require symfony/monolog-bundle`
*   `composer require orm` => `composer require symfony/orm-pack`
*   `composer require admin` => `composer require easycorp/easyadmin-bundle`

### 2. Recipes (Recettes)
Quand un paquet est installé, Flex cherche une "Recette" sur le serveur `flex.symfony.com`.
Une recette contient des instructions pour :
*   Créer des fichiers de config par défaut (`config/packages/monolog.yaml`).
*   Ajouter des variables d'environnement (`.env`).
*   Enregistrer le Bundle (`config/bundles.php`).
*   Créer des fichiers squelettes (`src/Entity/.gitignore`).
*   Ajouter des scripts au `composer.json` (`auto-scripts`).

### 3. Symfony.lock
Flex génère un fichier `symfony.lock` à la racine.
*   Il stocke la liste des recettes installées et leur version.
*   Il doit être commité dans Git (comme `composer.lock`).
*   Il permet à Flex de savoir si une recette doit être mise à jour (`composer recipes:update`).

## Serveurs de Recettes
1.  **Main Repository** (Officiel) : Recettes de haute qualité, maintenues par la core team, pour les paquets standards. Activé par défaut.
2.  **Contrib Repository** (Communautaire) : Recettes pour n'importe quel paquet tiers.
    *   Nécessite `extra.symfony.allow-contrib: true` dans `composer.json`.
    *   Flex demande confirmation interactive lors de la 1ère utilisation.

## Packs
Un "Pack" est un méta-paquet Composer vide qui ne fait que requérir un ensemble d'autres paquets. C'est une "liste de courses".
*   `symfony/webapp-pack` : Installe orm, twig, mailer, serializer, etc. pour une app complète.
*   `symfony/test-pack` : Installe phpunit, browser-kit, css-selector.

## Private Recipes (Enterprise)
Pour les entreprises, Flex permet d'héberger un serveur de recettes privé pour automatiser la config de paquets internes (propriétaires).

## 🧠 Concepts Clés
1.  **Auto-Discovery** : Flex scanne les paquets installés. Si un bundle n'a pas de recette, il n'est pas configuré automatiquement (il faut l'ajouter à `bundles.php` à la main).
2.  **Désinstallation** : `composer remove` déclenche la désinstallation de la recette (Flex supprime les fichiers de config créés, s'ils n'ont pas été trop modifiés, et nettoie `bundles.php`).
3.  **Update** : Quand vous mettez à jour un paquet (`composer update`), la recette n'est PAS mise à jour (pour ne pas écraser vos configs). Il faut lancer explicitement `composer recipes:update`.

## ⚠️ Points de vigilance (Certification)
*   **Flex != Symfony CLI** : Ne confondez pas le plugin Composer (Flex) et l'outil binaire `symfony` (Serveur web local, gestion de projet).
*   **Public** : Le dossier `public/` et le fichier `index.php` sont générés par la recette du paquet `symfony/framework-bundle` (installé via `symfony/skeleton` ou `webapp`).
*   **Docker** : Flex peut générer et mettre à jour un `compose.yaml` et `Dockerfile` si la recette le prévoit.

## Ressources
*   [Symfony Flex Server](https://flex.symfony.com/)
*   [Symfony Docs - Flex](https://symfony.com/doc/current/setup/flex.html)
