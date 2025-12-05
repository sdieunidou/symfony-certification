# API PHP (jusqu'à 8.2)

## Concept clé
L'API PHP englobe l'ensemble des fonctions, classes et structures natives du langage. La certification Symfony 7 requiert une maîtrise solide de PHP, notamment les nouveautés introduites jusqu'à la version 8.2, qui est la version minimale recommandée (voire requise par certaines dépendances) pour tirer pleinement parti de Symfony 7.

## Application dans Symfony 7.0
Symfony 7.0 nécessite au minimum **PHP 8.2**. Le framework utilise intensivement les fonctionnalités modernes de PHP comme le typage fort, les attributs, les énumérations (PHP 8.1), et les classes en lecture seule (PHP 8.2).

### Fonctionnalités clés à maîtriser (PHP 8.0 - 8.2) :
*   **PHP 8.0** : Attributs, Union Types, Match expression, Nullsafe operator, Constructor Property Promotion.
*   **PHP 8.1** : Enums, Readonly properties, Intersection types, Fibers, Array unpacking avec clés string.
*   **PHP 8.2** : Readonly classes, Disjunctive Normal Form (DNF) Types, `null`, `false`, et `true` comme types autonomes, Constantes dans les traits.

## Exemple de code

```php
<?php

// PHP 8.1 : Enum
enum Status: string {
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
}

// PHP 8.2 : Readonly Class
readonly class ArticleDto
{
    // PHP 8.0 : Constructor Property Promotion
    public function __construct(
        public string $title,
        public Status $status,
        // PHP 8.0 : Union Types
        public string|null $summary = null,
    ) {}
}

$dto = new ArticleDto('Symfony 7 is great', Status::PUBLISHED);

// PHP 8.0 : Match expression
$message = match($dto->status) {
    Status::DRAFT => 'Brouillon',
    Status::PUBLISHED => 'Publié',
};

// PHP 8.0 : Nullsafe operator (si $dto pouvait être null)
// $summary = $maybeDto?->summary;
```

## Points de vigilance (Certification)
*   **Typage** : Comprendre la différence entre `coercive` (par défaut) et `strict_types=1`. Symfony utilise le mode strict partout.
*   **Nouveautés PHP** : Les questions pièges portent souvent sur la version exacte d'introduction d'une fonctionnalité (ex: Enums en 8.1, Readonly Class en 8.2).
*   **Fonctions dépréciées** : Connaître les fonctions natives dépréciées en 8.2 (ex: `utf8_encode`, `utf8_decode`).
*   **Comparaisons** : Les changements de comportement dans les comparaisons (ex: `0 == "foo"` est `false` depuis PHP 8.0, c'était `true` avant).

## Ressources
*   [PHP 8.0 Released](https://www.php.net/releases/8.0/fr.php)
*   [PHP 8.1 Released](https://www.php.net/releases/8.1/fr.php)
*   [PHP 8.2 Released](https://www.php.net/releases/8.2/fr.php)

