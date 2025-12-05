# Modèle d'Expiration (Expiration Model)

## Concept clé
C'est la méthode la plus efficace : le serveur dit "Cette réponse est valide pendant X secondes".
Pendant ce temps, le cache (navigateur ou proxy) sert la réponse stockée **sans jamais contacter le serveur**.

## Application dans Symfony 7.0
En-têtes utilisés :
*   `Cache-Control: max-age=600` (Expiration en secondes).
*   `Cache-Control: s-maxage=3600` (Expiration spécifique pour les caches "Shared" / Proxies).
*   `Expires: Sat, 26 Aug 2025...` (Date absolue, moins utilisé aujourd'hui, `max-age` est prioritaire).

### Code
```php
public function index(): Response
{
    $response = new Response('Hello');
    
    // Cache pour 1 heure chez le client
    $response->setMaxAge(3600);
    
    // Cache pour 1 journée sur le CDN/Varnish
    $response->setSharedMaxAge(86400);
    
    // Cache public (par défaut si s-maxage est set, mais explicite est mieux)
    $response->setPublic();
    
    return $response;
}
```

## Points de vigilance (Certification)
*   **Invalidation** : L'inconvénient de l'expiration est qu'il est difficile d'invalider le cache navigateur avant la fin du délai (sauf à changer l'URL).
*   **s-maxage** : Si `s-maxage` est présent, les caches partagés l'utilisent et ignorent `max-age`. Les navigateurs ignorent `s-maxage`.

## Ressources
*   [Symfony Docs - Expiration](https://symfony.com/doc/current/http_cache/expiration.html)

