# Gestion des Exceptions et des Erreurs

## Concept clé
PHP gère les erreurs d'exécution via deux mécanismes principaux : les Erreurs traditionnelles (héritées de l'historique procédural) et les Exceptions (orientées objet). Depuis PHP 7, la plupart des erreurs fatales sont devenues des exceptions `Error`, ce qui permet de les attraper (`catch`).

L'interface racine est `Throwable`, dont héritent `Exception` (pour les erreurs applicatives) et `Error` (pour les erreurs internes du moteur PHP).

## Application dans Symfony 7.0
Symfony convertit toutes les erreurs PHP (notices, warnings) en exceptions via le composant `ErrorHandler` (surtout en mode `dev`). Cela permet une gestion unifiée. Le framework fournit de nombreuses exceptions spécifiques (`NotFoundHttpException`, `AccessDeniedException`).

## Exemple de code

```php
<?php

function divide(int $a, int $b): float
{
    if ($b === 0) {
        throw new \InvalidArgumentException("Division par zéro impossible.");
    }
    return $a / $b;
}

try {
    $result = divide(10, 0);
} catch (\InvalidArgumentException $e) {
    // Erreur logique métier
    echo "Erreur argument : " . $e->getMessage();
} catch (\DivisionByZeroError $e) {
    // Erreur native PHP (si on n'avait pas fait le check if)
    echo "Erreur native : " . $e->getMessage();
} catch (\Throwable $t) {
    // Attrape tout (Exceptions ET Errors)
    echo "Erreur inattendue : " . $t->getMessage();
} finally {
    echo "Exécuté dans tous les cas.";
}
```

## Points de vigilance (Certification)
*   **`Throwable`** : On ne peut pas implémenter `Throwable` directement dans une classe utilisateur (sauf si elle étend `Exception` ou `Error`). C'est l'interface de base pour le `catch`.
*   **`finally`** : Le bloc `finally` est exécuté après le `try` et le `catch`, même s'il y a un `return` ou un nouveau `throw` dans le `try/catch`.
*   **Hiérarchie** : L'ordre des `catch` est important : du plus spécifique au plus général. Si `catch (\Exception $e)` est avant `catch (\RuntimeException $e)`, le second ne sera jamais atteint.
*   **Erreurs PHP 8** : De nombreuses fonctions internes lancent désormais des `ValueError` ou `TypeError` au lieu de renvoyer `false` ou un warning.

## Ressources
*   [Manuel PHP - Exceptions](https://www.php.net/manual/fr/language.exceptions.php)
*   [Manuel PHP - Throwable](https://www.php.net/manual/fr/class.throwable.php)

