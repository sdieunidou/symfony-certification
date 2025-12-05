# Le Composant Notifier

Le composant **Notifier** (introduit dans Symfony 5.0) fournit une interface unifiée pour envoyer des notifications aux utilisateurs via différents canaux (**SMS**, **Email**, **Chat**, **Browser Push**).

Il repose sur une architecture similaire à Mailer (Message + Transport), mais ajoute la notion d'**Importance** et de **Recipient**.

---

## 1. Concepts Fondamentaux

1.  **Notification** : Le message à envoyer (Sujet, Contenu, Importance).
2.  **Recipient** : Le destinataire (User), qui doit fournir ses coordonnées (email, téléphone) via une interface.
3.  **Channel** : Le médium de communication (SMS, Email, Chat, Browser).
4.  **Transport** : Le service technique (Twilio, Slack, Firebase, Telegram, etc.).

---

## 2. Création et Envoi

### La Notification
```php
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Recipient\Recipient;

public function sendNotification(NotifierInterface $notifier)
{
    // Créer une notification avec une importance (Urgent, High, Medium, Low)
    $notification = (new Notification('Nouvelle facture', ['email', 'sms']))
        ->content('Vous avez reçu la facture #12345 de 50€.')
        ->importance(Notification::IMPORTANCE_HIGH);

    // Définir le destinataire
    $recipient = new Recipient(
        'client@example.com',
        '+33612345678'
    );

    // Envoyer
    $notifier->send($notification, $recipient);
}
```

### L'interface `RecipientInterface`
Plutôt que de créer un `Recipient` manuellement, vos entités `User` devraient implémenter `RecipientInterface` (ou `EmailRecipientInterface` / `SmsRecipientInterface`) pour être passées directement à `send()`.

---

## 3. Configuration des Canaux (Channels Policy)

La puissance du Notifier réside dans sa capacité à choisir le canal automatiquement en fonction de l'importance du message (`policy`).

```yaml
# config/packages/notifier.yaml
framework:
    notifier:
        chatter_transports:
            slack: '%env(SLACK_DSN)%'
            telegram: '%env(TELEGRAM_DSN)%'
        
        texter_transports:
            twilio: '%env(TWILIO_DSN)%'

        channel_policy:
            # Urgent = SMS + Email + Chat
            urgent: ['sms', 'chat/slack', 'email']
            # High = SMS
            high: ['sms']
            # Medium = Email
            medium: ['email']
            # Low = Email
            low: ['email']
```

Si vous envoyez une notification `URGENT`, elle partira sur tous les canaux configurés.

---

## 4. Les Types de Messages Spécialisés

Le composant distingue deux grandes familles de messages sous le capot :

### Texter (SMS)
Pour les messages courts (SMS).
*   Interface : `TexterInterface`
*   Classe : `SmsMessage`
*   Transports : Twilio, OVH, Vonage, etc.

### Chatter (Chat)
Pour les messages riches vers des messageries instantanées.
*   Interface : `ChatterInterface`
*   Classe : `ChatMessage`
*   Transports : Slack, Discord, Telegram, Google Chat, Microsoft Teams.

On peut injecter spécifiquement `TexterInterface` ou `ChatterInterface` si on ne veut pas utiliser la logique générique de Notification.

---

## 5. Personnalisation (Semantic HTML)

Comme pour les Emails, les Notifications utilisent des thèmes pour le rendu (notamment pour le canal Email).
Le contenu de la notification supporte un subset de Markdown simple.

```php
$notification->content('Hello *World*! Voici un [lien](https://symfony.com).');
```

---

## 6. Points de vigilance pour la Certification

*   **Admin Recipient** : On peut configurer des destinataires "Admin" globaux pour recevoir les notifications système (ex: exceptions critiques).
    ```yaml
    framework:
        notifier:
            admin_recipients:
                - { email: admin@example.com, phone: +33600000000 }
    ```
    Et envoyer avec `$notifier->sendAdmin($notification)`.
*   **Browser Channels** : Supporte les "Flash Messages" comme un canal de notification (`browser`).
*   **Async** : Comme pour Mailer, Notifier s'intègre avec Messenger pour l'envoi asynchrone (Bus).
