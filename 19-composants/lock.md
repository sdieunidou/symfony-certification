# Composant Lock

## Concept clé
Dans un environnement concurrent (plusieurs requêtes web simultanées, plusieurs workers), il faut parfois empêcher que deux processus accèdent à la même ressource en même temps (Race Condition).
Le composant **Lock** fournit des verrous (Mutex/Sémaphores) pour garantir un accès exclusif à une ressource partagée.

## Installation
```bash
composer require symfony/lock
```

## Utilisation
L'utilisation typique implique un `Store` (persistance du verrou) et une `LockFactory`.

```php
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\SemaphoreStore;

$store = new SemaphoreStore();
$factory = new LockFactory($store);

// 1. Créer un verrou pour une ressource nommée "pdf-creation"
$lock = $factory->createLock('pdf-creation');

// 2. Acquérir le verrou
if ($lock->acquire()) {
    try {
        // Section critique (Traitement lourd)
        // ...
    } finally {
        // 3. Toujours libérer dans un finally
        $lock->release();
    }
} else {
    // Impossible d'acquérir le verrou (déjà pris)
}
```

> **Note** : `createLock()` retourne toujours une nouvelle instance de `Lock`. Si le verrou n'est pas explicitement relâché, il l'est automatiquement à la destruction de l'objet (sauf si autoRelease est désactivé).

## Types d'Acquisition

### Non-Bloquant (Par défaut)
`acquire(false)` ou simplement `acquire()` retourne immédiatement `false` si le verrou est déjà pris.

### Bloquant
`acquire(true)` met le script en pause (attend indéfiniment) jusqu'à ce que le verrou se libère.
*Attention : Nécessite un Store compatible `BlockingStoreInterface` (ex: Flock, Semaphore, Redis).*

### Expiring Locks (TTL)
Pour éviter qu'un processus qui crashe ne bloque la ressource indéfiniment, on définit un **Time To Live (TTL)**.

```php
// Expire automatiquement après 30 secondes
$lock = $factory->createLock('invoice-generation', 30.0);

if ($lock->acquire()) {
    try {
        // Traitement...
        // Si le traitement est long, penser à rafraîchir le TTL
        $lock->refresh(); 
    } finally {
        $lock->release();
    }
}
```

## Stores (Stockage)
Le choix du store dépend de l'architecture :

1.  **Local (Mono-serveur)**
    *   `FlockStore` : Système de fichiers (fiable, simple).
    *   `SemaphoreStore` : Sémaphores Kernel (très rapide, mais attention au nettoyage IPC).

2.  **Distribué (Multi-serveurs)**
    *   `RedisStore` : Très performant, mais attention à la perte de données (locks en RAM) en cas de redémarrage.
    *   `MemcachedStore` : Similaire à Redis.
    *   `PdoStore` : Stockage en base de données (ACID). Plus lent mais très fiable. Requiert une table dédiée (`lock_keys`).
    *   `MongoDbStore`, `ZookeeperStore`, `DynamoDbStore`.

3.  **CombinedStore**
    *   Permet d'utiliser plusieurs stores en stratégie de redondance (Attention : la fiabilité est celle du maillon le plus faible pour la cohérence).

## Fonctionnalités Avancées

### Shared Locks (Verrous Partagés)
Permet à plusieurs lecteurs d'accéder à la ressource simultanément, mais bloque les écrivains.
*   `acquireRead()` : Acquiert un verrou en lecture (partagé).
*   `acquire()` : Acquiert un verrou en écriture (exclusif).

### Serialisation (Cross-Process)
Un verrou peut être acquis dans un processus et utilisé dans un autre (ex: job démarré en Web et fini en Worker).
Il faut sérialiser la `Key` du verrou.

```php
use Symfony\Component\Lock\Key;

// Process 1
$key = new Key('article.123');
$lock = $factory->createLockFromKey($key);
$lock->acquire();
// Envoyer $key (serialisé) au Process 2...

// Process 2
$lock = $factory->createLockFromKey($unserializedKey);
$lock->release();
```

## ⚠️ Points de vigilance (Certification)
1.  **Commandes Console** : Le trait `LockableTrait` intégré aux commandes Symfony utilise ce composant pour empêcher l'exécution multiple (`$this->lock()`).
2.  **Fiabilité** : Les verrous distants (`Redis`, `Memcached`) peuvent être perdus si le service redémarre. Pour une fiabilité absolue, préférer `PdoStore` ou `Zookeeper`.
3.  **Propriétaire** : Un verrou est lié à son propriétaire (Owner). Seul le propriétaire qui l'a acquis peut le relâcher ou le rafraîchir.

## Ressources
*   [Symfony Docs - Lock](https://symfony.com/doc/current/components/lock.html)
