# Fonctions anonymes et Fermetures (Closures)

## Concept clé
*   **Fonction anonyme** : Une fonction sans nom, souvent utilisée comme callback.
*   **Closure** : En PHP, les fonctions anonymes sont implémentées via la classe `Closure`. Elles peuvent "capturer" des variables de la portée parente grâce au mot-clé `use`.
*   **Arrow Functions (PHP 7.4+)** : Syntaxe courte pour les fonctions anonymes simples (`fn() => ...`), avec capture automatique des variables par valeur.

## Application dans Symfony 7.0
Très utilisées dans :
*   Les écouteurs d'événements (`EventListener`).
*   Le composant Form (options `choice_label`, validations callback).
*   La configuration des routes (rarement, mais possible).
*   Les fonctions de collection (Array methods).

## Exemple de code

```php
<?php

$factor = 2;

// Fonction anonyme classique
$multiplier = function ($number) use ($factor) {
    return $number * $factor;
};

// Arrow function (capture automatique de $factor)
$multiplierShort = fn($number) => $number * $factor;

echo $multiplier(10); // 20
echo $multiplierShort(10); // 20

// First-class Callables (PHP 8.1)
$callback = $this->myMethod(...); 
```

## Points de vigilance (Certification)
*   **`use`** : Pour les fonctions anonymes classiques, les variables externes doivent être explicitement importées avec `use`.
*   **Passage par référence** : Pour modifier une variable externe dans une closure, il faut la passer par référence : `use (&$variable)`.
*   **`$this`** : Dans une fonction anonyme déclarée dans une méthode de classe, `$this` est automatiquement accessible.
*   **Arrow functions** : Elles ne peuvent contenir qu'une seule expression (le `return` est implicite). Elles capturent les variables par **valeur** (lecture seule). Elles ne peuvent pas modifier les variables de la portée parente.
*   **Classe Closure** : Savoir que `Closure::bind()` ou `Closure::bindTo()` permet de changer le contexte (`$this`) et la portée d'une closure (utile pour faire de la "magie" ou accéder à des propriétés privées dans des tests).

## Ressources
*   [Manuel PHP - Fonctions anonymes](https://www.php.net/manual/fr/functions.anonymous.php)
*   [Manuel PHP - Arrow Functions](https://www.php.net/manual/fr/functions.arrow.php)
*   [Manuel PHP - Classe Closure](https://www.php.net/manual/fr/class.closure.php)

