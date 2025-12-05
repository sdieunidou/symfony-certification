# Fonctionnalités Avancées du Cache

## 1. Cache Chain (Chaînage)
Combine plusieurs adapters séquentiellement.
*   **Lecture** : Essaie le premier (ex: Array/RAM), puis le second (ex: Redis). Si trouvé dans le 2ème, il remplit le 1er.
*   **Écriture** : Écrit dans tous.

C'est utile pour avoir un cache L1 (très rapide, petite capacité) et L2 (rapide, grande capacité, distribué).

```yaml
framework:
    cache:
        pools:
            my_cache_pool:
                adapters:
                    - cache.adapter.array  # L1 (Mémoire process)
                    - cache.adapter.redis  # L2 (Serveur Redis)
```

## 2. Cache Tags (Étiquettes)
Permet de grouper des items de cache pour les invalider en masse, sans connaître leurs clés exactes.
Nécessite un adapter compatible (`TagAwareCacheInterface`), comme Redis ou Doctrine.

**Configuration :**
```yaml
framework:
    cache:
        pools:
            my_tagged_pool:
                adapter: cache.adapter.redis_tag_aware
                tags: true
```

**Utilisation :**
```php
$pool->get('item_key', function (ItemInterface $item) {
    $item->tag(['product_123', 'category_5']); // Ajout tags
    return 'data';
});

// Invalidation par tag
$pool->invalidateTags(['product_123']); // Supprime tous les items ayant ce tag
```

**Séparation du stockage des tags :**
On peut stocker les données dans Redis mais les tags ailleurs (ou dans un autre pool) si besoin, via la clé `tags: pool_name`.

## 3. Encryption (Chiffrement)
Pour des données sensibles (GDPR, Tokens), on peut chiffrer le contenu du cache. Les clés ne sont PAS chiffrées (attention aux fuites de métadonnées).
Utilise `SodiumMarshaller`.

```yaml
services:
    Symfony\Component\Cache\Marshaller\SodiumMarshaller:
        decorates: cache.default_marshaller
        arguments:
            - ['%env(base64:CACHE_DECRYPTION_KEY)%']
            - '@.inner'
```
Nécessite une clé générée via `sodium_crypto_box_keypair()`.

## Ressources
*   [Symfony Docs - Cache Tags](https://symfony.com/doc/current/cache.html#using-cache-tags)
*   [Symfony Docs - Chain](https://symfony.com/doc/current/cache.html#creating-a-cache-chain)
