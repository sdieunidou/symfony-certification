# Mise en cache (Caching HTTP)

## Concept clé
Le cache HTTP est un mécanisme fondamental pour la performance web. Il permet de stocker des copies de réponses pour réduire la charge serveur, la latence et la bande passante.
Il repose sur deux modèles distincts mais complémentaires :

1.  **Expiration (Cache-Control, Expires)** :
    *   Le serveur dit : "Cette ressource est valide pour 3600 secondes".
    *   Tant que le délai n'est pas écoulé, le navigateur (ou le proxy) **NE CONTACTE PAS** le serveur. C'est le cache le plus performant (0 appel réseau).
2.  **Validation (ETag, Last-Modified)** :
    *   Le délai d'expiration est passé. Le client demande : "J'ai cette version du fichier, est-elle toujours à jour ?".
    *   Le serveur compare. Si inchangé, il répond **304 Not Modified** (Header sans corps). Cela économise la bande passante, mais nécessite un aller-retour serveur (Network Roundtrip).

## Directives Cache-Control Standard
*   `max-age` : Durée de vie en secondes.
*   `s-maxage` : Durée de vie pour les caches partagés uniquement.
*   `public` : Peut être caché par tout le monde.
*   `private` : Peut être caché uniquement par le client final (navigateur).
*   `no-cache` : Doit revalider avec le serveur avant chaque utilisation (mal nommé !).
*   `no-store` : Interdiction absolue de stocker la réponse.
*   `must-revalidate` : Une fois expiré, interdiction de servir le contenu périmé (stale) sans revalidation réussie.
*   `immutable` : La ressource ne changera jamais pendant sa durée de vie.

## Ressources
*   [RFC 7234 - Caching](https://tools.ietf.org/html/rfc7234)
*   [MDN - HTTP Caching](https://developer.mozilla.org/en-US/docs/Web/HTTP/Caching)
