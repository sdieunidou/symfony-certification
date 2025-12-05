# Cookies (Usage dans le Contrôleur)

## Concept clé
HTTP est un protocole sans état. Les Cookies permettent de stocker de petites informations côté client pour simuler un état (préférences, tracking).
Dans un contrôleur Symfony, la gestion des cookies est stricte :
*   **Lecture** : Via la `Request`.
*   **Écriture** : Via la `Response`.

## Lecture (Request)
Les cookies envoyés par le navigateur sont dans `$request->cookies`.

```php
public function index(Request $request): Response
{
    // Récupère la valeur ou 'default' si absent
    $theme = $request->cookies->get('theme', 'light');
    
    // Récupère et valide (typé via InputBag)
    $trackingAllowed = $request->cookies->getBoolean('tracking_allowed');
}
```

## Écriture (Response)
On ne peut pas "envoyer" un cookie n'importe quand. Il faut l'attacher à l'objet `Response` qui sera retourné par le contrôleur.

```php
use Symfony\Component\HttpFoundation\Cookie;

public function switchTheme(): Response
{
    $response = $this->redirectToRoute('homepage');
    
    // Création
    $cookie = Cookie::create('theme', 'dark')
        ->withExpires(new \DateTime('+1 month'))
        ->withHttpOnly(true)
        ->withSecure(true)
        ->withSameSite(Cookie::SAMESITE_LAX);
        
    // Attachement
    $response->headers->setCookie($cookie);
    
    return $response;
}
```

## Suppression
Pour supprimer un cookie, on envoie un cookie avec le même nom, le même chemin/domaine, mais une date d'expiration passée et une valeur vide.
Symfony fournit un helper :

```php
$response->headers->clearCookie('theme', '/', null, true, true, Cookie::SAMESITE_LAX);
```
**Important** : Les paramètres (path, domain, secure) doivent être identiques à ceux utilisés lors de la création pour que la suppression fonctionne.

## 🧠 Concepts Clés
1.  **Response HeadersBag** : La méthode `setCookie` n'envoie pas le header tout de suite. Elle stocke le cookie dans le sac de headers de la réponse. Le header `Set-Cookie` est généré au moment du `$response->send()`.
2.  **Cookie vs Session** : Utilisez les cookies pour les données non sensibles et persistantes (préférences IHM). Utilisez la Session pour les données sensibles et temporaires (Auth, Panier).

## ⚠️ Points de vigilance (Certification)
*   **Auto-login** : Ne créez pas votre propre système de cookie "Remember Me" manuellement. Utilisez le système natif du composant Security (`remember_me`).
*   **GDPR** : Tout cookie non essentiel nécessite un consentement.
*   **Modification** : `$request->cookies->set(...)` ne modifie le cookie que pour la durée du script PHP courant. Cela n'envoie rien au navigateur. Seul `$response->headers->setCookie(...)` envoie l'ordre au navigateur.

## Ressources
*   [Symfony Docs - Cookies](https://symfony.com/doc/current/components/http_foundation.html#setting-cookies)
*   [API Cookie Class](https://github.com/symfony/http-foundation/blob/7.0/Cookie.php)
