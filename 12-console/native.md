# Commandes Natives

## Concept clé
Symfony (et ses bundles) fournit de nombreuses commandes prêtes à l'emploi pour gérer l'application, le cache, la base de données, etc.

## Application dans Symfony 7.0
Toutes les commandes s'exécutent via `php bin/console`.

### Commandes Indispensables
*   `list` : Liste toutes les commandes disponibles.
*   `help [cmd]` : Affiche l'aide d'une commande.
*   `cache:clear` : Vide le cache (indispensable après déploiement).
*   `cache:pool:clear` : Vide les pools de cache PSR-6.
*   `router:match` : Débugge le routage.
*   `debug:autowiring` : Liste les services autowirables.
*   `debug:container` : Liste les services.
*   `debug:event-dispatcher` : Liste les écouteurs.
*   `secrets:set` : Gère les secrets chiffrés (Vault).
*   `about` : Affiche les infos sur l'environnement actuel.

### Doctrine
*   `doctrine:database:create`
*   `doctrine:migrations:diff` / `migrate`

### Maker (Dev)
*   `make:controller`, `make:entity`, `make:command`...

## Points de vigilance (Certification)
*   **Alias** : Certaines commandes ont des alias (ex: `debug:router` ou `router:debug`).
*   **Environnement** : Par défaut, la console tourne en `APP_ENV=dev` (sauf si défini dans `.env` ou via `--env=prod`). Attention, `cache:clear` sans option vide le cache de l'env par défaut (dev). Pour la prod : `APP_ENV=prod php bin/console cache:clear`.

## Ressources
*   [Symfony Docs - Console Commands](https://symfony.com/doc/current/console.html)

