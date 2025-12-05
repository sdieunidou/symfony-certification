# Component Notifier

## Concept Clé
Le composant **Notifier** permet d'envoyer des notifications via divers canaux (SMS, Chat, Email, Push Browser) de manière unifiée. Il utilise le concept de "Channels" et de "Transports".

## Concepts
*   **Notification** : L'objet contenant le message (`subject`, `content`, `emoji`).
*   **Recipient** : Le destinataire (avec email et n° de téléphone).
*   **Channel** : Le type de canal (`email`, `sms`, `chat`, `browser`, `push`).
*   **Transport** : Le service tiers utilisé (Twilio, Slack, Firebase, etc.).

## Exemple
```php
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

public function notify(NotifierInterface $notifier)
{
    $notification = (new Notification('New Invoice', ['email', 'sms']))
        ->content('You got a new invoice for 10 EUR.');

    $recipient = new Recipient(
        'user@example.com',
        '+33612345678'
    );

    $notifier->send($notification, $recipient);
}
```

## Configuration (DSN)
```env
# .env
TEXYER_DSN=twilio://SID:TOKEN@default?from=FROM
CHATTER_DSN=slack://TOKEN@default?channel=CHANNEL
```

## Ressources
*   [Symfony Docs - Notifier](https://symfony.com/doc/current/notifier.html)
