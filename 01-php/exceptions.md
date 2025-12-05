# Gestion des Exceptions et des Erreurs

## Concept clé
PHP possède un modèle robuste de gestion des erreurs orienté objet.
L'interface racine est **`Throwable`**. Elle a deux branches principales :
1.  **`Exception`** : Erreurs logiques ou d'exécution que l'application peut raisonnablement gérer (ex: `ValidationException`).
2.  **`Error`** : Erreurs internes du moteur PHP (ex: `TypeError`, `ParseError`, `OutOfMemoryError`). Depuis PHP 7, elles peuvent être attrapées au lieu de faire planter le script fatalement.

## Application dans Symfony 7.0
Symfony convertit toutes les erreurs PHP (notices, warnings, deprecations) en exceptions grâce à son composant **ErrorHandler**.
*   **`NotFoundHttpException`** (404)
*   **`AccessDeniedException`** (403)
*   **Kernel Events** : L'événement `kernel.exception` permet d'intercepter toute exception non gérée pour afficher une page d'erreur personnalisée ou logger l'incident.

## Hiérarchie Throwable

```text
Throwable
├── Error (Erreurs internes PHP)
│   ├── TypeError
│   ├── ValueError (PHP 8.0)
│   ├── ArithmeticError
│   │   └── DivisionByZeroError
│   ├── CompileError
│   └── UnhandledMatchError (PHP 8.0)
└── Exception (Erreurs utilisateur/librairie)
    ├── LogicException (Erreurs de code/développeur)
    │   ├── InvalidArgumentException
    │   └── DomainException
    └── RuntimeException (Erreurs d'exécution/environnement)
        ├── OutOfBoundsException
        ├── OverflowException
        └── PDOException
```

## Exemple de code Complet

```php
<?php

// Création d'une exception personnalisée (Bonne pratique : suffixe Exception)
class UserNotActiveException extends \RuntimeException {}

function processUser(array $user): void
{
    // PHP 8.0 : throw est une expression
    $status = $user['status'] ?? throw new \InvalidArgumentException("Status manquant");

    // PHP 8.0 : Match expression qui peut throw
    match ($status) {
        'active' => true,
        'banned' => throw new UserNotActiveException("User banni"),
        default => null,
    };
}

try {
    processUser(['status' => 'banned']);
} catch (UserNotActiveException $e) {
    // 1. Catch spécifique (Métier)
    // Logique de récupération : rediriger vers page de support
    echo "Compte inactif : " . $e->getMessage();
} catch (\InvalidArgumentException|\ValueError $e) {
    // 2. Catch multiple (PHP 7.1+)
    echo "Données invalides.";
} catch (\Throwable $t) {
    // 3. Catch générique (Filet de sécurité ultime)
    // Attrape Exceptions ET Errors (ex: TypeError)
    // Recommandé pour les loggers ou les points d'entrée globaux
    echo "Erreur critique : " . $t->getMessage();
    
    // Exception Chaining (Chaînage)
    // On relance une nouvelle exception en gardant la trace de la précédente ($t)
    throw new \RuntimeException("Échec du traitement", 0, $t);
} finally {
    // 4. Exécuté DANS TOUS LES CAS (succès, erreur attrapée ou non)
    // Utile pour fermer des ressources (fichiers, connexions)
    echo "Cleanup done.";
}
```

## 🧠 Concepts Clés
1.  **Throwable** : L'interface parente de tout ce qui peut être lancé (`throw`). On ne peut pas l'implémenter directement dans une classe utilisateur (il faut étendre `Exception`).
2.  **Exception Chaining** : Le 3ème argument du constructeur d'Exception (`$previous`) permet de créer une chaîne de causalité. Très utile pour le débogage ("Cette DatabaseException a causé cette UserCreationException").
3.  **Nouveautés PHP 8** :
    *   `ValueError` : Lancée lorsqu'un argument a le bon type mais une valeur incorrecte (ex: `json_decode` avec profondeur négative).
    *   `UnhandledMatchError` : Si un `match` n'a pas de correspondance et pas de `default`.
    *   `throw` comme expression : Permet `return $x ?? throw new Ex();`.

## ⚠️ Points de vigilance (Certification)
*   **Ordre des catch** : Toujours du plus spécifique au plus général. Si `catch (Exception $e)` est placé avant `catch (RuntimeException $e)`, le second est **code mort** (ne sera jamais atteint).
*   **Finally et Return** : Si un bloc `try` contient un `return`, le bloc `finally` est exécuté **avant** que la valeur ne soit réellement retournée. Si `finally` contient aussi un `return`, il écrase celui du `try` (Comportement piégeux !).
*   **Set Exception Handler** : `set_exception_handler()` définit le gestionnaire par défaut pour les exceptions non attrapées. Symfony surcharge cela.
*   **Type Safety** : Depuis PHP 7/8, les erreurs de type (`TypeError`) ne sont plus silencieuses. C'est un changement majeur par rapport à PHP 5.

## Ressources
*   [Manuel PHP - Exceptions](https://www.php.net/manual/fr/language.exceptions.php)
*   [Manuel PHP - Throwable](https://www.php.net/manual/fr/class.throwable.php)
*   [Symfony - Error Handling](https://symfony.com/doc/current/controller/error_pages.html)
