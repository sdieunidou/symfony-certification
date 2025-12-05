# Component Messenger

## Concept Clé
Le composant **Messenger** permet d'envoyer et de recevoir des messages (objets) vers/depuis d'autres applications ou via des files d'attente (Queues). Il implémente le pattern **Command Bus**.

## Concepts
*   **Message** : Un objet PHP simple (DTO) qui contient des données.
*   **Bus** : Dispatch le message.
*   **Handler** : Reçoit le message et effectue le travail.
*   **Transport** : Le moyen de transport (Sync, Doctrine, AMQP, Redis, SQS).
*   **Worker** : Processus qui consomme les messages asynchrones.

## Workflow
1.  **Dispatch** : `$bus->dispatch(new SmsNotification('Contenu'));`
2.  **Routing** : La config détermine si le message est synchrone ou async.
3.  **Transport** : Si async, le message est sérialisé et envoyé dans une file (ex: RabbitMQ).
4.  **Consume** : `php bin/console messenger:consume` lit la file.
5.  **Handle** : Le Handler exécute la logique.

## Exemple
```php
// Message
class SmsNotification {
    public function __construct(public string $content) {}
}

// Handler
#[AsMessageHandler]
class SmsNotificationHandler {
    public function __invoke(SmsNotification $message) {
        // ... send SMS
    }
}
```

## Ressources
*   [Symfony Docs - Messenger](https://symfony.com/doc/current/messenger.html)
