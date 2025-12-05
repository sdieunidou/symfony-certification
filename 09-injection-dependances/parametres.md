# Paramètres de Configuration

## Concept clé
Les paramètres stockent des valeurs scalaires (strings, entiers, booléens) qui définissent la configuration de l'application (non-objet).
Ils sont isolés des services.

## Définition
Dans `config/services.yaml` :

```yaml
parameters:
    # Constantes statiques
    admin_email: 'admin@example.com'
    app.items_per_page: 20
    
    # Référence à des variables d'environnement (dynamique)
    # C'est la bonne pratique pour les secrets
    app.secret_key: '%env(APP_SECRET)%'
    
    # Paramètres système
    uploads_dir: '%kernel.project_dir%/public/uploads'
```

## Utilisation (Injection)

### 1. Via Attribut `#[Autowire]` (Symfony 6.1+)
C'est la méthode la plus simple et locale.

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AdminController
{
    public function __construct(
        #[Autowire('%admin_email%')] private string $adminEmail,
        #[Autowire(env: 'APP_SECRET')] private string $secret
    ) {}
}
```

### 2. Via `bind` (Global)
Pour injecter un paramètre dans *tous* les services qui ont un argument nommé `$adminEmail`.

```yaml
services:
    _defaults:
        bind:
            string $adminEmail: '%admin_email%'
```

## Paramètres du Kernel
Symfony expose des paramètres utiles par défaut :
*   `kernel.project_dir` : Racine du projet.
*   `kernel.environment` : `dev`, `prod`, `test`.
*   `kernel.debug` : `true` ou `false`.
*   `kernel.cache_dir`, `kernel.logs_dir`.

## 🧠 Concepts Clés
1.  **Syntaxe** : `%nom_param%` indique une référence à un paramètre.
2.  **Env Processors** : `%env(int:max:MY_VAR)%`. On peut traiter les variables d'env à la volée (caster en int, décoder du json, trimmer, etc.).

## ⚠️ Points de vigilance (Certification)
*   **ParameterBag** : Dans un contrôleur `AbstractController`, `$this->getParameter('name')` permet de lire un paramètre.
*   **Performance** : Les paramètres statiques sont compilés en dur. Les paramètres `env()` sont résolus au runtime (léger surcoût mais nécessaire pour les secrets Docker/K8s).

## Ressources
*   [Symfony Docs - Parameters](https://symfony.com/doc/current/service_container/parameters.html)
*   [Environment Variables](https://symfony.com/doc/current/configuration/env_var_processors.html)
