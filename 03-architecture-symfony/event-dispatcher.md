# Event Dispatcher et Événements du Noyau

## Concept clé
Le composant `EventDispatcher` implémente le pattern **Mediator** (ou Observer). Il permet d'étendre l'application sans modifier le code existant (Open/Closed Principle).
Des **Listeners** ou **Subscribers** s'abonnent à des événements spécifiques, et le **Dispatcher** les notifie (exécute leur code) lorsque ces événements surviennent.

## Listener vs Subscriber

### Event Listener
Une classe indépendante qu'on "attache" à un événement via la configuration.
*   **Attribut PHP 8** : La méthode recommandée depuis Symfony 6.
    ```php
    #[AsEventListener(event: KernelEvents::EXCEPTION, priority: 10)]
    public function onException(ExceptionEvent $event): void { ... }
    ```
*   **Configuration YAML** : Via le tag `kernel.event_listener`.
*   **Avantage** : Plus flexible (activable/désactivable conditionnellement via config).

### Event Subscriber
Une classe qui "sait" ce qu'elle écoute en implémentant `EventSubscriberInterface`.
*   Doit implémenter `getSubscribedEvents()`.
*   **Avantage** : Autonome, facile à réutiliser et partager entre projets. Symfony l'enregistre automatiquement sans config.

## Création d'un Event Custom
Pour découpler votre propre code, vous pouvez dispatcher vos événements.

1.  **Créer la classe d'Événement** (Optionnel depuis Symfony 5+, mais recommandé pour typer les données).
    Elle hérite généralement de `Symfony\Contracts\EventDispatcher\Event`.
    ```php
    class UserRegisteredEvent extends Event
    {
        public function __construct(private User $user) {}
        public function getUser(): User { return $this->user; }
    }
    ```

2.  **Dispatcher l'événement**
    ```php
    public function register(EventDispatcherInterface $dispatcher)
    {
        // ... user created
        $event = new UserRegisteredEvent($user);
        $dispatcher->dispatch($event, 'user.registered');
    }
    ```

## Les Événements du Kernel (Chronologie)

C'est le squelette de Symfony. À connaître par cœur pour la certification.
Classe des constantes : `Symfony\Component\HttpKernel\KernelEvents`.

1.  **`kernel.request`** (`RequestEvent`)
    *   **Quand** : Tout début, avant de savoir quel contrôleur utiliser.
    *   **But** : Ajouter des infos à la Request, rediriger, gérer la maintenance, Firewall (Sécurité).
    *   **Action** : Si on set une `Response` (`$event->setResponse()`), le reste est court-circuité jusqu'à `kernel.response`.

2.  **`kernel.controller`** (`ControllerEvent`)
    *   **Quand** : Le contrôleur (classe/méthode) a été résolu, mais pas encore exécuté.
    *   **But** : Initialiser des choses avant le contrôleur, changer le contrôleur à la volée.
    *   **Use Case** : Vérifier des annotations custom sur le contrôleur (Before Filter).

3.  **`kernel.controller_arguments`** (`ControllerArgumentsEvent`)
    *   **Quand** : Juste avant l'appel, les arguments ont été résolus (Autowiring, ParamConverter).
    *   **But** : Modifier les arguments passés à la méthode.

4.  **`kernel.view`** (`ViewEvent`)
    *   **Quand** : Après le contrôleur, **SI** il ne retourne PAS une `Response` (ex: array, null).
    *   **But** : Transformer la valeur de retour brute en `Response` (HTML, JSON). Utilisé par API Platform ou `@Template`.

5.  **`kernel.response`** (`ResponseEvent`)
    *   **Quand** : Une `Response` valide a été créée (par le contrôleur ou `kernel.view`).
    *   **But** : Modifier les headers, cookies, compresser le contenu, ajouter des logs. (After Filter).

6.  **`kernel.terminate`** (`TerminateEvent`)
    *   **Quand** : Après `$response->send()`. La réponse est déjà partie chez le client.
    *   **But** : Tâches lourdes "post-response" qui ne doivent pas faire attendre l'utilisateur (Emails, logs intensifs). Attention, sur PHP-FPM, cela garde le processus actif.

7.  **`kernel.exception`** (`ExceptionEvent`)
    *   **Quand** : Si une Exception non attrapée survient n'importe où.
    *   **But** : Convertir l'exception en `Response` d'erreur personnalisée. Si un listener set une Response, l'exception est considérée comme "gérée".

## Debugging
Utilisez la console pour voir qui écoute quoi et dans quel ordre.
```bash
# Lister tous les events
php bin/console debug:event-dispatcher

# Voir les listeners d'un event précis
php bin/console debug:event-dispatcher kernel.request
```

## 🧠 Concepts Clés
1.  **Priorité** : Entier (`int`). Plus il est élevé, plus le listener est exécuté tôt. Défaut = 0.
2.  **Propagation** : `$event->stopPropagation()` arrête la chaîne. Les listeners de priorité inférieure ne seront pas appelés.
3.  **Main vs Sub Request** : Toujours vérifier `$event->isMainRequest()` pour éviter d'exécuter la logique pour les sous-requêtes (ESI, `{{ render() }}`).
4.  **Event Aliases** : On peut s'abonner via le nom de la classe de l'événement (`UserRegisteredEvent::class`) au lieu du nom string (`'user.registered'`). C'est recommandé.

## ⚠️ Points de vigilance (Certification)
*   **Hook de méthodes** : On peut utiliser l'EventDispatcher pour créer des hooks `pre_foo` et `post_foo` dans ses propres services sans héritage.
*   **Arguments de Controller** : `kernel.controller` reçoit le callable contrôleur. Attention, si c'est une classe invokable ou un tableau `[$obj, 'method']`.

## Ressources
*   [Symfony Docs - Events and Event Listeners](https://symfony.com/doc/current/event_dispatcher.html)
*   [Symfony Docs - Built-in Symfony Events](https://symfony.com/doc/current/reference/events.html)
