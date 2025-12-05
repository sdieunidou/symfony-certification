# DBAL (Database Abstraction Layer)

## ORM vs DBAL
*   **ORM** (Object Relational Mapper) : Couche haute. Manipule des Entités, UnitOfWork, DQL.
*   **DBAL** (Database Abstraction Layer) : Couche basse. Manipule du SQL, des tableaux, des types. C'est une surcouche à PDO.

Parfois, l'ORM est trop lourd ou limitant (Batch insert massif, requêtes analytiques complexes, Window functions). On peut alors descendre au niveau DBAL.

## Utilisation

On récupère la connexion via l'EntityManager ou injection directe (`Connection $connection`).

```php
use Doctrine\DBAL\Connection;

public function report(Connection $connection): array
{
    $sql = 'SELECT count(*), status FROM users GROUP BY status';
    
    // executeQuery pour les SELECT (retourne un ResultSet)
    $result = $connection->executeQuery($sql);
    return $result->fetchAllAssociative();
    
    // executeStatement pour INSERT/UPDATE/DELETE (retourne int, nombre de lignes affectées)
    // $connection->executeStatement('DELETE FROM ...');
}
```

## Transactions
DBAL gère les transactions de manière atomique.

```php
$connection->beginTransaction();
try {
    $connection->executeStatement(...);
    $connection->executeStatement(...);
    $connection->commit();
} catch (\Exception $e) {
    $connection->rollBack();
    throw $e;
}
```
Ou plus simple :
```php
$connection->transactional(function($conn) {
    // Tout ce qui est ici est dans une transaction
});
```

## Types DBAL Personnalisés
Si vous voulez mapper un type SQL spécifique (ex: `geometry`) vers un objet PHP, vous devez créer un `Type` DBAL.
Il faut ensuite l'enregistrer dans `doctrine.yaml` (`dbal: types: ...`).

## Ressources
*   [Doctrine Docs - DBAL](https://www.doctrine-project.org/projects/doctrine-dbal/en/current/index.html)
