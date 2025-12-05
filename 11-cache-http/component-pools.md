# Pools de Cache Personnalisés

## Concept clé
Au lieu d'utiliser un seul pool global (`cache.app`), vous pouvez créer des **Pools** isolés.
Chaque pool a son propre namespace, ses propres réglages (backend, durée de vie) et peut être vidé indépendamment.
Cela évite les conflits de clés et permet d'utiliser Redis pour certaines données et Filesystem pour d'autres.

## Configuration (`framework.yaml`)

```yaml
framework:
    cache:
        default_redis_provider: 'redis://localhost'

        pools:
            # 1. Pool simple (utilise config app par défaut)
            custom_thing.cache:
                adapter: cache.app

            # 2. Pool spécifique (Filesystem)
            my_files.cache:
                adapter: cache.adapter.filesystem
            
            # 3. Pool avec configuration Provider spécifique
            my_redis.cache:
                adapter: cache.adapter.redis
                provider: 'redis://user:pass@1.2.3.4'
```

## Options de Provider Custom
Si vous avez besoin d'options spécifiques pour Redis (timeout, retry), vous devez définir le provider comme un service à part.

```yaml
framework:
    cache:
        pools:
            cache.my_redis:
                adapter: cache.adapter.redis
                provider: app.my_custom_redis_provider

services:
    app.my_custom_redis_provider:
        class: \Redis
        factory: ['Symfony\Component\Cache\Adapter\RedisAdapter', 'createConnection']
        arguments:
            - 'redis://localhost'
            - { retry_interval: 2, timeout: 10 }
```

## Injection de Dépendance
Chaque pool défini devient un **Service** nommé.
Symfony crée aussi un alias autowirable basé sur le nom (CamelCase).

*   Nom du pool : `custom_thing.cache`
*   Argument autowire : `$customThingCache`

```php
use Symfony\Contracts\Cache\CacheInterface;

public function __construct(
    private CacheInterface $customThingCache, // Injecte custom_thing.cache
    private CacheInterface $myRedisCache      // Injecte my_redis.cache
) {}
```

## Isolation (Namespacing)
Chaque pool préfixe ses clés (Hash du nom du pool + seed).
Les clés de `pool_a` ne peuvent jamais écraser celles de `pool_b`, même s'ils utilisent la même base Redis.
Si vous voulez partager le cache avec une autre application (ex: microservices), vous pouvez forcer un `namespace` fixe via les tags de service, mais c'est rare.

## Ressources
*   [Symfony Docs - Custom Pools](https://symfony.com/doc/current/cache.html#creating-custom-namespaced-pools)
