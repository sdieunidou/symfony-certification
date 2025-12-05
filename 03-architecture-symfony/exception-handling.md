# Gestion des Exceptions (Architecture)

## Concept clé
Dans une application Symfony, les exceptions ne doivent pas faire crasher le serveur (Page blanche ou Stack trace brute en prod). Elles doivent être capturées et transformées en réponse HTTP appropriée (page d'erreur 404, 500, JSON error).

## Application dans Symfony 7.0
Le `HttpKernel` capture toutes les exceptions qui remontent lors du traitement de la requête.
Il dispatche l'événement `kernel.exception`.
Un écouteur par défaut (`ErrorListener`) attrape cet événement et :
1.  Loggue l'erreur.
2.  Détermine le code de statut HTTP (ex: `NotFoundHttpException` -> 404).
3.  Délègue le rendu à un contrôleur d'erreur (`TwigBundle` fournit des templates d'erreur personnalisables).

## Exemple de code (Event Listener personnalisé)

```php
<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class JsonExceptionListener
{
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        // Si ce n'est pas une API, on laisse faire Symfony
        if ($request->getContentTypeFormat() !== 'json') {
            return;
        }

        $response = new JsonResponse([
            'error' => [
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ]
        ]);

        if ($exception instanceof HttpExceptionInterface) {
            $response->setStatusCode($exception->getStatusCode());
        } else {
            $response->setStatusCode(500);
        }

        // Important : définir la réponse dans l'événement arrête la propagation
        // et envoie cette réponse au client.
        $event->setResponse($response);
    }
}
```

## Points de vigilance (Certification)
*   **Priorité** : `kernel.exception` est déprécié au profit de `kernel.error` dans certaines versions futures, mais en 7.0 c'est toujours le mécanisme principal. Note : En fait, `ExceptionEvent` est utilisé.
*   **Hiérarchie** : `HttpExceptionInterface` permet de contrôler le code de statut et les headers. Une exception standard PHP = 500.
*   **Debugging** : En mode `APP_ENV=dev`, Symfony utilise le composant `ErrorHandler` pour afficher la belle page d'exception avec stack trace.

## Ressources
*   [Symfony Docs - How to Customize Error Pages](https://symfony.com/doc/current/controller/error_pages.html)

