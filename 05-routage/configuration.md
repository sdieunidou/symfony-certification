# Configuration du Routage

## Concept clé
Le routeur est le composant qui fait le lien entre une **URL entrante** et le **Code à exécuter** (Contrôleur).
Dans Symfony 7, la configuration se fait principalement par **Attributs PHP**.

## Formats Supportés

### 1. Attributs PHP (`#[Route]`) - **Recommandé**
Standard depuis Symfony 6/7. Remplace les Annotations (`@Route`).
*   **Avantage** : Le code et la route sont au même endroit (Localité). Refactoring facile (renommer la méthode ou classe ne casse pas le lien).

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

### 3. XML / PHP
Possibles mais rares (utilisés par les développeurs de bundles pour la performance ou l'autocomplétion XML). Non prioritaires pour la certification.

## Nommage des Routes
Chaque route interne doit avoir un nom unique (`name`).
*   **Convention** : `snake_case`.
*   **Préfixe** : `app_` pour vos routes applicatives, `admin_` pour l'admin, `api_` pour l'API. Cela évite les collisions avec les routes des bundles installés (`fos_user_...`).
*   **Exemple** : `app_blog_show`, `app_cart_add`.

## 🧠 Concepts Clés
1.  **Compilation** : En prod, toutes les routes (Attributs, YAML, XML) sont compilées en un seul fichier PHP optimisé (regex géante) dans `var/cache/prod`. Il n'y a pas de différence de performance à l'exécution entre YAML et Attributs.
2.  **First Match Wins** : Le routeur s'arrête à la **première** route qui correspond.
    *   `/blog/new` doit être déclaré **AVANT** `/blog/{slug}`. Sinon `{slug}` va matcher "new" et appeler le contrôleur `show` avec "new" comme slug.

## ⚠️ Points de vigilance (Certification)
*   **Annotations vs Attributs** : Les annotations (`/** @Route */`) sont dépréciées et nécessitent `doctrine/annotations`. Symfony 7 utilise les attributs natifs PHP 8 (`#[Route]`).
*   **UTF-8** : Par défaut, le routeur suppose que les URLs sont en UTF-8. On peut configurer `utf8: true` pour matcher des caractères spéciaux (emojis, accents) dans les regex.

## Ressources
*   [Symfony Docs - Routing](https://symfony.com/doc/current/routing.html)
