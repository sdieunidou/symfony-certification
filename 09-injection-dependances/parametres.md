# Paramètres de Configuration

## Concept clé
Les paramètres sont des valeurs scalaires (string, int, array, bool) stockées dans le conteneur pour configurer les services (ex: admin email, dossier upload).

## Application dans Symfony 7.0
Définis dans `config/services.yaml` sous la clé `parameters`.

```yaml
parameters:
    admin_email: 'admin@example.com'
    upload_dir: '%kernel.project_dir%/public/uploads'
```

### Injection
On peut injecter un paramètre dans un service de plusieurs façons :
1.  **Autowiring Bind** (Recommandé) :
    ```yaml
    services:
        _defaults:
            bind:
                $adminEmail: '%admin_email%'
    ```
    ```php
    public function __construct(string $adminEmail) { ... }
    ```
2.  **Attribut PHP** (Nouveau Symfony 6.1+) :
    ```php
    use Symfony\Component\DependencyInjection\Attribute\Autowire;

    public function __construct(
        #[Autowire('%admin_email%')] private string $email
    ) {}
    ```

## Points de vigilance (Certification)
*   **%...%** : Syntaxe pour référencer un paramètre.
*   **Env Vars** : `%env(DATABASE_URL)%` permet de lire une variable d'environnement à l'exécution. Ne pas confondre Paramètre (statique, compilé) et Variable d'Env (dynamique).
*   **Kernel Parameters** : Symfony fournit des paramètres par défaut comme `kernel.project_dir`, `kernel.environment`, `kernel.debug`.

## Ressources
*   [Symfony Docs - Parameters](https://symfony.com/doc/current/service_container/parameters.html)

