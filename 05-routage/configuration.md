# Configuration du Routage

## Concept clé
Le routage associe une URL (path) à un Contrôleur.
Symfony supporte PHP Attributes (recommandé), YAML, et XML. L'examen exclut XML/PHP (classique) pour cette section, se concentrant sur YAML et Attributs.

## Application dans Symfony 7.0

### 1. Attributs PHP (Recommandé)
Directement au-dessus de la méthode du contrôleur.

```php
namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;

class BlogController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response { ... }
}
```

### 2. YAML
Dans `config/routes.yaml` (ou `config/routes/`). Utile pour rediriger vers des contrôleurs tiers ou surcharger des routes de bundles.

```yaml
blog_list:
    path: /blog
    controller: App\Controller\BlogController::list
```

## Points de vigilance (Certification)
*   **Ordre** : Le premier qui matche gagne ("First match wins"). Si `/blog/new` est après `/blog/{slug}`, `{slug}` va manger "new". Toujours placer les routes spécifiques AVANT les routes génériques.
*   **Nommage** : Chaque route doit avoir un nom unique pour permettre la génération d'URL.
*   **Annotation** : Les annotations (`@Route`) sont supprimées en Symfony 7 au profit des Attributs PHP 8 (`#[Route]`).

## Ressources
*   [Symfony Docs - Routing](https://symfony.com/doc/current/routing.html)

