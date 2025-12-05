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

## Application dans Symfony 7.0
Symfony embrasse totalement la spécification HTTP.
*   **L'objet `Response`** : Fournit une API fluide pour manipuler les headers `Cache-Control`, `ETag`, `Last-Modified`.
*   **HttpCache (Reverse Proxy)** : Symfony intègre un reverse proxy écrit en PHP (`Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache`). Il est utile pour le développement ou les hébergements mutualisés, mais en production haute performance, on lui préfère souvent **Varnish** ou un **CDN** (Cloudflare, Fastly).

## Exemple de code

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{
    // Méthode 1 : Via l'Attribut #[Cache] (Recommandé pour les cas simples)
    #[Cache(public: true, maxage: 60, smaxage: 3600)]
    public function index(): Response
    {
        return $this->render('blog/index.html.twig');
    }

    // Méthode 2 : Gestion Fine dans le contrôleur
    public function show(Request $request, Post $post): Response
    {
        $response = new Response();

        // --- 1. Modèle de Validation ---
        // On utilise la date de modif de l'entité pour l'ETag ou Last-Modified
        $response->setEtag(md5($post->getContent() . $post->getUpdatedAt()->format('c')));
        $response->setLastModified($post->getUpdatedAt());
        
        // Définit la réponse comme "Public" (cacheable par CDN/Proxy)
        $response->setPublic(); 

        // Vérification automatique :
        // Compare l'ETag de la réponse avec le header 'If-None-Match' de la requête.
        // Si match : configure le status 304, supprime le contenu, et renvoie TRUE.
        if ($response->isNotModified($request)) {
            return $response; // Arrêt immédiat, on n'envoie pas le HTML
        }

        // --- 2. Modèle d'Expiration ---
        // Si le cache n'est pas valide, on génère le contenu et on définit sa durée de vie
        $response->setMaxAge(60);       // Cache privé/navigateur (60s)
        $response->setSharedMaxAge(600); // Cache partagé/Varnish (10min)
        
        // Header Vary : Important si le contenu dépend d'autre chose que l'URL
        // Ex: Si le contenu change selon que l'user est compressé gzip ou non, ou le User-Agent
        $response->setVary(['Accept-Encoding', 'User-Agent']);

        $response->setContent($this->renderView('blog/show.html.twig', ['post' => $post]));

        return $response;
    }
}
```

## Directives Cache-Control Avancées
*   `must-revalidate` : Une fois le cache expiré, le client DOIT revalider avec le serveur (interdit de servir du contenu périmé même si le serveur est down).
*   `proxy-revalidate` : Idem, mais pour les caches partagés uniquement.
*   `immutable` : "Ce contenu ne changera JAMAIS". Le navigateur ne revalidera jamais tant qu'il est dans la période `max-age`. Idéal pour les assets versionnés (`style.123.css`).
*   `stale-while-revalidate` : Permet de servir un contenu périmé pendant qu'une revalidation asynchrone se fait en arrière-plan.
*   `stale-if-error` : Sert du contenu périmé si le serveur backend plante.

## 🧠 Concepts Clés
1.  **Cache Gateway (Reverse Proxy)** : C'est l'intermédiaire (Varnish, Symfony HttpCache, CDN) qui stocke les réponses "Public".
2.  **Invalidation** : Le problème difficile du cache. Le modèle d'Expiration ne permet pas l'invalidation instantanée facile (il faut attendre la fin du TTL). Le modèle de Validation nécessite toujours un contact serveur. L'invalidation explicite (PURGE) est spécifique aux Reverse Proxies (Varnish).
3.  **Private by default** : Si une session est démarrée ou un header `Authorization` présent, Symfony passe automatiquement le Cache-Control à `private, must-revalidate` pour éviter qu'un CDN ne cache la page "Mon Profil" de Toto et la serve à Tata.

## ⚠️ Points de vigilance (Certification)
*   **s-maxage vs max-age** : `s-maxage` (Shared Max Age) écrase `max-age` pour les caches partagés (CDNs, Proxies), mais est ignoré par les navigateurs privés.
*   **Vary** : Oublier le header `Vary` est une source majeure de bugs. Si vous servez une version Mobile et une Desktop sur la même URL, vous DEVEZ mettre `Vary: User-Agent`, sinon le CDN servira la version Mobile aux utilisateurs Desktop (ou inversement).
*   **`Response::setCache()`** : Méthode helper pratique pour définir ETag, LastModified et MaxAge en un seul appel.
*   **No-Store** : Seule directive garantissant qu'aucune copie n'est gardée (`no-cache` signifie en réalité "doit valider avant de servir", ce qui est trompeur).

## Ressources
*   [Symfony Docs - HTTP Cache](https://symfony.com/doc/current/http_cache.html)
*   [RFC 7234 - Caching](https://tools.ietf.org/html/rfc7234)
*   [Cache-Control: immutable](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cache-Control#immutable)
