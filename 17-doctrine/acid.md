# Principe ACID (Transactions)

## Concept Clé
ACID est un ensemble de propriétés qui garantissent la fiabilité des transactions de base de données.
Une transaction est une séquence d'opérations traitée comme une unité indivisible.

## 1. Atomicité (Atomicity)
**"Tout ou rien".**
Si une partie de la transaction échoue (ex: contrainte violée, coupure réseau), **aucune** modification n'est appliquée. La base de données revient à l'état initial (Rollback).
*Exemple* : Virement bancaire. Débiter A et Créditer B doit être atomique. Si Créditer B échoue, Débiter A doit être annulé.

## 2. Cohérence (Consistency)
La transaction doit faire passer la base d'un état valide à un autre état valide.
Toutes les règles d'intégrité (clés étrangères, contraintes `CHECK`, `UNIQUE`, Types de données) doivent être respectées à la fin de la transaction.

## 3. Isolation
L'exécution simultanée de plusieurs transactions doit donner le même résultat que si elles étaient exécutées l'une après l'autre (séquentiellement).
Les transactions ne doivent pas se voir les unes les autres avant d'être validées (selon le niveau d'isolation).
*Niveaux d'isolation SQL* :
*   `READ UNCOMMITTED` (Danger : Dirty Read)
*   `READ COMMITTED` (Standard : PostgreSQL/Oracle)
*   `REPEATABLE READ` (Standard : MySQL)
*   `SERIALIZABLE` (Le plus strict, mais lent)

## 4. Durabilité (Durability)
Une fois la transaction validée (`COMMIT`), les modifications sont permanentes, même en cas de panne système (coupure de courant, crash disque). Cela est garanti par les logs de transaction (Write-Ahead Logging).

## Application dans Symfony (Doctrine)

Doctrine offre plusieurs façons de gérer les transactions.

### 1. `transactional(callable $fn)` (Recommandé pour cas simples)
Ouvre une transaction, exécute le code, et fait un **flush automatique** à la fin.
Si une exception survient, le rollback est automatique.

```php
$em->transactional(function (EntityManagerInterface $em) use ($user) {
    $em->persist($user);
    // Pas besoin de $em->flush(), c'est fait automatiquement à la fin
});
```

### 2. `wrapInTransaction(callable $fn)` (Contrôle avancé)
Ouvre une transaction, mais **ne fait PAS de flush automatique**. C'est à vous de décider quand flusher.
Utile pour faire plusieurs `flush()` dans une même transaction (ex: obtenir un ID généré pour l'utiliser ensuite).

```php
$em->wrapInTransaction(function (EntityManagerInterface $em) use ($order) {
    $em->persist($order);
    $em->flush(); // Flush 1 : On obtient l'ID de la commande

    foreach ($order->getItems() as $item) {
        $em->persist($item);
    }
    $em->flush(); // Flush 2 : On sauvegarde les items
    
    // Le COMMIT global se fait ici, si tout est OK.
});
```

### 3. Bas niveau : `beginTransaction`, `commit`, `rollback`
À réserver aux cas spécifiques (SQL brut, scripts complexes).

```php
$em->beginTransaction();
try {
    // ...
    $em->flush();
    $em->commit();
} catch (\Throwable $e) {
    $em->rollback();
    throw $e;
}
```

## Ressources
*   [Wikipedia - ACID](https://fr.wikipedia.org/wiki/ACID_(informatique))
*   [Doctrine Transactions](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/transactions-and-concurrency.html)
