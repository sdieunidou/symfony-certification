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
Quand un paquet est installé, Flex cherche s'il existe une "Recette" pour ce paquet.
Contrairement aux débuts de Symfony 4, il n'y a plus de serveur API centralisé (`flex.symfony.com` est devenu un endpoint statique).

**Fonctionnement moderne (Serverless) :**
Flex interroge directement des fichiers JSON statiques hébergés sur l'infrastructure de GitHub (via les dépôts `symfony/recipes` et `symfony/recipes-contrib`).
1.  **Index** : Flex télécharge un index léger listant les recettes disponibles.
2.  **Manifest** : Si une recette existe pour le paquet, Flex télécharge le fichier `manifest.json` spécifique.

**Actions d'une recette :**
Une fois téléchargée, la recette automatise l'intégration :
*   **Copie de fichiers** : Crée des fichiers de config par défaut (`config/packages/monolog.yaml`).
*   **Variables d'env** : Ajoute des entrées dans `.env` (ex: `DATABASE_URL`).
*   **Bundles** : Modifie `config/bundles.php` pour activer le bundle.
*   **Structure** : Crée des dossiers (ex: `templates/`).
*   **Docker** : Met à jour `compose.yaml` si nécessaire.

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
3.  **Private Recipes** (Enterprise) : Vous pouvez configurer Flex pour interroger vos propres dépôts privés GitHub/GitLab, permettant d'automatiser la configuration de vos paquets internes.

## Packs
Un "Pack" est un méta-paquet Composer vide qui ne fait que requérir un ensemble d'autres paquets. C'est une "liste de courses".
*   `symfony/webapp-pack` : Installe orm, twig, mailer, serializer, etc. pour une app complète.
*   `symfony/test-pack` : Installe phpunit, browser-kit, css-selector.

## 🧠 Concepts Clés
1.  **Auto-Discovery** : Flex scanne les paquets installés. Si un bundle n'a pas de recette, il n'est pas configuré automatiquement (il faut l'ajouter à `bundles.php` à la main).
2.  **Désinstallation** : `composer remove` déclenche la désinstallation de la recette (Flex supprime les fichiers de config créés, s'ils n'ont pas été trop modifiés, et nettoie `bundles.php`).
3.  **Update** : Quand vous mettez à jour un paquet (`composer update`), la recette n'est PAS mise à jour (pour ne pas écraser vos configs). Il faut lancer explicitement `composer recipes:update` pour appliquer les changements de structure proposés par la nouvelle version de la recette.

## ⚠️ Points de vigilance (Certification)
*   **Flex != Symfony CLI** : Ne confondez pas le plugin Composer (Flex) et l'outil binaire `symfony` (Serveur web local, gestion de projet).
*   **Endpoint Statique** : Flex ne "parle" pas à une API intelligente, il consomme des fichiers JSON statiques indexés pour la performance.
*   **Docker** : Flex peut générer et mettre à jour un `compose.yaml` et `Dockerfile` si la recette le prévoit.

## Ressources
*   [Symfony Docs - Flex](https://symfony.com/doc/current/setup/flex.html)
*   [Symfony Recipes Repository](https://github.com/symfony/recipes)
