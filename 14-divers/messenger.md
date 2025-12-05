# Composant Messenger

## Concept clé
Messenger fournit un **Message Bus** qui permet d'envoyer des messages (objets PHP arbitraires) et de les traiter immédiatement (synchrone) ou plus tard (asynchrone/queue).
C'est l'implémentation du pattern CQRS (Command Query Responsibility Segregation) ou Event-Driven.

## Architecture
1.  **Message** : Un simple objet (DTO) contenant des données. `class SmsNotification { public string $content; }`
2.  **Handler** : Le service qui exécute la logique. `class SmsNotificationHandler { __invoke(SmsNotification $msg) }`
3.  **Bus** : Le dispatcher. On lui donne le message, il trouve le bon Handler.
4.  **Transport** (Optionnel) : Le canal de communication (RabbitMQ, Redis, Doctrine DB) pour l'asynchrone.
5.  **Worker** : Le processus CLI qui lit les messages du Transport et appelle le Handler.

## Application dans Symfony 7.0

### Dispatch
```php
public function index(MessageBusInterface $bus): Response
{
    $bus->dispatch(new SmsNotification('Hello!'));
    return new Response('Message envoyé (ou mis en file d\'attente)');
}
```

### Configuration (Routing)
On décide dans `messenger.yaml` si un message est traité tout de suite ou envoyé dans une file.

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%' # ex: doctrine://default
        routing:
            # Les messages SmsNotification vont dans le transport 'async'
            'App\Message\SmsNotification': async
```

### Consommation
Lancer un worker en ligne de commande (supervisé par Supervisor/Systemd).
```bash
php bin/console messenger:consume async
```

## 🧠 Concepts Clés
1.  **Middleware** : Le Bus est composé de middlewares (comme HTTP). On peut ajouter du Logging, de la Validation, ou de la Transaction DB autour du traitement du message.
2.  **Stamp** : On peut ajouter des métadonnées au message (Enveloppe) sans modifier l'objet message lui-même (ex: `DelayStamp` pour différer l'exécution, `HandledStamp` pour récupérer le résultat en synchrone).

## ⚠️ Points de vigilance (Certification)
*   **Séquentiel** : Par défaut, un worker traite les messages un par un.
*   **Retries** : Messenger gère nativement les échecs. Si un handler lance une exception, le message est rejoué X fois (config `retry_strategy`) avant d'aller dans une file d'échec (`failure_transport`).

## Ressources
*   [Symfony Docs - Messenger](https://symfony.com/doc/current/messenger.html)
