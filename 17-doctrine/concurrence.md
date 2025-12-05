# Concurrence avec Doctrine (Locking & Recettes)

## Concept Clé
Lorsque plusieurs utilisateurs (ou processus) tentent de modifier la même donnée en même temps, on risque des conflits (Lost Update).
Pour résoudre ce problème, on utilise des verrous (Locks) et des stratégies de timeout.

## 1. Niveaux d'isolation & Timeouts
Avant même les verrous, la configuration de la connexion est cruciale.

*   **READ COMMITTED** : Bon compromis performance/sécurité (souvent le défaut).
*   **Timeouts de verrou** : Éviter qu'un processus attende indéfiniment.

```php
// Postgres (dans la transaction courante)
$em->getConnection()->executeStatement("SET LOCAL lock_timeout = '500ms'");

// MySQL
$em->getConnection()->executeStatement("SET innodb_lock_wait_timeout = 1");
```

## 2. Optimistic Locking (Verrouillage Optimiste)
**Stratégie** : "On espère que tout ira bien". On ne bloque pas la base.
Idéal si **beaucoup de lectures**, peu d'écritures concurrentes.

### Mise en place
Ajouter une colonne de version (`@Version`) dans l'entité.

```php
#[ORM\Entity]
class Product
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column]
    #[ORM\Version] // Doctrine gère ce champ automatiquement
    private int $version;
}
```

### Fonctionnement
1.  Doctrine lit l'entité (version 1).
2.  Lors du `flush()`, Doctrine exécute : `UPDATE product SET ..., version = 2 WHERE id = 1 AND version = 1`.
3.  Si la ligne n'est pas trouvée (modifiée par un autre), Doctrine lance une `OptimisticLockException`.

## 3. Pessimistic Locking (Verrouillage Pessimiste)
**Stratégie** : "On bloque tout". On verrouille la ligne dès la lecture.
Pour les processus critiques courts (stock, solde).

### Mise en place
Utiliser le mode de verrouillage lors du `find()`.

```php
use Doctrine\DBAL\LockMode;

$em->wrapInTransaction(function($em) use ($id) {
    // SELECT ... FOR UPDATE
    $product = $em->find(Product::class, $id, LockMode::PESSIMISTIC_WRITE);
    
    $product->decrementStock(1);
    
    $em->flush(); // Le verrou est relâché au commit de la transaction
});
```

### Pattern Worker : SKIP LOCKED
Pour traiter des tâches en parallèle sans conflit : "Donne-moi les tâches que personne d'autre ne traite".

```php
$rows = $em->getConnection()->fetchAllAssociative("
  SELECT id FROM job
  WHERE status = 'pending'
  FOR UPDATE SKIP LOCKED
  LIMIT 100
");
```

## 4. Recettes Types

### R1 : Réservation de Stock (Optimiste + Retry)
Comme les conflits sont rares mais possibles, on utilise l'Optimistic Lock avec une boucle de retry.

```php
$maxTries = 3;
do {
    try {
        $em->wrapInTransaction(function($em) use ($productId) {
            $p = $em->find(Product::class, $productId); // #[Version] activé
            if ($p->getStock() <= 0) throw new Exception('Rupture');
            $p->decrementStock(1);
            $em->flush();
        });
        break; // Succès
    } catch (OptimisticLockException $e) {
        if (--$maxTries <= 0) throw $e;
        usleep(random_int(50000, 150000)); // Backoff
    }
} while (true);
```

### R2 : Mouvement de Solde (Pessimiste)
Forte contention, on veut être sûr de séquentialiser les opérations.

```php
$em->wrapInTransaction(function($em) use ($accountId, $amount) {
    // On verrouille tout de suite
    $account = $em->find(Account::class, $accountId, LockMode::PESSIMISTIC_WRITE);
    $account->addBalance($amount);
    $em->flush();
});
```

## Ressources
*   [Doctrine Locking Support](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/transactions-and-concurrency.html#locking-support)
