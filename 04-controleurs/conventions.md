# Conventions de Nommage (Contrôleurs)

## Concept clé
Symfony utilise une stratégie de détection automatique ("Convention over Configuration") pour simplifier le routage et l'injection de dépendances dans les contrôleurs.

## Règles Standards

### 1. La Classe
*   **Namespace** : `App\Controller`.
*   **Suffixe** : `Controller`. Ex: `BlogController`.
    *   *Note* : Ce n'est pas techniquement obligatoire pour que ça marche, mais obligatoire pour l'autodiscovery des routes si vous utilisez le chargement par annotation/attribut dans `config/routes.yaml`.
*   **Héritage** : `AbstractController` (Recommandé).

### 2. La Méthode (Action)
*   **Visibilité** : `public`.
*   **Nom** : camelCase. Pas de suffixe obligatoire (historiquement `Action`, ex: `indexAction`, mais plus nécessaire depuis Symfony 4).
*   **Retour** : Doit retourner un objet `Response`.

### 3. Le Routing (Attributs)
Utilisez `#[Route]` directement au-dessus de la méthode.

```php
#[Route('/blog/{slug}', name: 'blog_show', methods: ['GET'])]
public function show(string $slug): Response
```

### 3.1 Alternative : Configuration YAML
Bien que les attributs PHP soient recommandés, vous pouvez définir vos routes dans `config/routes.yaml` pointant vers un contrôleur.

**Exemple de contrôleur :**
```php
// src/Controller/BlogController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class BlogController extends AbstractController
{
    public function show(string $slug): Response
    {
        // ...
    }
}
```

**Configuration YAML correspondante :**
```yaml
# config/routes.yaml
blog_show:
    path: /blog/{slug}
    controller: App\Controller\BlogController::show
    methods: GET
```

### 4. Single Action Controller (Invokable)
Pour les contrôleurs ne faisant qu'une seule chose, utilisez la méthode magique `__invoke`.

```php
namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;

#[Route('/register', name: 'app_register')]
class RegistrationController extends AbstractController
{
    public function __invoke(Request $request): Response
    {
        // ...
    }
}
```
Cela rend la classe plus propre et facilite l'injection de dépendances (SRP - Single Responsibility Principle).

## Organisation des Dossiers
Pour les grosses applications, ne mettez pas tout à plat dans `src/Controller`. Créez des sous-dossiers par domaine.

*   `src/Controller/Admin/DashboardController.php`
*   `src/Controller/Api/V1/UserController.php`
*   `src/Controller/Blog/PostController.php`

Symfony détecte automatiquement les contrôleurs dans les sous-dossiers.
Vous pouvez appliquer une route préfixe à toute une classe :

```php
#[Route('/api/v1', name: 'api_v1_')]
class UserController extends AbstractController
{
    // URL: /api/v1/users
    // Name: api_v1_users_list
    #[Route('/users', name: 'users_list')]
    public function index() { ... }
}
```

## 🧠 Concepts Clés
1.  **Service Tag** : Les contrôleurs sont des services tagués `controller.service_arguments`. Cela active le `ServiceValueResolver` (injection dans les méthodes) et le `ContainerBag` (injection du Service Locator pour AbstractController).
2.  **Public vs Private** : Les contrôleurs sont des services **privés** (non accessibles via `$container->get()`).

## ⚠️ Points de vigilance (Certification)
*   **Type de retour** : Symfony 7 encourage fortement le typage de retour `: Response`. Si vous ne le mettez pas et que vous retournez `null`, vous aurez une erreur claire du Kernel.
*   **Nom des routes** : Utilisez le snake_case (`blog_show`). Le kebab-case ou camelCase fonctionnent mais snake_case est la convention Symfony.

## Ressources
*   [Symfony Docs - Controller](https://symfony.com/doc/current/controller.html)
*   [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html#controllers)
