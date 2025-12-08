# L'objet Request (Usage Contrôleur)

## Concept clé
Dans un contrôleur, l'objet `Request` est votre fenêtre sur les données envoyées par le client.
Il est injecté par type-hint : `public function index(Request $request)`.

## Récupération des Données (Les "Bags")

| Propriété | Source PHP | Usage |
| :--- | :--- | :--- |
| `$request->query` | `$_GET` | Paramètres d'URL (`?page=1`). |
| `$request->request` | `$_POST` | Données de formulaire. |
| `$request->files` | `$_FILES` | Fichiers uploadés. |
| `$request->cookies` | `$_COOKIE` | Cookies client. |
| `$request->headers` | `$_SERVER` | En-têtes HTTP (`User-Agent`, `Content-Type`). |
| `$request->attributes`| (Symfony) | Paramètres de route (`{id}`), `_route`, etc. |
| `$request->server` | `$_SERVER` | Variables serveur (`REMOTE_ADDR`). |

## Méthodes Utiles

### Typage (InputBag)
Depuis Symfony 5+, `query`, `request` et `cookies` sont des `InputBag`.
*   `$request->query->getInt('page', 1)` : Force en entier.
*   `$request->query->getBoolean('ajax')` : Convertit 'true', '1', 'on' en `true`.
*   `$request->query->getString('name')` : Force en string.
*   `$request->query->getEnum('status', MyEnum::class)` (Symfony 6.3+).

### Contenu Brut (JSON API)
Pour une API JSON, `$_POST` est vide.
*   `$request->getContent()` : Chaîne JSON brute.
*   `$request->toArray()` : Convertit le JSON en tableau PHP (lance une Exception si invalide).

### Infos Requête
*   `$request->getMethod()` : 'GET', 'POST'...
*   `$request->getClientIp()` : IP du client (gère les proxies si configuré).
*   `$request->getPreferredLanguage(['en', 'fr'])`.
*   `$request->isXmlHttpRequest()` : Vérifie header `X-Requested-With` (AJAX jQuery legacy). *Note: Moins utilisé avec `fetch` moderne qui n'envoie pas ce header par défaut.*

## Attributs vs Paramètres
Confusion classique :
*   URL : `/product/123?sort=price`
*   Route : `/product/{id}`
*   `$request->attributes->get('id')` -> `123` (Routing)
*   `$request->query->get('sort')` -> `price` (Query String)

## RequestStack
Si vous avez besoin d'accéder à la requête **en dehors d'un contrôleur** (ex: dans un Service, une Extension Twig ou un Listener), vous ne devez pas injecter `Request` mais **`RequestStack`**.

```php
use Symfony\Component\HttpFoundation\RequestStack;

class MyService
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    public function doSomething(): void
    {
        $request = $this->requestStack->getCurrentRequest();
        
        if (!$request) {
            return; // Pas de requête (ex: contexte CLI)
        }
        
        // ...
    }
}
```

### Méthodes Clés
*   **`getCurrentRequest()`** : Retourne la requête active (peut être une sous-requête). Retourne `null` en CLI.
*   **`getMainRequest()`** : Retourne la requête principale (Master Request), même si on est dans une sous-requête (ex: `{{ render(controller(...)) }}`).

## 🧠 Concepts Clés
1.  **Stateless** : L'objet Request est recréé à chaque requête.
2.  **Immutabilité** : Ne modifiez pas l'objet Request manuellement (sauf cas très avancés). Considérez-le comme "Read-Only".

## ⚠️ Points de vigilance (Certification)
*   **Injection** : Ne jamais faire `new Request()` dans un contrôleur. Toujours l'injecter. `Request::createFromGlobals()` est réservé au Front Controller (`index.php`).
*   **Session** : `$request->getSession()` démarre la session si nécessaire.

## Ressources
*   [Symfony Docs - Request](https://symfony.com/doc/current/components/http_foundation.html#accessing-request-data)
