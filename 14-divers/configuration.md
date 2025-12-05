# Configuration et DotEnv

## Concept clé
Symfony distingue la **configuration** (comportement de l'application, `config/*.yaml`) de l'**environnement** (infrastructure, `.env`).

## Application dans Symfony 7.0

### DotEnv
Le composant `Dotenv` charge les fichiers `.env` dans `$_SERVER` et `$_ENV`.
*   `.env` : Valeurs par défaut (committé).
*   `.env.local` : Surcharges locales (non committé, machine développeur).
*   `.env.test` : Environnement de test.

```php
// public/index.php
(new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
```

### ExpressionLanguage
Permet d'utiliser des expressions dynamiques dans la configuration (Security, Routing, Validation, Services).

```yaml
services:
    App\Mailer:
        arguments: ["@=service('App\\\\Config').getSmtpServer()"]
```

## Points de vigilance (Certification)
*   **Hiérarchie .env** : `.env.local` écrase `.env`. En mode test, `.env.test.local` écrase `.env.test`.
*   **Réel vs Var** : En prod, on utilise de "vraies" variables d'environnement serveur (SetEnv Apache, export bash), le fichier `.env` n'est qu'un fallback. `composer dump-env prod` compile le .env en fichier PHP optimisé pour la prod.

## Ressources
*   [Symfony Docs - Configuration](https://symfony.com/doc/current/configuration.html)
*   [Symfony Docs - ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html)

