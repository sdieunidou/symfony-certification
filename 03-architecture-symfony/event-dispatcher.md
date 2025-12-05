# Event Dispatcher et Événements du Noyau

## Concept clé
Le composant `EventDispatcher` implémente le pattern **Mediator** (ou Observer). Il permet d'étendre l'application sans modifier le code existant (Open/Closed Principle).
Des **Listeners** s'abonnent à des événements spécifiques, et le **Dispatcher** les notifie lorsque ces événements surviennent.

## Les Événements du Kernel (Chronologie)

C'est le squelette de Symfony. À connaître par cœur pour la certification.
Classe des constantes : `Symfony\Component\HttpKernel\KernelEvents`.

1.  **`kernel.request`** (`RequestEvent`)
    *   **Quand** : Tout début, avant de savoir quel contrôleur utiliser.
    *   **But** : Ajouter des infos à la Request, rediriger, gérer la maintenance, Firewall (Sécurité).
    *   **Action** : Si on set une `Response`, le reste est court-circuité jusqu'à `kernel.response`.
2.  **`kernel.controller`** (`ControllerEvent`)
    *   **Quand** : Le contrôleur (classe/méthode) a été résolu, mais pas encore exécuté.
    *   **But** : Initialiser des choses avant le contrôleur, modifier le contrôleur dynamiquement.
3.  **`kernel.controller_arguments`** (`ControllerArgumentsEvent`)
    *   **Quand** : Juste avant l'appel, les arguments ont été résolus (Autowiring, ParamConverter).
    *   **But** : Modifier les arguments passés à la méthode.
4.  **`kernel.view`** (`ViewEvent`)
    *   **Quand** : Après le contrôleur, **SI** il ne retourne PAS une `Response`.
    *   **But** : Transformer la valeur de retour (array, objet) en `Response` (HTML, JSON). Utilisé par API Platform.
5.  **`kernel.response`** (`ResponseEvent`)
    *   **Quand** : Une `Response` valide a été créée (par le contrôleur ou `kernel.view`).
    *   **But** : Modifier les headers, cookies, compresser le contenu, ajouter des logs.
6.  **`kernel.terminate`** (`TerminateEvent`)
    *   **Quand** : Après `$response->send()`.
    *   **But** : Tâches lourdes "post-response" (Emails, génération PDF asynchrone simulée).
7.  **`kernel.exception`** (`ExceptionEvent`)
    *   **Quand** : Si une Exception non attrapée survient n'importe où.
    *   **But** : Convertir l'exception en `Response` d'erreur.

## Listener vs Subscriber

### Event Listener
Une classe simple configurée dans `services.yaml` via le tag `kernel.event_listener` ou l'attribut `#[AsEventListener]`.
*   **Avantage** : Peut être attaché à n'importe quel événement via la config.
*   **Inconvénient** : La configuration est externe à la classe (sauf avec l'attribut).

### Event Subscriber
Implémente `EventSubscriberInterface::getSubscribedEvents()`.
*   **Avantage** : Connait ses propres événements. Zéro config dans `services.yaml` (autowiring + autoconfiguration suffisent).
*   **Recommandé** : Pour les bundles réutilisables et la logique métier interne.

## Exemple de Subscriber (Complet)

```php
<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MaintenanceSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private bool $isMaintenanceMode
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            // Nom event => [Méthode, Priorité]
            // Priorité haute (9999) pour passer avant le Router et la Sécurité
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return; // Ne pas bloquer les sous-requêtes (ESI, render controller)
        }

        if ($this->isMaintenanceMode) {
            // Court-circuite tout le framework
            $event->setResponse(new Response('Site en maintenance', 503));
            // $event->stopPropagation(); // Optionnel ici car setResponse suffit souvent à stopper la propagation pour le Kernel
        }
    }
}
```

## 🧠 Concepts Clés
1.  **Priorité** : Entier (`int`). Plus il est élevé, plus le listener est exécuté tôt. Défaut = 0. Intervalle commun : -255 à +255, mais peut être n'importe quel entier.
2.  **Propagation** : `$event->stopPropagation()` arrête la chaîne. Les listeners de priorité inférieure ne seront pas appelés.
3.  **Main vs Sub Request** : Toujours vérifier `$event->isMainRequest()` pour éviter d'exécuter la logique 10 fois si vous utilisez des fragments Twig `{{ render() }}`.

## ⚠️ Points de vigilance (Certification)
*   **Events Génériques** : Le composant Form et Workflow utilisent aussi l'EventDispatcher, mais avec leurs propres événements. Ce fichier se concentre sur `HttpKernel`.
*   **Ordre** : Savoir placer `Response`, `Request`, `Controller`, `Exception` sur une ligne de temps est une question fréquente.
*   **Immutabilité** : L'EventDispatcher est synchrone par défaut. Le code est bloquant. Pour l'asynchrone, utiliser le composant **Messenger**.

## Ressources
*   [Symfony Docs - Built-in Symfony Events](https://symfony.com/doc/current/reference/events.html)
*   [EventDispatcher Component](https://symfony.com/doc/current/components/event_dispatcher.html)
