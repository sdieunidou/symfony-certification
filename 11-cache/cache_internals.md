# Cache : Fonctionnement Interne

## Concept clé
Le composant Cache de Symfony implémente les standards **PSR-6** (Cache générique) et **PSR-16** (Simple Cache). Il fournit une abstraction puissante pour stocker des données coûteuses à calculer.

## Architecture et Classes Clés

### 1. Adapter (Le Stockage)
Les **Adapters** sont responsables de la persistance réelle des données.
*   `FilesystemAdapter` : Stocke sur le disque (par défaut).
*   `RedisAdapter`, `MemcachedAdapter` : Stocke en mémoire distribuée.
*   `ArrayAdapter` : Stocke en mémoire PHP (disparaît fin de requête, utile pour les tests).
*   `ChainAdapter` : Combine plusieurs adapters (ex: Array puis Redis puis File).

### 2. CacheItem (`CacheItemInterface`)
Représente une unité de donnée en cache (PSR-6).
*   Possède une **Key** (clé unique).
*   Possède une **Value** (la donnée, qui doit être sérialisable).
*   Possède une **Metadata** (date d'expiration, tags).
*   Méthodes : `isHit()`, `get()`, `set()`, `expiresAfter()`.

### 3. CachePool (`CacheItemPoolInterface`)
C'est le service que vous injectez (le gestionnaire). Il permet de récupérer des `CacheItem`.
*   Méthode clé : `getItem($key)`.

### 4. Marshaller
Responsable de la sérialisation/désérialisation des données avant stockage.
*   Par défaut : `DefaultMarshaller` (utilise `serialize()` / `unserialize()`).

## Le Flux PSR-6

```php
// 1. Récupérer le pool (injecté)
public function __construct(private CacheItemPoolInterface $myCachePool) {}

public function getStats() {
    // 2. Demander un item via sa clé
    $item = $this->myCachePool->getItem('stats.daily');

    // 3. Vérifier s'il est déjà en cache (Hit)
    if (!$item->isHit()) {
        // 4. Miss : Recalculer la donnée
        $data = $this->heavyCalculation();

        // 5. Mettre à jour l'item
        $item->set($data);
        $item->expiresAfter(3600);

        // 6. Sauvegarder dans le pool
        $this->myCachePool->save($item);
    }

    // 7. Retourner la valeur
    return $item->get();
}
```

## Fonctionnalités Avancées

### Cache Contracts (Callback)
Symfony propose une surcouche plus simple que PSR-6, qui gère le "Get or Compute" atomiquement (évite le Stampede/Race conditions).
```php
$value = $cache->get('my_key', function (ItemInterface $item) {
    $item->expiresAfter(3600);
    return $this->compute();
});
```

### Tags
Permet de grouper des items pour les invalider ensemble.
*   `$item->tag(['user_1', 'blog']);`
*   `$cache->invalidateTags(['blog']);` // Supprime tout ce qui est tagué 'blog'.

## ⚠️ Points de vigilance (Certification)
*   **Sérialisation** : Tout ce que vous mettez en cache doit être sérialisable. Attention aux ressources (connexions DB) et aux Closures.
*   **Namespace** : Les pools sont isolés. Vider le pool `cache.app` ne vide pas `cache.system`.

## Ressources
*   [Symfony Docs - Cache Component](https://symfony.com/doc/current/components/cache.html)
*   [PSR-6 vs PSR-16](https://www.php-fig.org/psr/psr-6/)
