# Mise en cache (Caching HTTP)

## Concept clé
Le cache HTTP permet de stocker des copies de réponses pour réduire la charge serveur, la latence et la bande passante.
Il repose sur deux modèles :
1.  **Expiration** (Cache-Control, Expires) : "Cette réponse est valide pendant 3600s". Le navigateur ne rappelle même pas le serveur.
2.  **Validation** (ETag, Last-Modified) : "Cette réponse est-elle toujours à jour ?". Le navigateur envoie une requête conditionnelle (`If-None-Match`, `If-Modified-Since`). Si inchangé, le serveur répond `304 Not Modified` (sans corps).

## Application dans Symfony 7.0
Symfony fournit des méthodes helper sur l'objet `Response` pour gérer ces headers complexes.
Le framework supporte aussi un Reverse Proxy intégré (`HttpCache`) écrit en PHP pour le développement ou les hébergements simples.

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Response;

public function index(): Response
{
    $response = new Response('Hello World');

    // 1. Modèle d'Expiration
    // Cache-Control: public, s-maxage=600, max-age=60
    $response->setPublic();
    $response->setMaxAge(60); // Cache privé/navigateur (60s)
    $response->setSharedMaxAge(600); // Cache partagé/proxy (600s)

    // 2. Modèle de Validation
    $date = new \DateTime(); // Date de modif de la ressource
    $response->setLastModified($date);
    
    // ETag (hash du contenu)
    $response->setEtag(md5($response->getContent()));

    // Vérification conditionnelle automatique
    // Si le client a envoyé If-None-Match correspondant à l'ETag,
    // isNotModified met le status à 304 et vide le contenu.
    if ($response->isNotModified($request)) {
        return $response; // Renvoie la réponse 304 vide
    }

    return $response;
}
```

## Points de vigilance (Certification)
*   **Public vs Private** :
    *   `Public` : Peut être mis en cache par tout le monde (Proxies partagés, CDN).
    *   `Private` : Uniquement par le navigateur de l'utilisateur final (données personnelles). Par défaut, si une session est active, Symfony marque la réponse comme `private`.
*   **s-maxage** : Prioritaire sur `max-age` mais uniquement pour les caches partagés (le "s" est pour shared).
*   **No-Store** : Interdit tout stockage (`$response->headers->addCacheControlDirective('no-store', true)`).

## Ressources
*   [Symfony Docs - HTTP Cache](https://symfony.com/doc/current/http_cache.html)

