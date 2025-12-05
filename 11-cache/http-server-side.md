# Symfony Reverse Proxy (HttpCache)

## Concept clé
Symfony inclut un Reverse Proxy écrit en PHP (`Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache`). Il se comporte comme Varnish mais sans infrastructure supplémentaire. Il intercepte les requêtes avant qu'elles n'atteignent le Kernel de l'application.

## Mise en Place
Il s'active dans `config/packages/framework.yaml` ou en enveloppant le Kernel dans `public/index.php`.

**Configuration recommandée (framework.yaml)** :
```yaml
when@prod:
    framework:
        http_cache: true
        # Options avancées
        # http_cache:
        #     trace_level: short # none, short, full (Ajoute X-Symfony-Cache)
        #     trace_header: X-Symfony-Cache
```

**Méthode Legacy (index.php)** :
```php
// public/index.php
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

if ('prod' === $kernel->getEnvironment()) {
    $kernel = new HttpCache($kernel);
}
```

## Fonctionnalités
1.  **Gateway Cache** : Stocke les réponses publiques (`s-maxage`).
2.  **Validation** : Gère les `If-None-Match` pour le compte du client.
3.  **ESI** : Traite les tags `<esi:include>` (Edge Side Includes) pour assembler les fragments.
4.  **Invalidation** : Permet de purger le cache via des requêtes HTTP (PURGE).

## Debugging (`X-Symfony-Cache`)
En mode debug (ou si configuré), le header `X-Symfony-Cache` indique l'état :
*   `HIT` : Servi depuis le cache.
*   `MISS` : Pas en cache, requête transmise à l'application.
*   `FRESH` : En cache et valide (non expiré).
*   `STALE` : En cache mais expiré (peut être servi si `stale-if-error` ou `stale-while-revalidate`).

## Performance vs Varnish
*   **Symfony HttpCache** : Écrit en PHP. Boot un process PHP à chaque requête. Plus lent que Varnish/Nginx, mais suffisant pour multiplier les perfs par x10 vs l'appli brute.
*   **Varnish** : Écrit en C. Extrêmement rapide. Gère le cache en RAM. Recommandé pour les forts trafics.

## 🧠 Concepts Clés
1.  **Store** : Par défaut, Symfony stocke le cache sur le disque (`var/cache/prod/http_cache`).
2.  **Surrogate Capability** : Le proxy s'annonce à l'application via le header `Surrogate-Capability`, ce qui active automatiquement le support ESI dans Symfony.

## ⚠️ Points de vigilance (Certification)
*   **IP Client & Trusted Proxies** : Derrière un reverse proxy, `REMOTE_ADDR` est l'IP du proxy, pas celle du client. Il est impératif de configurer les `trusted_proxies` dans `framework.yaml` pour que Symfony fasse confiance aux headers `X-Forwarded-For` et restaure la vraie IP client.

## Ressources
*   [Symfony Docs - Reverse Proxy](https://symfony.com/doc/current/http_cache.html#symfony-reverse-proxy)
