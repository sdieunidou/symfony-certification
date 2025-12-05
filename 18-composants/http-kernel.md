# Component HttpKernel

## Concept Clé
Le composant **HttpKernel** gère la conversion d'une `Request` en `Response`. Il est le cœur du framework Symfony et orchestre le processus de traitement des requêtes via un système d'événements.

## Fonctionnement
Le `HttpKernel::handle()` suit un flux précis :
1.  **Request** : Entrée.
2.  **Events** : Dispatch d'événements à chaque étape clé (`kernel.request`, `kernel.controller`, `kernel.response`, etc.).
3.  **Controller** : Exécution de la logique métier.
4.  **Response** : Sortie.

## Événements Clés
*   `kernel.request` : Avant le routage (ex: Firewall, Locale).
*   `kernel.controller` : Après le routage, avant l'exécution (ex: initialisation).
*   `kernel.controller_arguments` : Résolution des arguments du contrôleur.
*   `kernel.view` : Si le contrôleur ne retourne pas une Response (ex: transformation d'un array en JSON).
*   `kernel.response` : Juste avant d'envoyer la réponse (ex: headers de cache, compression).
*   `kernel.terminate` : Après l'envoi de la réponse (tâches lourdes).
*   `kernel.exception` : En cas d'erreur non attrapée.

## Utilisation Autonome
```php
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;

$dispatcher = new EventDispatcher();
$kernel = new HttpKernel($dispatcher, new ControllerResolver(), new RequestStack(), new ArgumentResolver());

$response = $kernel->handle(new Request());
$response->send();
```

## Ressources
*   [Symfony Docs - HttpKernel](https://symfony.com/doc/current/components/http_kernel.html)
