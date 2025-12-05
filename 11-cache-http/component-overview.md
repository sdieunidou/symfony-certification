# Composant Cache : Vue d'ensemble

## Concept clé
Le composant Cache de Symfony fournit une abstraction puissante (basée sur PSR-6 et PSR-16) pour stocker des données temporaires. Il supporte de nombreux backends (Redis, Memcached, Filesystem, APCu, Base de données).

## Standards Supportés
1.  **Cache Contracts** (`Symfony\Contracts\Cache\ItemInterface`) : L'approche recommandée par Symfony (plus simple, callback pour recompute automatique).
2.  **PSR-6** (Generic Cache) : Standard complexe, orienté "Item Pool".
3.  **PSR-16** (Simple Cache) : Standard simplifié (`get`, `set`).

## Configuration FrameworkBundle (`cache.yaml`)
Deux pools principaux sont toujours configurés par défaut :
*   **`cache.app`** : Pour vos données applicatives.
*   **`cache.system`** : Pour le framework (Annotations, Serializer, Validation). Optimisé pour être rapide (souvent stocké en fichier PHP ou APCu).

```yaml
framework:
    cache:
        # Choisir l'adapter (le moteur de stockage)
        app: cache.adapter.filesystem
        system: cache.adapter.system

        # Configuration globale des providers (connexions)
        default_redis_provider: 'redis://localhost'
        directory: '%kernel.cache_dir%/pools' # Pour filesystem
```

## Les Adapters Disponibles
Ce sont les "drivers" techniques.
*   `cache.adapter.apcu` : Très rapide (mémoire partagée PHP), mais local au serveur (pas distribué).
*   `cache.adapter.array` : Mémoire du processus PHP (perdu à la fin de la requête). Utile pour les tests.
*   `cache.adapter.filesystem` : Stockage sur disque. Fiable mais moins performant que la RAM.
*   `cache.adapter.redis` : Serveur Redis (rapide, distribué, persistant).
*   `cache.adapter.memcached` : Serveur Memcached.
*   `cache.adapter.doctrine_dbal` / `cache.adapter.pdo` : Stockage en base de données SQL.
*   `cache.adapter.system` : Spécial. Choisit intelligemment entre APCu et PHP Files selon ce qui est disponible. Recommandé pour `cache.system`.

## Utilisation (Cache Contracts)
C'est la méthode la plus courante (Stampede protection incluse).

```php
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

public function index(CacheInterface $cache): Response // Injecte cache.app
{
    $value = $cache->get('my_cache_key', function (ItemInterface $item) {
        $item->expiresAfter(3600);
        
        // Logique lourde exécutée UNIQUEMENT si miss ou expiration
        return $this->heavyCalculation();
    });

    return new Response($value);
}
```

## ⚠️ Points de vigilance (Certification)
*   **Cache System** : Ne changez pas l'adapter de `cache.system` à la légère. `cache.adapter.system` est optimisé pour les fichiers PHP compilés (OPcache). Mettre Redis ici peut ralentir le démarrage de l'app.
*   **DSN** : Depuis Symfony 7.1, le PDO Adapter supporte aussi un DSN comme provider.

## Ressources
*   [Symfony Docs - Cache Component](https://symfony.com/doc/current/cache.html)
