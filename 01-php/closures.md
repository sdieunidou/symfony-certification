# Fonctions anonymes et Fermetures (Closures)

## Concept clé
En PHP, les fonctions sont des citoyens de première classe.
*   **Fonction anonyme** : Une fonction déclarée sans nom, souvent assignée à une variable ou passée en argument.
*   **Closure (Fermeture)** : Instance de la classe interne `Closure`. C'est l'objet qui représente la fonction anonyme. Elle a la capacité de **capturer** (enclose) des variables de son contexte parent grâce au mot-clé `use`.
*   **Arrow Functions (Fonctions fléchées)** : Syntaxe plus concis introduite en PHP 7.4 (`fn() => ...`), optimisée pour les opérations simples (getter, mapper).

## Application dans Symfony 7.0
L'architecture moderne de Symfony utilise intensivement les callables :
1.  **EventDispatcher** : Définition de listeners rapides dans `KernelEvents`.
2.  **Form Component** : Options `choice_label`, `group_by`, et contraintes de validation `Callback`.
3.  **Collection handling** : Utilisation avec `array_map`, `array_filter` pour manipuler des données.
4.  **Dependency Injection** : Usines de services (Factories) sous forme de closures.
5.  **Routing** : Définition de contrôleurs sous forme de closure (micro-framework style).

## Exemples de Code

### 1. Closure Classique vs Arrow Function

```php
<?php

$taxRate = 1.2;

// --- Closure Classique ---
// Doit importer explicitement les variables externes avec 'use'
$calculatePrice = function (float $price) use ($taxRate): float {
    return $price * $taxRate;
};

// --- Arrow Function (PHP 7.4+) ---
// Capture AUTOMATIQUE des variables externes par VALEUR
$calculatePriceShort = fn(float $price): float => $price * $taxRate;

// --- Modification du contexte (Reference) ---
$total = 0;
$adder = function (int $val) use (&$total) { // '&' obligatoire pour modifier
    $total += $val;
};
```

### 2. First-class Callables (PHP 8.1)
Syntaxe `...` pour créer une closure à partir d'une méthode existante sans passer par des chaînes de caractères `['ClassName', 'method']`.

```php
class StringProcessor {
    public function normalize(string $s): string { /* ... */ }
}

$processor = new StringProcessor();

// Avant PHP 8.1
$callbackOld = [$processor, 'normalize'];

// PHP 8.1 : Crée une instance de Closure proprement
$callbackNew = $processor->normalize(...);

$data = array_map($callbackNew, [' Test ', 'DATA']);
```

### 3. Binding de Closure (Avancé)
La méthode `Closure::bindTo` ou `bind` permet de changer le `$this` et la portée (scope) d'une closure. C'est la base de la "magie" de certains frameworks de test ou ORM.

```php
class User {
    private string $secret = 'hidden';
}

$viewer = function() {
    return $this->secret;
};

$user = new User();
// $viewer(); // Error: Cannot access private property

// On "bind" la closure à l'objet $user, avec le scope de la classe User
$unlockedViewer = $viewer->bindTo($user, User::class);

echo $unlockedViewer(); // Affiche 'hidden'
```

## 🧠 Concepts Clés
1.  **Objet `Closure`** : Toutes les fonctions anonymes sont des instances de la classe `Closure`.
2.  **Capture de contexte** :
    *   `function() use ($var)` : Copie de la valeur au moment de la définition.
    *   `function() use (&$var)` : Référence à la variable (permet modification).
    *   `fn()` : Copie par valeur (Scope parent entier accessible en lecture).
3.  **`$this` automatique** : Dans une closure définie à l'intérieur d'une méthode de classe, `$this` est automatiquement disponible (sauf si déclarée `static function`).

## ⚠️ Points de vigilance (Certification)
*   **Arrow Functions Limitations** :
    *   Elles ne contiennent qu'une **seule expression**. Pas de bloc `{ ... }`.
    *   Le `return` est implicite.
    *   Impossible de modifier des variables du scope parent (capture par valeur uniquement).
*   **Static Closures** : Si vous déclarez `static function() { ... }` ou `static fn() => ...`, la closure n'aura pas accès à `$this`, même si définie dans une classe. Cela améliore légèrement les performances et évite les fuites de mémoire (cycles) si la closure n'a pas besoin du contexte objet.
*   **Type Hinting** : Une closure peut être typée avec la classe `Closure` ou le pseudo-type `callable`. `Closure` est plus strict (accepte uniquement les fonctions anonymes), `callable` accepte aussi les chaînes `'function_name'` et tableaux `[$obj, 'method']`.

## Ressources
*   [Manuel PHP - Fonctions anonymes](https://www.php.net/manual/fr/functions.anonymous.php)
*   [Manuel PHP - Arrow Functions](https://www.php.net/manual/fr/functions.arrow.php)
*   [Manuel PHP - Classe Closure](https://www.php.net/manual/fr/class.closure.php)
*   [RFC First-class Callable Syntax](https://wiki.php.net/rfc/first_class_callable_syntax)
