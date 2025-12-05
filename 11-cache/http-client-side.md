# Mise en cache Client-side (Navigateur)

## Concept clé
C'est le niveau de cache le plus proche de l'utilisateur. Il réduit la latence réseau à zéro si le cache est valide.
Le serveur contrôle ce cache via l'en-tête HTTP `Cache-Control`.

## Directives Principales

### 1. `private` vs `public`
*   **`private`** (Défaut avec session) : La réponse est spécifique à l'utilisateur (ex: "Mon Profil"). Elle ne doit être stockée **que** par le navigateur, jamais par un CDN ou Proxy intermédiaire.
*   **`public`** : La réponse est générique (ex: "Homepage"). Elle peut être stockée par tout le monde (Navigateur, CDN, Proxy d'entreprise).

### 2. `max-age`
Durée de vie en secondes.
`Cache-Control: private, max-age=600` : "Navigateur, garde ça 10 minutes. Ne me rappelle pas."

### 3. `no-cache` vs `no-store`
Confusion fréquente à l'examen.
*   **`no-cache`** : "Tu peux stocker, MAIS tu dois valider avec le serveur (ETag) avant chaque réutilisation". (Cache avec validation forcée).
*   **`no-store`** : "Interdiction absolue de stocker quoi que ce soit sur le disque". Utilisé pour les données bancaires ou très sensibles.

## Application dans Symfony 7.0

```php
public function index(): Response
{
    $response = new Response('...');

    // 1. Cache privé (Navigateur seulement) - 10 minutes
    $response->setPrivate();
    $response->setMaxAge(600);

    // 2. Cache public (CDN friendly) - 1 heure
    $response->setPublic();
    $response->setMaxAge(3600);

    // 3. Désactiver le cache (pour de vrai)
    $response->headers->addCacheControlDirective('no-store', true);

    return $response;
}
```

## 🧠 Concepts Clés
1.  **Session** : Si une session est active (cookie `PHPSESSID`), Symfony passe automatiquement la réponse en `private, must-revalidate, max-age=0` pour éviter les fuites de données utilisateur sur des caches partagés. Vous devez appeler `setPublic()` explicitement pour surcharger ça.
2.  **Immutable** : `Cache-Control: immutable` indique que le contenu ne changera jamais (ex: fichier asset versionné). Le navigateur ne revalidera jamais, même si l'utilisateur fait "Refresh".

## ⚠️ Points de vigilance (Certification)
*   **Expires** : C'est un vieux header (HTTP/1.0) contenant une date absolue. `Cache-Control: max-age` (HTTP/1.1) est prioritaire. Symfony gère les deux si besoin mais privilégie `max-age`.

## Ressources
*   [MDN - Cache-Control](https://developer.mozilla.org/fr/docs/Web/HTTP/Headers/Cache-Control)
*   [Symfony Docs - HTTP Cache](https://symfony.com/doc/current/http_cache.html)
