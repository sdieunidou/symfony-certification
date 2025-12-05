# Principe ACID

## Concept Clé
ACID est un acronyme définissant les propriétés garantissant la fiabilité des transactions dans une base de données.
Essentiel pour la cohérence des données, surtout en forte concurrence.

## A - Atomicité (Atomicity)
**"Tout ou rien"**.
Une transaction est une unité indivisible. Si une partie échoue, toute la transaction est annulée (Rollback).
*Symfony* : `$em->beginTransaction()` ... `$em->commit()` ou `$em->rollback()`.

## C - Cohérence (Consistency)
La transaction doit amener la base d'un état valide à un autre état valide, respectant toutes les contraintes (Clés étrangères, Unique, Check).

## I - Isolation
L'exécution simultanée de transactions doit donner le même résultat que si elles étaient exécutées séquentiellement.
C'est ici que se jouent les problèmes de concurrence (Lecture sale, lecture non répétable, lecture fantôme).
Les bases de données offrent différents niveaux d'isolation (Read Uncommitted, Read Committed, Repeatable Read, Serializable).

## D - Durabilité (Durability)
Une fois validée (Commit), la transaction est persistée de manière permanente, même en cas de panne de courant juste après.

## Application Symfony
Doctrine ORM encapsule ces principes via l'`EntityManager`.
La méthode `flush()` exécute toutes les modifications SQL dans une seule transaction atomique par défaut.

```php
// Garantit l'Atomicité
$em->wrapInTransaction(function($em) use ($user, $order) {
    $em->persist($user);
    $em->persist($order);
    // Si une exception survient ici, $user ne sera pas créé non plus.
});
```

