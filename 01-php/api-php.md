# API PHP (jusqu'à 8.2)

## Concept clé
L'API PHP englobe l'ensemble des fonctions, classes et structures natives du langage. La certification Symfony 7 requiert une maîtrise solide de PHP, notamment les nouveautés introduites jusqu'à la version 8.2, qui est la version minimale recommandée (voire requise par certaines dépendances) pour tirer pleinement parti de Symfony 7.

## Application dans Symfony 7.0
Symfony 7.0 nécessite au minimum **PHP 8.2**. Le framework utilise intensivement les fonctionnalités modernes de PHP comme le typage fort, les attributs, les énumérations (PHP 8.1), et les classes en lecture seule (PHP 8.2).

### Fonctionnalités clés à maîtriser (PHP 8.0 - 8.2) :

#### PHP 8.0
*   **Attributs (Attributes)** : Remplacent les annotations PHPDoc. Essentiels pour le Routing, l'Injection de Dépendances, la Validation, et Doctrine dans Symfony 7.
*   **Union Types** : `int|float`.
*   **Match expression** : Alternative stricte et plus lisible au `switch`.
*   **Nullsafe operator** : `$user?->getAddress()?->getCity()`.
*   **Constructor Property Promotion** : Réduit le boilerplate des DTOs et Services.
*   **Named Arguments** : `setCookie(name: 'test', secure: true)`.
*   **`mixed` Type** : Type explicite pour "n'importe quoi".
*   **`throw` expression** : `throw` peut être utilisé là où une expression est attendue (ex: ternaire).

#### PHP 8.1
*   **Enums (Énumérations)** : Types limités (Backed Enums ou Pure Enums). Très utilisés pour les statuts, types, etc.
*   **Readonly properties** : Propriétés initialisables une seule fois.
*   **Intersection types** : `Iterator&Countable` (l'objet doit satisfaire les DEUX types).
*   **Fibers** : Primitives pour le code asynchrone (utilisé par ReactPHP/Amphp, moins direct dans Symfony standard).
*   **Array unpacking avec clés string** : `['a' => 1, ...$array]`.
*   **`never` return type** : Pour les fonctions qui `exit` ou `throw` toujours.

#### PHP 8.2
*   **Readonly classes** : Rend toutes les propriétés de la classe `readonly` automatiquement.
*   **Disjunctive Normal Form (DNF) Types** : Combinaison Union + Intersection `(A&B)|null`.
*   **Types autonomes** : `null`, `false`, et `true` peuvent être utilisés comme types de retour ou paramètres (True type).
*   **Constantes dans les traits** : Les traits peuvent définir des constantes.
*   **Redaction de paramètres sensibles** : Attribut `#[SensitiveParameter]` pour masquer les valeurs dans les traces d'erreur.
*   **Classes dynamiques dépréciées** : Créer des propriétés dynamiques est déprécié (sauf avec `#[AllowDynamicProperties]` ou `stdClass`).

## Exemple de code Complet

```php
<?php

// PHP 8.1 : Enum (Backed Enum)
enum Status: string {
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}

// PHP 8.2 : Readonly Class
// Toutes les propriétés sont implicitement readonly
readonly class ArticleDto
{
    // PHP 8.0 : Constructor Property Promotion
    public function __construct(
        public string $title,
        public Status $status,
        // PHP 8.2 : DNF Type (Intersection + Union)
        // L'auteur doit être (Author ET User) OU null
        public (\App\Entity\Author&\App\Entity\User)|null $author = null,
        // PHP 8.0 : Union Types
        public string|null $summary = null,
    ) {}
}

// Utilisation des Named Arguments (PHP 8.0)
$dto = new ArticleDto(
    title: 'Symfony 7 is great', 
    status: Status::PUBLISHED
);

// PHP 8.0 : Match expression
// Retourne une valeur, comparaison stricte (===), pas de 'break' nécessaire
$message = match($dto->status) {
    Status::DRAFT => 'Brouillon',
    Status::PUBLISHED, Status::ARCHIVED => 'Visible (ou ancien)', // Groupement
    default => 'Statut inconnu', // Default obligatoire si non exhaustif
};

// PHP 8.0 : Nullsafe operator
// Si $dto est null, ou author est null, $name sera null sans erreur
$name = $dto?->author?->getName();

// PHP 8.2 : Sensitive Parameter
function login(
    string $username, 
    #[\SensitiveParameter] string $password
): void {
    throw new \Exception("Erreur login"); 
    // Dans la stack trace, $password sera remplacé par "Object(SensitiveParameterValue)"
}
```

## Typage : Le Système de Types PHP

La certification insiste sur la rigueur du typage.

1.  **Scalar Types** : `bool`, `int`, `float`, `string`.
2.  **Compound Types** : `array`, `object`, `callable`, `iterable`.
3.  **Special Types** : `resource`, `null`, `void`, `never` (8.1).
4.  **Class/Interface Types** : `MyClass`, `DateTimeInterface`.
5.  **Union Types (8.0)** : `T1|T2`.
6.  **Intersection Types (8.1)** : `T1&T2`.
7.  **DNF Types (8.2)** : `(A&B)|C`.

### Coercitive vs Strict
Par défaut, PHP tente de convertir les types (`"10"` -> `10`). Symfony et les bonnes pratiques exigent l'activation du mode strict au début de chaque fichier :
```php
declare(strict_types=1);
```
Cela transforme les erreurs de type en `TypeError` (Exception).

## 🧠 Concepts Clés
1.  **Modernité** : PHP n'est plus un langage de script lâche. C'est un langage typé, orienté objet et robuste.
2.  **Immutabilité** : Les classes et propriétés `readonly` favorisent l'immutabilité, réduisant les effets de bord (très apprécié en architecture Hexagonale/DDD).
3.  **Attributs** : Ils sont la méthode standard pour ajouter des métadonnées au code (Configuration, Validation, Mapping).
4.  **Exhaustivité** : Le `match` force souvent à gérer tous les cas (notamment avec les Enums), ce qui réduit les bugs.

## ⚠️ Points de vigilance (Certification)
*   **Dépréciations 8.2** :
    *   Les fonctions `utf8_encode` et `utf8_decode` sont dépréciées (utiliser `mbstring` ou `intl`).
    *   L'interpolation de chaînes `${var}` est dépréciée (utiliser `{$var}`).
    *   `callable` dans les propriétés typées n'est pas supporté (car context-dependent).
*   **Comparaisons** : Les changements de comportement dans les comparaisons (ex: `0 == "foo"` est `false` depuis PHP 8.0, c'était `true` avant).
*   **Priorité des opérateurs** : Des changements subtils ont eu lieu (ex: concaténation vs addition).
*   **Performance** : PHP 8 introduit le JIT (Just In Time) Compiler. Bien que peu impactant pour une app web standard (I/O bound), il est crucial pour les tâches CPU-intensive.

## Ressources
*   [PHP 8.0 Released](https://www.php.net/releases/8.0/fr.php)
*   [PHP 8.1 Released](https://www.php.net/releases/8.1/fr.php)
*   [PHP 8.2 Released](https://www.php.net/releases/8.2/fr.php)
*   [Guide de migration PHP](https://www.php.net/manual/fr/appendices.php)
