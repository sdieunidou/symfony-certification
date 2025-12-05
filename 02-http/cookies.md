# Cookies

## Concept clé
Un Cookie est une petite donnée (clé=valeur) envoyée par le serveur au client (Header `Set-Cookie`), stockée par le navigateur, et renvoyée par le client au serveur à chaque requête suivante vers le même domaine (Header `Cookie`). C'est le mécanisme principal pour maintenir l'état (sessions).

## Application dans Symfony 7.0
Symfony gère les cookies via la classe `Symfony\Component\HttpFoundation\Cookie`.
*   **Lecture** : Via `$request->cookies->get('nom')`.
*   **Écriture** : On n'écrit pas directement dans la réponse. On crée un objet `Cookie`, on l'ajoute aux headers de la réponse (`$response->headers->setCookie($cookie)`), et on envoie la réponse.

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

public function setPreference(Request $request): Response
{
    $response = new Response();
    
    // Création d'un cookie sécurisé
    $cookie = Cookie::create(
        'theme',           // Nom
        'dark',            // Valeur
        new \DateTime('+1 year'), // Expiration
        '/',               // Path
        null,              // Domain
        true,              // Secure (HTTPS only)
        true,              // HttpOnly (inaccessible via JS)
        false,             // Raw
        Cookie::SAMESITE_LAX // SameSite
    );

    $response->headers->setCookie($cookie);
    
    return $response;
}

public function getPreference(Request $request): Response
{
    // Lecture
    $theme = $request->cookies->get('theme', 'light');
    
    // Suppression (en fait, on renvoie un cookie expiré)
    $response = new Response();
    $response->headers->clearCookie('theme');
    
    return $response;
}
```

## Points de vigilance (Certification)
*   **HttpOnly** : Très important pour la sécurité (empêche le vol de cookie par XSS). `true` par défaut dans les sessions Symfony, mais paramètre constructeur à vérifier.
*   **Secure** : Le cookie n'est envoyé que si la requête est HTTPS.
*   **SameSite** : (`Lax`, `Strict`, `None`). Contrôle l'envoi du cookie lors des requêtes cross-site (protection CSRF). Depuis les navigateurs récents, `Lax` est souvent la valeur par défaut implicite.
*   **ClearCookie** : La méthode `clearCookie()` du ResponseHeaderBag crée en fait un Set-Cookie avec une date d'expiration dans le passé.

## Ressources
*   [Symfony Docs - Cookies](https://symfony.com/doc/current/components/http_foundation.html#setting-cookies)

