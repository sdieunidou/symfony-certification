# Composant Messenger

## Concept clé
Pattern "Message Bus" pour envoyer des messages (Commandes, Événements) et les traiter de manière synchrone ou asynchrone (Worker/Queue).

## Application dans Symfony 7.0
1.  **Message** : Simple classe PHP (DTO). `class SendEmail { ... }`.
2.  **Handler** : Service qui traite le message. `class SendEmailHandler { __invoke(SendEmail $msg) }`.
3.  **Bus** : Dispatche le message. `$bus->dispatch(new SendEmail(...))`.
4.  **Transport** : Configure comment le message est envoyé (Sync, Doctrine DB, RabbitMQ, Redis).

```yaml
framework:
    messenger:
        transports:
            async: '%env(MESSENGER_TRANSPORT_DSN)%'
        routing:
            'App\Message\SendEmail': async
```

## Points de vigilance (Certification)
*   **Worker** : Pour consommer les messages asynchrones, il faut lancer un worker : `php bin/console messenger:consume async`.
*   **Failed** : Les messages échoués vont dans un transport `failed` pour être rejoués plus tard.

## Ressources
*   [Symfony Docs - Messenger](https://symfony.com/doc/current/messenger.html)

