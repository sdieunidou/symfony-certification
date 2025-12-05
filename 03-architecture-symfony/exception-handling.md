# Gestion des Exceptions (Architecture)

## Concept clé
En production, une application ne doit jamais crasher (Page blanche ou Stack Trace).
Symfony intercepte toutes les exceptions via le mécanisme `kernel.exception` (via `ExceptionEvent`) pour les transformer en objet `Response`.

## Flux de Traitement d'Erreur

1.  **Exception lancée** : `throw new NotFoundHttpException()`.
2.  **Kernel catch** : Le `HttpKernel` attrape l'exception.
3.  **Dispatch Event** : `ExceptionEvent` est dispatché.
4.  **ErrorListener** (Natif) :
    *   Log l'exception.
    *   Duplique la requête interne vers un contrôleur d'erreur (Forward).
5.  **ErrorController** : Rend une vue Twig (`error404.html.twig`) ou du JSON selon le format.

## Personnalisation des Pages d'Erreur

Symfony utilise `TwigBundle` pour rendre les erreurs.
Il suffit de créer des templates dans `templates/bundles/TwigBundle/Exception/` :
*   `error404.html.twig` (Page non trouvée)
*   `error403.html.twig` (Accès interdit)
*   `error500.html.twig` (Erreur serveur)
*   `error.html.twig` (Fallback pour toutes les autres erreurs)

## Exceptions HTTP (HttpExceptionInterface)
Pour contrôler le code HTTP de retour, lancez des exceptions implémentant `HttpExceptionInterface` ou utilisez les classes helper :

| Exception | Code HTTP | Usage |
| :--- | :--- | :--- |
| `NotFoundHttpException` | 404 | Ressource inexistante. |
| `AccessDeniedHttpException` | 403 | Interdit (Sécurité). |
| `BadRequestHttpException` | 400 | Syntaxe requête invalide. |
| `MethodNotAllowedHttpException`| 405 | GET sur POST. |
| `ServiceUnavailableHttpException`| 503 | Maintenance. |
| `UnprocessableEntityHttpException`| 422 | Validation échouée (API). |

## JSON & API Error Handling
Par défaut, Symfony rend du HTML. Pour une API, on veut du JSON.
Plusieurs stratégies :

### 1. Serializer (Symfony 6.4+)
Symfony peut sérialiser nativement les erreurs si le format est JSON (RFC 7807 Problem Details).

### 2. Event Listener Custom (Recommandé pour contrôle total)

```php
#[AsEventListener(event: KernelEvents::EXCEPTION)]
public function onKernelException(ExceptionEvent $event): void
{
    $e = $event->getThrowable();
    
    // Vérifier si c'est une requête API
    if (!$event->getRequest()->isXmlHttpRequest() && /* check accept header */) {
        return;
    }

    $data = [
        'status' => 'error',
        'message' => $e->getMessage(),
    ];

    // Mapping du code status
    $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
    
    // Masquer les détails internes en prod pour les 500
    if ($statusCode === 500 && $_ENV['APP_ENV'] === 'prod') {
        $data['message'] = 'Internal Server Error';
    }

    $event->setResponse(new JsonResponse($data, $statusCode));
}
```

## Mapping Configuration (`framework.yaml`)
On peut mapper n'importe quelle classe d'exception (même tierce) vers un code HTTP sans écrire de code.

```yaml
framework:
    exceptions:
        App\Exception\UserBannedException: { status_code: 403 }
        Symfony\Component\Serializer\Exception\NotNormalizableValueException: { status_code: 400 }
```

## 🧠 Concepts Clés
1.  **Preview en Dev** : En dev, vous voyez la stack trace. Pour voir la page d'erreur réelle (comme l'utilisateur final), utilisez les routes spéciales fournies par `_error_controller` (ou modifiez l'URL via le router de dev, ex: `/_error/404`).
2.  **Deprecation** : L'événement s'appelait `GetResponseForExceptionEvent` dans le passé. Il est renommé `ExceptionEvent`.
3.  **FlattenException** : Symfony convertit l'objet Exception PHP (complexe, récursif) en un objet `FlattenException` simple pour pouvoir le passer au template Twig sans erreurs de sérialisation.

## ⚠️ Points de vigilance (Certification)
*   **`kernel.exception` vs `kernel.view`** : `kernel.exception` n'est appelé QUE s'il y a une exception. `kernel.view` est appelé si le contrôleur retourne une donnée brute.
*   **Priorité** : Si vous écrivez un Listener d'exception, mettez une priorité élevée (ex: 10) pour passer avant le listener par défaut de Symfony, ou négative pour passer après (logger).

## Ressources
*   [Symfony Docs - Error Pages](https://symfony.com/doc/current/controller/error_pages.html)
*   [RFC 7807 - Problem Details for HTTP APIs](https://tools.ietf.org/html/rfc7807)
