# Codes de Statut HTTP

## Concept clé
Le code de statut est un entier de 3 chiffres renvoyé par le serveur pour indiquer l'issue de la requête.
Il est crucial pour :
1.  **Le Client** : Savoir s'il doit afficher le contenu, retenter, ou corriger sa requête.
2.  **Le SEO** : Google traite différemment une 404 (supprimé), une 410 (définitivement parti), ou une 301 (déménagé).
3.  **Le Monitoring** : Taux d'erreurs 5xx vs 4xx.

## Catégories
*   **1xx (Information)** : Protocole intermédiaire (ex: `100 Continue`, `103 Early Hints`).
*   **2xx (Succès)** : La requête a été comprise et traitée.
*   **3xx (Redirection)** : Action supplémentaire requise (changement d'URL ou cache).
*   **4xx (Erreur Client)** : Le client a fait une erreur (syntaxe, droits, ressource inexistante).
*   **5xx (Erreur Serveur)** : Le serveur a échoué (bug, surcharge).

## Codes Importants pour un Expert Symfony

| Code | Constante Symfony `Response::` | Signification & Usage |
| :--- | :--- | :--- |
| **200** | `HTTP_OK` | Succès standard. |
| **201** | `HTTP_CREATED` | Ressource créée (suite à POST/PUT). Doit retourner header `Location`. |
| **204** | `HTTP_NO_CONTENT` | Succès mais pas de corps (ex: DELETE réussi). |
| **301** | `HTTP_MOVED_PERMANENTLY` | Redirection SEO définitive (Cacheable). |
| **302** | `HTTP_FOUND` | Redirection temporaire (Standard historique). |
| **307** | `HTTP_TEMPORARY_REDIRECT` | Comme 302, mais garantit que la méthode HTTP ne change pas (POST reste POST). |
| **304** | `HTTP_NOT_MODIFIED` | Cache validation (voir fichier Caching). Pas de corps. |
| **400** | `HTTP_BAD_REQUEST` | Erreur générique (ex: JSON malformé). |
| **401** | `HTTP_UNAUTHORIZED` | **Non Authentifié**. Il manque le token/login. |
| **403** | `HTTP_FORBIDDEN` | **Non Autorisé**. Authentifié mais droits insuffisants. |
| **404** | `HTTP_NOT_FOUND` | Ressource introuvable. |
| **405** | `HTTP_METHOD_NOT_ALLOWED` | Mauvaise méthode (GET sur route POST). |
| **406** | `HTTP_NOT_ACCEPTABLE` | Négociation de contenu échouée (Client veut XML, serveur ne fait que JSON). |
| **422** | `HTTP_UNPROCESSABLE_ENTITY` | Erreur de Validation (sémantique). Le JSON est valide, mais l'email est vide. Standard API moderne. |
| **429** | `HTTP_TOO_MANY_REQUESTS` | Rate Limiting atteint. |
| **500** | `HTTP_INTERNAL_SERVER_ERROR` | Bug non géré (Exception). |
| **502** | `HTTP_BAD_GATEWAY` | Erreur du upstream (ex: PHP-FPM est down, Nginx renvoie 502). |
| **503** | `HTTP_SERVICE_UNAVAILABLE` | Maintenance ou surcharge temporaire. |

## Application dans Symfony 7.0
Symfony mappe souvent les Exceptions vers des Codes HTTP via un `SubscribedEvent` dans le `ExceptionListener`.

### Mapping Automatique (Exceptions → Codes)
Certaines exceptions Symfony génèrent automatiquement le bon code :
*   `NotFoundHttpException` -> **404**
*   `AccessDeniedException` -> **403**
*   `MethodNotAllowedHttpException` -> **405**

### Configuration Personnalisée (`framework.yaml`)
On peut définir ses propres mappings :

```yaml
framework:
    exceptions:
        App\Exception\Domain\UserBannedException: 403
        App\Exception\Validation\InvalidSkuException: 422
```

### Attributs (Symfony 6.3+)
Depuis Symfony 6.3, on peut mapper le code directement sur la classe Exception :

```php
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpFoundation\Response;

#[WithHttpStatus(Response::HTTP_UNPROCESSABLE_ENTITY)]
class InvalidOrderException extends \Exception
{
}
```

## Exemple de code

```php
public function create(Request $request): Response
{
    // ... création ...
    
    // Retourner 201 Created avec Location
    return new JsonResponse(
        ['id' => $product->getId()], 
        Response::HTTP_CREATED, 
        ['Location' => '/api/products/' . $product->getId()]
    );
}

public function delete(int $id): Response
{
    // ... suppression ...
    
    // Retourner 204 No Content
    return new Response(null, Response::HTTP_NO_CONTENT);
}
```

## 🧠 Concepts Clés
1.  **Sémantique** : Utilisez le code le plus précis possible. Une erreur de validation de formulaire n'est pas une 400 (Bad Request = syntaxe) mais une 422 (Unprocessable Entity = logique).
2.  **Security** : Ne jamais retourner de 500 en prod avec la stack trace (faille informationnelle). Symfony gère cela en affichant une page générique en prod.
3.  **Teapot** : Le code **418** (I'm a teapot) est supporté comme un easter egg officiel du standard HTTP et de Symfony.

## ⚠️ Points de vigilance (Certification)
*   **401 vs 403** : Confusion fréquente.
    *   401 = "Qui es-tu ?" (Authentication). Le header `WWW-Authenticate` est requis.
    *   403 = "Tu n'as pas le droit" (Authorization).
*   **301 vs 302 vs 307** :
    *   Si vous redirigez un formulaire POST avec un 301 ou 302, la plupart des navigateurs transforment la requête en GET sur la nouvelle URL (données perdues).
    *   Pour conserver le POST (ex: redirection vers un autre serveur d'API), utilisez **307** ou **308** (Permanent).

## Ressources
*   [IANA HTTP Status Registry](https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml)
*   [Symfony Exception Mapping](https://symfony.com/doc/current/controller/error_pages.html#mapping-exceptions-to-specific-status-codes)
