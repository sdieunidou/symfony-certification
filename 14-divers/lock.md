# Composant Lock

## Concept clé
Dans un environnement concurrent (plusieurs requêtes web simultanées, plusieurs workers), il faut parfois empêcher que deux processus accèdent à la même ressource en même temps (Race Condition).
Le composant **Lock** fournit des verrous (Mutex/Sémaphores).

## Utilisation

```php
use Symfony\Component\Lock\LockFactory;

public function generateReport(LockFactory $factory): void
{
    // Créer un verrou nommé
    $lock = $factory->createLock('report_generation');

    // Tenter d'acquérir le verrou (Non bloquant par défaut)
    if (!$lock->acquire()) {
        throw new \Exception('Un rapport est déjà en cours de génération.');
    }

    try {
        // Section critique (Traitement lourd)
        sleep(10);
    } finally {
        // Toujours libérer dans un finally
        $lock->release();
    }
}
```

## Bloquant vs Non-Bloquant
*   `acquire(false)` (Défaut) : Retourne `false` immédiatement si verrouillé.
*   `acquire(true)` : Attend indéfiniment (ou jusqu'au timeout) que le verrou se libère.

## Stores (Persistance)
Le verrou doit être partagé entre les processus.
*   `FlockStore` : Fichier système (local à une machine).
*   `RedisStore`, `MemcachedStore`, `PdoStore` : Distribué (pour les architectures multi-serveurs).
*   `SemaphoreStore` : Sémaphores système PHP.

## 🧠 Concepts Clés
1.  **TTL** : On peut définir une durée de vie (`ttl`) au verrou. S'il n'est pas rafraîchi (`refresh()`) avant la fin du TTL, il expire automatiquement. Cela évite de bloquer le système à jamais si un script crashe avant le `release()`.
2.  **Resource** : Le nom du verrou (`report_generation`) est la ressource protégée.

## ⚠️ Points de vigilance (Certification)
*   **Commandes** : Le trait `LockableTrait` permet de verrouiller facilement une commande Console pour éviter qu'elle ne soit lancée en double par le CRON.

## Ressources
*   [Symfony Docs - Lock](https://symfony.com/doc/current/components/lock.html)
