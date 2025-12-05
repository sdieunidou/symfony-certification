# Commandes Natives Indispensables

## Concept clé
Un développeur Symfony passe 30% de son temps dans le terminal. Connaître les commandes natives est vital pour la productivité et le débogage.

## Général
*   `php bin/console list` : Liste toutes les commandes.
*   `php bin/console help [cmd]` : Affiche l'aide et les arguments d'une commande.
*   `php bin/console about` : Infos sur l'environnement (Version Symfony/PHP, Kernel).

## Cache & Config
*   **`cache:clear`** : Vide le cache (et le warmup). Indispensable après déploiement ou changement de config.
*   `cache:pool:clear` : Vide les pools de cache PSR-6 (Redis, Filesystem).
*   `cache:warmup` : Prépare le cache (sans le vider avant).

## Autocomplétion & Profiling
*   **Complétion** : `php bin/console completion` génère le script pour Bash/Zsh/Fish.
*   **Profiling** : `php bin/console app:my-command --profile` active le profileur Symfony pour la commande (visible dans le web profiler).

## Débogage (Debug Bundle)
*   `debug:container` : Liste les services publics.
*   `debug:autowiring` : Liste les types injectables (interfaces).
*   `debug:router` : Liste les routes.
*   `debug:event-dispatcher` : Liste les écouteurs.
*   `debug:config [bundle]` : Dump la configuration actuelle d'un bundle.
*   `debug:twig` : Filtres/fonctions disponibles.

## Développement (Maker Bundle)
*   `make:controller`, `make:entity`, `make:form`, `make:command`, `make:migration`.

## Base de données (Doctrine)
*   `doctrine:database:create`
*   `doctrine:schema:update --force` (Dev uniquement !)
*   `doctrine:migrations:diff` (Génère une migration)
*   `doctrine:migrations:migrate` (Exécute les migrations)

## Qualité (Lint)
*   `lint:yaml` : Vérifie la syntaxe des fichiers YAML.
*   `lint:twig` : Vérifie la syntaxe des templates Twig.
*   `lint:container` : Vérifie que les services injectés existent.

## 🧠 Concepts Clés
1.  **Environnement** : Attention, par défaut la console tourne en `APP_ENV=dev`. `cache:clear` vide le cache **dev**. Pour vider la prod : `APP_ENV=prod php bin/console cache:clear`.
2.  **Interactive** : La plupart des commandes sont interactives (elles posent des questions si des arguments manquent). Pour scripter (CI/CD), utilisez `--no-interaction` (`-n`).

## ⚠️ Points de vigilance (Certification)
*   **Router Match** : `router:match /path` est l'outil de debug ultime pour les erreurs 404.
*   **Secrets** : `secrets:set` / `secrets:list` gère le coffre-fort de secrets chiffrés.

## Ressources
*   [Symfony Docs - Console Commands](https://symfony.com/doc/current/console.html)
