# Cookies

## Concept clé
Un Cookie est un mécanisme de stockage **côté client** (navigateur) initié par le serveur.
*   **But** : Maintenir l'état (State) dans un protocole sans état (HTTP).
*   **Flux** :
    1.  Serveur envoie Header `Set-Cookie: name=value`.
    2.  Navigateur stocke.
    3.  Navigateur renvoie Header `Cookie: name=value` à chaque requête vers le même domaine.

## Application dans Symfony 7.0
Symfony abstrait la gestion via la classe `Symfony\Component\HttpFoundation\Cookie`.
**Important** : Symfony ne chiffre pas les cookies par défaut, mais signe le cookie de session.

## Structure et Sécurité d'un Cookie

Le constructeur de `Cookie` expose tous les flags de sécurité modernes :

```php
public function __construct(
    string $name,
    string|null $value = null,
    int|string|\DateTimeInterface $expire = 0,
    string|null $path = '/',
    string|null $domain = null,
    bool|null $secure = null, // HTTPS only
    bool $httpOnly = true,    // Inaccessible JS
    bool $raw = false,        // URL encoding
    string|null $sameSite = self::SAMESITE_LAX // CSRF Protection
)
```

### Flags de Sécurité Critiques
1.  **`HttpOnly`** : Si `true` (défaut Symfony), le cookie est invisible pour JavaScript (`document.cookie`). Protège contre le vol de session via failles XSS.
2.  **`Secure`** : Si `true`, le cookie n'est envoyé que sur connexions chiffrées (HTTPS).
3.  **`SameSite`** : Protège contre les attaques CSRF (Cross-Site Request Forgery).
    *   `Lax` (Recommandé/Défaut) : Envoyé sur navigation top-level (clic lien) mais pas sur appels AJAX cross-site ou images/iframes tiers.
    *   `Strict` : Jamais envoyé si l'origine diffère (même en cliquant sur un lien depuis un mail). Expérience utilisateur parfois dégradée.
    *   `None` : Toujours envoyé (requis pour certains embeds tiers). Nécessite impérativement `Secure=true`.

## Exemple de code Complet

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PreferenceController
{
    public function set(Request $request): Response
    {
        $response = new Response('Préférence sauvegardée');

        // Création d'un cookie robuste
        $cookie = Cookie::create('app_theme')
            ->withValue('dark')
            ->withExpires(new \DateTime('+1 year'))
            ->withSecure(true) // Uniquement HTTPS
            ->withHttpOnly(true) // Invisible JS
            ->withSameSite(Cookie::SAMESITE_LAX);

        // Ajout aux headers
        $response->headers->setCookie($cookie);

        return $response;
    }

    public function get(Request $request): Response
    {
        // Lecture (via ParameterBag)
        // Attention: Le client peut modifier cette valeur ! Ne jamais faire confiance aveuglément.
        $theme = $request->cookies->get('app_theme', 'light');

        return new Response("Thème: $theme");
    }
    
    public function delete(): Response
    {
        $response = new Response('Cookie supprimé');
        // Pour supprimer, on écrase avec un cookie expiré
        $response->headers->clearCookie('app_theme', '/', null, true, true, Cookie::SAMESITE_LAX);
        
        return $response;
    }
}
```

## Cookie vs Session
*   **Cookie** : Donnée stockée chez le client (max 4KB). Visible et modifiable par l'utilisateur (sauf si signé/chiffré). Usage : Préférences IHM, "Se souvenir de moi", Tracking.
*   **Session** : Donnée stockée sur le serveur (Fichier, Redis, DB). Seul l'ID de session est stocké dans un cookie chez le client (`PHPSESSID`). Usage : Authentification, Panier, Données sensibles.

## 🧠 Concepts Clés
1.  **Stateless** : Le serveur oublie tout après l'envoi de la réponse. Les cookies sont le moyen pour le client de rappeler au serveur "C'est encore moi" à la requête suivante.
2.  **Domain Scope** : Un cookie défini sur `.example.com` sera visible sur `blog.example.com` et `app.example.com`. Un cookie sur `app.example.com` ne sera pas visible sur `example.com`.
3.  **Cookie Marshalling** : Depuis Symfony 5/6, on peut utiliser le `CookieMarshallerInterface` pour chiffrer ou signer automatiquement la valeur des cookies, rendant leur modification par le client impossible.

## ⚠️ Points de vigilance (Certification)
*   **Raw vs UrlEncoded** : Par défaut, PHP et Symfony encodent les valeurs de cookies (les espaces deviennent `+` ou `%20`). Si vous devez interagir avec du JS ou d'autres langages attendant du brut, utilisez `$raw = true`.
*   **Taille** : Limite stricte ~4KB par cookie. Pour plus de données, utilisez la Session ou le LocalStorage (JS).
*   **Auto-Secure** : Symfony possède une option `framework.session.cookie_secure: 'auto'` qui active le flag Secure uniquement si la requête entrante est HTTPS.
*   **Acceptance** : Réglementation RGPD (GDPR). Vous ne pouvez pas poser de cookies non-essentiels (tracking) sans consentement. Les cookies de Session/Auth/Panier sont généralement considérés comme "essentiels".

## Ressources
*   [Symfony Docs - Cookies](https://symfony.com/doc/current/components/http_foundation.html#setting-cookies)
*   [MDN - Set-Cookie](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Set-Cookie)
*   [OWASP - SameSite](https://owasp.org/www-community/SameSite)
