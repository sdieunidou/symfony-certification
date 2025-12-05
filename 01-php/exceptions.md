# Gestion des Exceptions et des Erreurs

## Concept clé
Les exceptions en PHP sont des objets permettant de signaler des conditions d'erreur ou des situations inattendues.
L'interface racine est **`Throwable`**. Elle a deux branches principales :
1.  **`Exception`** : Erreurs que l'application peut gérer.
    *   **`LogicException`** : Erreurs de programmation (bug) qui auraient dû être évitées par le développeur (ex: `InvalidArgumentException` pour un type incorrect, `DomainException`).
    *   **`RuntimeException`** : Erreurs survenant pendant l'exécution, dépendant de l'environnement ou des données (ex: `OutOfBoundsException`, `PDOException`, fichier introuvable).
2.  **`Error`** : Erreurs internes du moteur PHP (ex: `TypeError`, `ParseError`, `DivisionByZeroError`). Depuis PHP 7, elles peuvent être attrapées.

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
    ├── LogicException (Erreur développeur - Code à corriger)
    │   ├── InvalidArgumentException
    │   ├── BadMethodCallException
    │   └── DomainException
    └── RuntimeException (Erreur contexte - À gérer au runtime)
        ├── OutOfBoundsException
        ├── OverflowException
        └── PDOException
```

## Syntaxe et Fonctionnalités Modernes (PHP 8+)

```php
<?php

// 1. Création d'une exception personnalisée
// Bonne pratique : Suffixe "Exception" et hériter d'une classe SPL précise si possible
class UserNotActiveException extends \RuntimeException {}

function processUser(array $user): void
{
    // 2. throw est une expression (PHP 8.0)
    // Permet de lancer une exception dans une affectation ternaire ou coalescente
    $status = $user['status'] ?? throw new \InvalidArgumentException("Status manquant");

    // 3. match avec throw
    match ($status) {
        'active' => true,
        'banned' => throw new UserNotActiveException("User banni"),
        default => null,
    };
}

try {
    processUser(['status' => 'banned']);
} catch (UserNotActiveException $e) {
    // 4. Catch spécifique (Métier)
    echo "Compte inactif : " . $e->getMessage();
} catch (\InvalidArgumentException|\ValueError $e) {
    // 5. Catch multiple (PHP 7.1+) avec le pipe '|'
    echo "Données invalides.";
} catch (\Throwable $t) {
    // 6. Catch générique (Filet de sécurité)
    // Attrape Exceptions ET Errors.
    // Recommandé uniquement pour logger ou retourner une erreur 500 générique.
    
    // Exception Chaining (Chaînage)
    // Le 3ème argument permet de garder la trace de l'exception précédente
    throw new \RuntimeException("Échec critique", 0, $t);
} finally {
    // 7. Finally : Exécuté dans tous les cas (succès ou erreur)
    // Nettoyage de ressources
    echo "Cleanup done.";
}
```

## 🧠 Concepts Clés et Bonnes Pratiques

1.  **Ne jamais étouffer une exception** : Un bloc `catch` vide est une très mauvaise pratique. Si vous attrapez une exception, c'est pour la gérer (log, fallback, message utilisateur) ou la relancer (`throw $e`).
2.  **Exception Chaining** : Utilisez toujours l'argument `$previous` lors du relancement d'une exception pour ne pas perdre la stack trace originale.
3.  **Préférez les exceptions standards** : Avant de créer `MyCustomInvalidArgumentException`, vérifiez si `InvalidArgumentException` (SPL) ne suffit pas.
4.  **Loggez avant de traiter** : Si vous capturez une exception bloquante, assurez-vous qu'elle soit loggée (via Monolog) pour le débogage futur.

## ⚠️ Points de vigilance (Certification)
*   **Ordre des catch** : Du plus spécifique au plus général (`LogicException` avant `Exception`). L'inverse rend le code mort.
*   **Finally vs Return** : Le bloc `finally` est exécuté **avant** le retour effectif de la fonction. Un `return` dans le `finally` écrasera le `return` du `try`.
*   **Set Exception Handler** : `set_exception_handler()` est le mécanisme natif PHP.
*   **Type Safety** : Les erreurs de typage lancent des `TypeError`.

## Ressources
*   [Manuel PHP - Exceptions](https://www.php.net/manual/fr/language.exceptions.php)
