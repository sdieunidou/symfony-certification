# Configuration et Environnements

## Concept clé
Symfony sépare strictement :
*   La **Configuration** : Le comportement de l'application (Quels services ? Quelles routes ?). Stockée dans `config/`.
*   L'**Environnement** : Les spécificités de l'infrastructure (IP de la DB, Clé API). Stocké dans des variables d'environnement.

## Hiérarchie `.env` (Composant Dotenv)
Symfony charge les variables d'environnement dans cet ordre (le dernier écrase le précédent) :
1.  Variables réelles du système (export bash, php-fpm). **Gagnant absolu**.
2.  `.env` (Committé, valeurs par défaut).
3.  `.env.local` (Non committé, surcharges machine locale).
4.  `.env.{env}` (Committé, ex: `.env.test`).
5.  `.env.{env}.local` (Non committé, ex: `.env.test.local`).

## Utilisation
*   **YAML** : `%env(DATABASE_URL)%`.
*   **PHP** : `$_ENV['DATABASE_URL']` ou via injection `#[Autowire(env: '...')]`.

## Processeurs de Variables d'Env
On peut transformer la valeur à la volée dans le YAML.
*   `%env(int:MAX_ITEMS)%` : Cast en entier.
*   `%env(bool:DEBUG)%` : Cast en booléen.
*   `%env(json:PAGES)%` : Décode du JSON.
*   `%env(file:SECRET_FILE)%` : Lit le contenu du fichier dont le chemin est dans la var.
*   `%env(base64:KEY)%` : Décode du base64.
*   `%env(trim:VAR)%` : Supprime les espaces.

## Expression Language (Configuration dynamique)
Dans `services.yaml`, on peut utiliser des expressions logiques si nécessaire.

```yaml
services:
    App\Mailer:
        # Si le paramètre 'use_smtp' est vrai, on injecte SmtpMailer, sinon Sendmail
        arguments: ["@=parameter('use_smtp') ? service('App\\SmtpMailer') : service('App\\Sendmail')"]
```

## 🧠 Concepts Clés
1.  **Compilation** : La configuration (`config/`) est compilée en PHP et mise en cache. Elle est statique en prod.
2.  **Runtime** : Les variables d'environnement sont lues à chaque requête (Runtime). C'est ce qui permet de changer un mot de passe DB sans redéployer/vider le cache (juste redémarrer le worker/fpm).

## ⚠️ Points de vigilance (Certification)
*   **Dump Env** : En production, parser le fichier `.env` à chaque requête est lent. La commande `composer dump-env prod` compile le `.env` en un fichier PHP optimisé `.env.local.php`.
*   **Secrets** : Ne jamais committer de secrets dans `.env`. Utiliser le **Secrets Vault** (`bin/console secrets:set`) pour chiffrer les valeurs et les committer en toute sécurité.

## Ressources
*   [Symfony Docs - Configuration](https://symfony.com/doc/current/configuration.html)
*   [Symfony Docs - Env Var Processors](https://symfony.com/doc/current/configuration/env_var_processors.html)
