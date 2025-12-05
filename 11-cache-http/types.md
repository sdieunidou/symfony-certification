# Types de Cache HTTP

## Concept clé
Le cache HTTP n'est pas un stockage unique, c'est une chaîne de caches situés entre le serveur original et le client.

## Les différents acteurs
1.  **Cache Navigateur (Browser Cache)** : Privé. Stocké sur la machine de l'utilisateur. Rapide, mais ne bénéficie qu'à un seul utilisateur.
2.  **Proxy Cache (Gateway Cache / Reverse Proxy)** : Partagé. Situé devant le serveur (Varnish, Nginx, Symfony HttpCache) ou sur le réseau (CDN like Cloudflare, Akamai). Bénéficie à tous les utilisateurs.
3.  **Proxy Forward** : Cache situé chez le FAI ou dans l'entreprise de l'utilisateur (plus rare aujourd'hui avec HTTPS).

## Application dans Symfony 7.0
Symfony gère ces caches via les en-têtes HTTP standard (`Cache-Control`).
Le framework distingue :
*   `public` : Cacheable par tous (Browser + Proxies + CDN).
*   `private` : Cacheable uniquement par le navigateur (données utilisateur).

## Points de vigilance (Certification)
*   **HTTPS** : Les proxies partagés ne peuvent généralement pas cacher le contenu HTTPS chiffré, sauf s'ils sont le point de terminaison SSL (Reverse Proxy / CDN).
*   **Symfony HttpCache** : Un Reverse Proxy écrit en PHP, activable dans `index.php`. Moins performant que Varnish, mais très utile si on n'a pas la main sur l'infra.

## Ressources
*   [Symfony Docs - HTTP Cache](https://symfony.com/doc/current/http_cache.html#types-of-caches)

