# Symfony Flex

## Concept clé
Symfony Flex n'est pas une version de Symfony, mais un plugin Composer (`symfony/flex`) qui automatise les tâches d'installation et de configuration des paquets tiers dans une application Symfony. Il se base sur un serveur de "recettes" (Recipes).

## Application dans Symfony 7.0
Flex est installé par défaut lors de la création d'un projet (`composer create-project symfony/skeleton`).
Il interagit avec le fichier `composer.json` (section `extra.symfony`).

Ses rôles principaux :
1.  **Alias** : Permet d'installer des paquets via un nom court (ex: `composer require logger` -> installe `symfony/monolog-bundle`).
2.  **Recettes** : Exécute des scripts lors de l'installation (création de fichiers de config dans `config/`, ajout de variables d'env dans `.env`, enregistrement de bundles dans `config/bundles.php`).
3.  **Docker** : Configure automatiquement `docker-compose.yaml` via les recettes (pour certaines dépendances).

## Exemple de configuration

```json
// composer.json
{
    "require": {
        "php": ">=8.2",
        "symfony/flex": "^2"
    },
    "config": {
        "allow-plugins": {
            "symfony/flex": true
        }
    },
    "extra": {
        "symfony": {
            "allow-contrib": false, // true pour accepter les recettes de la communauté (contrib)
            "require": "7.0.*",
            "docker": true
        }
    }
}
```

## Points de vigilance (Certification)
*   **Contrib vs Official** : Il existe deux dépôts de recettes. Le dépôt officiel (maintenu par la Core Team) est activé par défaut. Le dépôt "contrib" (communauté) nécessite l'approbation explicite (`extra.symfony.allow-contrib` ou validation interactive).
*   **Désinstallation** : Flex gère aussi la désinstallation (`composer remove`). Il supprime les fichiers créés par la recette (sauf s'ils ont été modifiés).
*   **Symfony Binary** : Flex n'est pas le binaire Symfony CLI, c'est un plugin Composer.
*   **Update** : `composer sync-recipes` permet de mettre à jour les recettes sans changer les versions des paquets (commande : `composer recipes:update` via le binaire ou plugin flex).

## Ressources
*   [Symfony Docs - Symfony Flex](https://symfony.com/doc/current/setup/flex.html)

