# Organisation du Code

## Concept clé
Symfony impose (et suggère) une structure de répertoires standardisée pour faciliter la maintenance et l'onboarding des développeurs.

## Application dans Symfony 7.0 (Structure Flex par défaut)

*   `bin/` : Exécutables (console, phpunit).
*   `config/` : Configuration (YAML, PHP).
    *   `bundles.php` : Liste des bundles activés.
    *   `packages/` : Config par paquet (doctrine.yaml, security.yaml).
    *   `routes/` : Config du routing (si non annotations).
    *   `services.yaml` : Config des services.
*   `public/` : Racine Web (point d'entrée `index.php`, assets compilés).
*   `src/` : Code source PHP (App namespace).
    *   `Controller/`, `Entity/`, `Repository/`, `Service/`, `Form/`...
*   `templates/` : Fichiers Twig.
*   `tests/` : Tests automatiques.
*   `translations/` : Fichiers de traduction.
*   `var/` : Fichiers temporaires (cache, logs). Doit être accessible en écriture.
*   `vendor/` : Dépendances Composer (ne pas toucher).
*   `.env` : Variables d'environnement par défaut.

## Points de vigilance (Certification)
*   **Kernel** : Le fichier `src/Kernel.php` est le cœur de l'application. Il configure le conteneur et les routes.
*   **Public** : Seul le dossier `public/` doit être accessible par le serveur web. Le code source et la config doivent être hors d'atteinte.
*   **Ressources** : Avant Symfony 4, on utilisait `app/Resources`. C'est fini. Tout est à la racine ou dans `src/`.

## Ressources
*   [Symfony Docs - Directory Structure](https://symfony.com/doc/current/best_practices.html#creating-the-project)

