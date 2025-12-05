# Créer une Extension Twig (Custom)

## Concept clé
Si vous avez besoin d'une logique d'affichage spécifique (ex: formater un prix, générer un badge HTML, convertir du Markdown), vous devez créer une **Extension Twig**.
Depuis Symfony 7.3, l'utilisation des **Attributs PHP** simplifie considérablement la déclaration.

## 1. Créer la Classe Extension
Il suffit de créer une classe et de marquer les méthodes avec `#[AsTwigFilter]` ou `#[AsTwigFunction]`.
L'autoconfiguration de Symfony (`services.yaml`) détecte automatiquement ces attributs et enregistre l'extension.

```php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use App\Service\MarkdownParser;

// AbstractExtension est optionnel si vous n'utilisez que les attributs, 
// mais recommandé pour getTokenParsers() ou getNodeVisitors()
class AppExtension extends AbstractExtension
{
    // Injection de dépendance possible (Attention au Lazy Loading, voir plus bas)
    public function __construct(
        private MarkdownParser $parser
    ) {}

    #[AsTwigFilter]
    public function price(float $number, string $currency = '€'): string
    {
        return number_format($number, 2, ',', ' ') . ' ' . $currency;
    }

    #[AsTwigFunction]
    public function area(int $width, int $length): int
    {
        return $width * $length;
    }
    
    // Si le nom du filtre diffère de la méthode
    #[AsTwigFilter('md2html')]
    public function markdownToHtml(string $content): string
    {
        return $this->parser->parse($content);
    }
}
```

## 2. Lazy-Loaded Extensions (Runtime) - **Performance**
Si votre extension a des dépendances lourdes (ex: Base de données, Service complexe), injecter ces services dans le constructeur de l'Extension est **mauvais** pour la performance. Twig instancie toutes les extensions au démarrage, même si elles ne sont pas utilisées sur la page.

La solution est de séparer la définition (Extension) de l'exécution (Runtime).

### A. L'Extension (Définition)
Elle ne contient aucune logique, juste la signature.

```php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use App\Twig\AppRuntime;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // On pointe vers la classe Runtime et sa méthode
            new TwigFilter('price', [AppRuntime::class, 'formatPrice']),
        ];
    }
}
```
*Note : Les attributs `#[AsTwigFilter]` supportent-ils le Runtime ? Oui, mais la séparation manuelle reste courante pour expliciter le runtime.*

### B. Le Runtime (Logique)
C'est ici qu'on injecte les dépendances. Cette classe ne sera instanciée que si le filtre `|price` est réellement utilisé dans le template.

```php
namespace App\Twig;

use Twig\Extension\RuntimeExtensionInterface;

class AppRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private SomeHeavyService $service
    ) {}

    public function formatPrice(float $number): string
    {
        // Logique...
    }
}
```

## Filtres et Fonctions Natifs Importants
*   **Filtres** : `trans`, `date`, `format`, `merge`, `map`, `filter`, `sort`.
*   **Fonctions** : `path`, `url`, `asset`, `dump`, `form`.

## 🧠 Concepts Clés
1.  **Safe HTML** : Si votre filtre retourne du HTML (ex: un badge), il sera échappé automatiquement. Pour l'autoriser, ajoutez l'option `is_safe`.
    ```php
    #[AsTwigFilter(isSafe: ['html'])]
    public function badge(string $status): string { ... }
    ```
2.  **Needs Environment** : Si vous avez besoin d'accéder à l'environnement Twig (ex: pour rendre un template depuis le filtre), ajoutez l'option `needs_environment: true` et acceptez `Environment $env` en premier argument.

## ⚠️ Points de vigilance (Certification)
*   **Logique Métier** : Ne mettez pas de logique métier (Business Logic) dans Twig. Twig est pour la **Logique de Présentation**. Si ça touche à la base de données pour modifier des données, c'est un Service/Contrôleur.
*   **Tests** : Les extensions sont des classes PHP pures, donc très faciles à tester unitairement avec PHPUnit.

## Ressources
*   [Symfony Docs - Twig Extensions](https://symfony.com/doc/current/templating/twig_extension.html)
