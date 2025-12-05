# Filtres et Fonctions

## Concept clé
Twig distingue deux types de transformations :
1.  **Filtres** (`|`) : Modifient une variable existante (`{{ name|upper }}`).
2.  **Fonctions** (`()`) : Génèrent du contenu ou accèdent à la logique (`{{ path('home') }}`).

## Filtres Natifs (Twig Core)
*   **Texte** : `upper`, `lower`, `capitalize`, `trim`, `striptags`, `nl2br`.
*   **Tableaux** : `first`, `last`, `length`, `join`, `slice`, `sort`, `merge`.
*   **Nombres** : `number_format`, `abs`, `round`.
*   **Divers** : `date` (format), `json_encode`, `default` (valeur par défaut si null/empty).

Exemple `default` :
```twig
{{ user.bio|default('Aucune bio renseignée.') }}
```

## Filtres et Fonctions Symfony (Twig Bridge)
Symfony enrichit considérablement Twig.

### Fonctions
*   `path()`, `url()` : Routage.
*   `asset()` : Assets.
*   `render(controller())` : Embedding.
*   `dump()` : Debug.
*   `form_*()` : Formulaires.
*   `is_granted()` : Sécurité.

### Filtres
*   `trans` : Traduction.
*   `yaml_encode`, `yaml_dump`.
*   `humanize` : Transforme `snake_case` en texte lisible.

## Créer une Extension Twig (Custom)
Si vous avez besoin d'une logique spécifique (ex: formater un prix avec une devise complexe, afficher un statut sous forme de badge HTML), créez une extension.

```php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice(float $number, string $currency = 'EUR'): string
    {
        return number_format($number, 2, ',', ' ') . ' ' . $currency;
    }
}
```
**Usage** : `{{ product.price|price }}`.
L'extension est enregistrée automatiquement comme service (autoconfiguration).

## 🧠 Concepts Clés
1.  **Pipe** : Les filtres s'enchaînent de gauche à droite. `{{ name|trim|upper }}` = `strtoupper(trim($name))`.
2.  **Arguments** : Les filtres et fonctions acceptent des arguments. `{{ date|date('d/m/Y') }}`.

## ⚠️ Points de vigilance (Certification)
*   **Logique dans Template** : Ne faites pas de logique métier complexe dans Twig. Si vous avez besoin d'un filtre de 50 lignes, c'est probablement que le contrôleur aurait dû préparer la donnée, ou que vous avez besoin d'une extension Twig testable unitairement.
*   **Filtre vs Fonction** : Une fonction ne s'applique pas à une variable via `|`. On ne fait pas `{{ 'home'|path }}`, on fait `{{ path('home') }}`.

## Ressources
*   [Twig Filter Reference](https://twig.symfony.com/doc/3.x/filters/index.html)
*   [Create Twig Extension](https://symfony.com/doc/current/templating/twig_extension.html)
