# Configuration du Routage

## Concept clé
Le routeur est le composant qui fait le lien entre une **URL entrante** et le **Code à exécuter** (Contrôleur).
Dans Symfony 7, la configuration se fait principalement par **Attributs PHP**.

## Formats Supportés

### 1. Attributs PHP (`#[Route]`) - **Recommandé**
Standard depuis Symfony 6/7. Remplace les Annotations (`@Route`).
*   **Avantage** : Le code et la route sont au même endroit (Localité). Refactoring facile (renommer la méthode ou classe ne casse pas le lien).
*   **Config** : Nécessite `config/routes/attributes.yaml` pour dire à Symfony où chercher les classes (Kernel).

```php
namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response { ... }
}
```

### 2. YAML (`config/routes.yaml`)
Utilisé pour :
*   Surcharger des routes de bundles tiers.
*   Définir des routes statiques (sans contrôleur PHP dédié, ex: `TemplateController`).
*   Monter des groupes de routes (Importer tout un dossier).
*   Définir des **Alias de Route** (pour la BC).

```yaml
# Import des attributs (Fait par défaut dans une app Symfony)
app_controllers:
    resource: ../src/Controller/
    type: attribute

# Route manuelle
legacy_home:
    path: /accueil
    controller: App\Controller\HomeController::index
```

### 3. PHP (`config/routes.php`)
Utilisé par les power-users pour l'autocomplétion et le refactoring statique.

```php
return function (RoutingConfigurator $routes): void {
    $routes->add('blog_list', '/blog')
        ->controller([BlogController::class, 'list']);
};
```

### 4. XML (Déprécié Symfony 7.4)
Le format XML est officiellement déprécié en 7.4 et sera supprimé en 8.0.

## Alias de Route (Route Aliasing)
Permet de donner plusieurs noms à la même route (ex: pour la rétrocompatibilité après un renommage).
*   **Nouveauté 7.3** : Support des alias dans les Attributs.

```php
#[Route('/product/{id}', name: 'product_show', alias: ['product_details'])]
```

On peut aussi marquer un alias comme **Déprécié** pour prévenir les utilisateurs de l'API :
```php
use Symfony\Component\Routing\Attribute\DeprecatedAlias;

#[Route('/product/{id}', 
    name: 'product_show', 
    alias: new DeprecatedAlias(aliasName: 'product_old', package: 'my/app', version: '1.0')
)]
```

## Groupes et Préfixes
On peut grouper des routes pour leur appliquer des options communes.

### 1. Sur la Classe (Attributs)
Vous pouvez appliquer `#[Route]` sur la classe entière. Toutes les méthodes hériteront de ces configurations.

```php
#[Route(
    path: '/api/{_locale}', 
    name: 'api_', 
    requirements: [
        '_locale' => 'en|fr',
        'host' => 'api.example.com' // Restriction par domaine
    ],
    host: '{host}', // Possibilité d'avoir un host dynamique
    priority: 10
)]
class ApiController extends AbstractController
{
    // URL: api.example.com/api/en/users
    // Name: api_users_list
    #[Route('/users', name: 'users_list')] 
    public function list() {}
}
```

### 2. Par Import (YAML)
C'est très puissant pour préfixer tout un dossier de contrôleurs (ex: Admin).

```yaml
# config/routes.yaml
admin_area:
    resource: ../src/Controller/Admin/
    type: attribute
    prefix: /admin
    name_prefix: admin_
    host: admin.example.com
    requirements:
        _locale: en|fr
```

## 🧠 Concepts Clés
1.  **Compilation** : En prod, toutes les routes (Attributs, YAML, PHP) sont compilées en un seul fichier PHP optimisé (regex géante) dans `var/cache/prod`. Il n'y a pas de différence de performance à l'exécution entre YAML et Attributs.
2.  **First Match Wins** : Le routeur s'arrête à la **première** route qui correspond.
    *   `/blog/new` doit être déclaré **AVANT** `/blog/{slug}`. Sinon `{slug}` va matcher "new" et appeler le contrôleur `show` avec "new" comme slug.

## ⚠️ Points de vigilance (Certification)
*   **UTF-8** : Par défaut, le routeur suppose que les URLs sont en UTF-8. On peut configurer `utf8: true` pour matcher des caractères spéciaux (emojis, accents) dans les regex.

## Ressources
*   [Symfony Docs - Routing](https://symfony.com/doc/current/routing.html)
