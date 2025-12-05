# Composant Lock

## Concept clé
Gérer les verrous (Locks) pour empêcher l'exécution concurrente d'une ressource ou d'une commande (Mutex, Semaphore).
Exemple : Empêcher qu'un Cron job ne se lance deux fois en même temps.

## Application dans Symfony 7.0
Utilise un "Store" (Flock, Redis, Database, Memcached) pour persister le verrou.

```php
use Symfony\Component\Lock\LockFactory;

public function export(LockFactory $factory): void
{
    $lock = $factory->createLock('pdf_export');

    if (!$lock->acquire()) {
        // Déjà en cours d'exécution
        return;
    }

    try {
        // ... traitement long ...
    } finally {
        $lock->release();
    }
}
```

## Points de vigilance (Certification)
*   **Blocking** : `acquire(true)` est bloquant (attend que le verrou se libère). `acquire()` retourne false immédiatement si verrouillé.
*   **TTL** : Les verrous peuvent avoir une durée de vie (expiration automatique) pour éviter les deadlocks si le script crashe.

## Ressources
*   [Symfony Docs - Lock](https://symfony.com/doc/current/components/lock.html)

