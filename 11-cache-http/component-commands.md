# Commandes Console (Cache Component)

## Concept clé
Symfony fournit des commandes pour gérer les pools de cache applicatifs (vider, lister).
Cela ne concerne PAS le cache HTTP (Varnish), ni le dossier `var/cache` (qui se vide via `cache:clear`).
Ici on parle de vider Redis, APCu, etc.

## Commandes Principales

### 1. Lister les Pools
```bash
php bin/console cache:pool:list
```
Affiche tous les pools définis (système et custom).

### 2. Vider un Pool (`cache:pool:clear`)
```bash
# Vider un pool spécifique
php bin/console cache:pool:clear my_cache_pool

# Vider tous les pools
php bin/console cache:pool:clear --all

# Vider le cache "app" (défaut)
php bin/console cache:pool:clear cache.app_clearer
```
Cette commande supprime physiquement les entrées du stockage (Redis `FLUSHDB` ou suppression fichiers).

### 3. Invalider des Tags (`cache:pool:invalidate-tags`)
Si vous utilisez un cache taggé (`TagAware`).

```bash
# Supprimer tous les items taggés "product_123"
php bin/console cache:pool:invalidate-tags product_123
```

## Clearers
Symfony groupe les pools dans des "Clearers" :
*   `cache.global_clearer` : Tout.
*   `cache.system_clearer` : Utilisé par `cache:clear` (warmup).
*   `cache.app_clearer` : Cache applicatif par défaut.

## ⚠️ Points de vigilance (Certification)
*   `cache:clear` vs `cache:pool:clear` :
    *   `cache:clear` vide le dossier `var/cache` (Container, Twig, Routing) ET appelle `cache.system_clearer`.
    *   `cache:pool:clear` est l'outil chirurgical pour vos données métier (Redis, etc.).

## Ressources
*   [Symfony Docs - Clearing Cache](https://symfony.com/doc/current/cache.html#clearing-the-cache)
