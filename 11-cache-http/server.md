# Mise en cache Server-side (Reverse Proxy)

## Concept clé
Cacher les réponses complètes HTML avant même qu'elles n'atteignent l'application PHP (Front Controller). C'est ce qui permet d'atteindre des performances énormes (milliers de req/s).

## Application dans Symfony 7.0
Deux options :
1.  **Symfony HttpCache** (PHP) : Un reverse proxy implémenté en PHP. Facile à installer (décorateur du Kernel dans `index.php`).
    ```php
    // public/index.php
    $kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
    // Envelopper le kernel
    $kernel = new HttpCache($kernel);
    ```
2.  **Varnish / Nginx** (Infra) : Solutions dédiées plus performantes.

### Invalidation
L'inconvénient du cache serveur est qu'il faut le purger quand les données changent.
Symfony ne gère pas la purge nativement (le standard HTTP ne le prévoit pas), mais le `FOSHttpCacheBundle` est souvent utilisé pour envoyer des requêtes `PURGE` à Varnish.

## Points de vigilance (Certification)
*   **HttpCache** : Supporte l'ESI (Edge Side Includes). Stocke les fichiers dans `var/cache/prod/http_cache`.
*   **Shared** : Les caches serveurs sont "Shared". Ils ignorent les headers `private`. Ils utilisent `s-maxage`.

## Ressources
*   [Symfony Docs - Symfony Reverse Proxy](https://symfony.com/doc/current/http_cache.html#symfony-reverse-proxy)

