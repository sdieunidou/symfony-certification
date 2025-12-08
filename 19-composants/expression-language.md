# Composant ExpressionLanguage

## Concept clé
Le composant `ExpressionLanguage` fournit un moteur capable de compiler et d'évaluer des expressions. Une expression est une "one-liner" qui retourne une valeur (souvent un booléen, mais pas que).
Il permet d'ajouter de la logique dynamique dans la configuration (ex: règles de validation, sécurité, routing) sans exposer toute la puissance (et les risques) de PHP.

## Installation
```bash
composer require symfony/expression-language
```

## Utilisation de base

L'objet principal est `Symfony\Component\ExpressionLanguage\ExpressionLanguage`.

```php
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

$el = new ExpressionLanguage();

// 1. Evaluate (Exécution directe)
var_dump($el->evaluate('1 + 2')); // 3

// 2. Compile (Transformation en code PHP pour mise en cache)
var_dump($el->compile('1 + 2')); // "(1 + 2)"
```

### Variables
On peut passer des variables à l'expression.

```php
class Apple { public string $variety = 'Honeycrisp'; }
$apple = new Apple();

// Accès aux propriétés publiques et getters
$result = $el->evaluate(
    'fruit.variety == "Honeycrisp"', 
    ['fruit' => $apple]
); // true
```

## Syntaxe
La syntaxe est proche de Twig ou JS.
*   Littéraux : `"hello"`, `123`, `true`, `null`.
*   Opérateurs : `+`, `-`, `*`, `/`, `%`, `**` (puissance).
*   Comparaison : `==`, `===`, `!=`, `<`, `>`, `<=`, `>=`.
*   Logique : `and` (`&&`), `or` (`||`), `not` (`!`).
*   Chaînes : `~` (concaténation), `matches` (regex), `in` (tableau), `starts with`, `ends with`, `contains`.
*   Tableaux/Objets : `[1, 2]`, `{key: "value"}`, `user.name`.
*   Ternaire : `condition ? true : false`.
*   Null Coalescing : `foo ?? "bar"`.

Exemples métiers :
```text
user.getGroup() in ['good_customers', 'collaborator']
product.stock < 15 and product.isAvailable
```

## Parsing et Linting
Avant d'évaluer, on peut vérifier la syntaxe.

```php
try {
    $el->lint('1 + a', ['a']); // Vérifie si 'a' est valide
} catch (SyntaxError $e) {
    // Erreur de syntaxe
}

// AST (Arbre syntaxique)
$nodes = $el->parse('1 + 2', [])->getNodes();
```

## Extension (Fonctions Custom)
On peut ajouter ses propres fonctions au langage.

```php
$el->register('lowercase', 
    // Compiler : génère le code PHP
    function ($str) {
        return sprintf('strtolower(%s)', $str);
    },
    // Evaluator : exécute la logique
    function ($arguments, $str) {
        return strtolower($str);
    }
);

echo $el->evaluate('lowercase("HELLO")'); // "hello"
```

### Providers
Pour réutiliser des fonctions, on crée un `ExpressionFunctionProviderInterface`.

```php
class StringProvider implements ExpressionFunctionProviderInterface {
    public function getFunctions(): array {
        return [
            new ExpressionFunction('uppercase', ...),
        ];
    }
}

$el->registerProvider(new StringProvider());
```

## Caching
La compilation (`compile()`) est légère, mais le parsing (`parse()`) est coûteux.
`ExpressionLanguage` peut utiliser un Adapter de Cache (PSR-6) pour stocker les expressions parsées.

```php
use Symfony\Component\Cache\Adapter\PhpArrayAdapter;

$cache = new PhpArrayAdapter();
$el = new ExpressionLanguage($cache);
```

## AST Dumping
Pour le debug ou l'analyse statique, on peut dumper l'arbre syntaxique.

```php
$ast = $el->parse('1 + 2', [])->getNodes();
dump($ast->dump()); // Représentation textuelle des nœuds
```

## Fonctionnement Interne

### Architecture
*   **Lexer** : Tokenise la chaîne (`1 + 2` -> `NUMBER`, `OPERATOR`, `NUMBER`).
*   **Parser** : Convertit les tokens en AST (Nodes).
*   **Compiler** : Transforme l'AST en code PHP natif (`return 1 + 2;`).
*   **Evaluator** : Exécute l'AST à la volée (plus lent mais dynamique).

### Le Flux
1.  **Compile** : Si utilisé en cache (ex: Routing, Security), l'expression est compilée en PHP pur.
2.  **Evaluate** : Sinon, l'évaluateur parcourt l'arbre de nœuds et résout les variables/fonctions au runtime.

## Sécurité
Contrairement à `eval()` en PHP, `ExpressionLanguage` est sandboxé.
*   Accès limité aux variables passées explicitement.
*   Pas d'accès aux fonctions PHP natives (sauf si enregistrées manuellement).
*   Pas d'accès au système de fichiers ou aux superglobales.

## Ressources
*   [Symfony Docs - ExpressionLanguage](https://symfony.com/doc/current/components/expression_language.html)
*   [Syntaxe Complète](https://symfony.com/doc/current/reference/formats/expression_language.html)

