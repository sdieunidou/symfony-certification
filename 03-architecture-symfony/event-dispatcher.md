# Event Dispatcher et Événements du Noyau

## Concept clé
Le pattern Mediator/Observer permet aux composants de communiquer sans se connaître.
Un "Dispatcher" central reçoit des événements et notifie tous les "Listeners" (écouteurs) enregistrés pour cet événement.

## Application dans Symfony 7.0
Le composant `EventDispatcher` est central.
On peut enregistrer des écouteurs via :
1.  **Event Listener** : Une classe avec une méthode appelée quand l'événement survient.
2.  **Event Subscriber** : Une classe qui implémente `EventSubscriberInterface` et dit elle-même "Je veux écouter l'événement A sur la méthode X, et l'événement B sur la méthode Y".

### Événements du Kernel (Rappel)
*   `kernel.request`
*   `kernel.controller`
*   `kernel.controller_arguments`
*   `kernel.view`
*   `kernel.response`
*   `kernel.finish_request`
*   `kernel.terminate`
*   `kernel.exception`

## Exemple de code

```php
<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // L'événement, la méthode, et la priorité (optionnelle, défaut 0)
        // Plus la priorité est haute, plus il est exécuté tôt.
        return [
            KernelEvents::RESPONSE => ['addSecurityHeaders', 0],
        ];
    }

    public function addSecurityHeaders(ResponseEvent $event): void
    {
        $response = $event->getResponse();
        $response->headers->set('X-Frame-Options', 'DENY');
    }
}
```

## Points de vigilance (Certification)
*   **Listener vs Subscriber** : Le Subscriber est plus facile à réutiliser et à configurer (il porte sa propre config). Le Listener est plus flexible si on veut le configurer dynamiquement via `services.yaml`. Symfony recommande généralement les Subscribers ou les attributs `#[AsEventListener]`.
*   **Propagation** : `$event->stopPropagation()` empêche les autres écouteurs (de priorité inférieure) d'être exécutés.
*   **Immutabilité** : L'objet Event passé aux écouteurs est souvent mutable (on modifie la réponse dedans), mais le nom de l'événement est immuable.

## Ressources
*   [Symfony Docs - Events and Event Listeners](https://symfony.com/doc/current/event_dispatcher.html)

