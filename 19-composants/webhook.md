# Composant Webhook

## Concept clé
Le composant **Webhook** permet à votre application Symfony de recevoir et traiter des webhooks (notifications HTTP entrantes) provenant de services tiers (Mailer, Notifier, ou custom).
Il gère la sécurité (vérification de signature), le parsing des requêtes et le dispatching d'événements via le composant **RemoteEvent**.

## Installation
```bash
composer require symfony/webhook
```

## Usage avec Mailer (Email Events)
Permet de recevoir des événements comme "Email Delivered", "Bounced", "Opened" depuis des fournisseurs comme Sendgrid, Mailgun, Brevo, etc.

### 1. Configuration
Exemple avec Mailgun :
```yaml
framework:
    webhook:
        routing:
            mailer_mailgun:
                service: 'mailer.webhook.request_parser.mailgun'
                secret: '%env(MAILER_MAILGUN_SECRET)%'
```
L'URL de webhook à configurer chez le provider sera : `https://votresite.com/webhook/mailer_mailgun`.

### 2. Consommation (RemoteEvent)
On crée une classe qui implémente `ConsumerInterface` avec l'attribut `#[AsRemoteEventConsumer]`.

```php
use Symfony\Component\RemoteEvent\Attribute\AsRemoteEventConsumer;
use Symfony\Component\RemoteEvent\Consumer\ConsumerInterface;
use Symfony\Component\RemoteEvent\RemoteEvent;
use Symfony\Component\RemoteEvent\Event\Mailer\MailerDeliveryEvent;
use Symfony\Component\RemoteEvent\Event\Mailer\MailerEngagementEvent;

#[AsRemoteEventConsumer('mailer_mailgun')]
class WebhookListener implements ConsumerInterface
{
    public function consume(RemoteEvent $event): void
    {
        if ($event instanceof MailerDeliveryEvent) {
            // Traiter la livraison (ex: mise à jour statut en base)
        } elseif ($event instanceof MailerEngagementEvent) {
            // Traiter l'engagement (ex: log ouverture)
        }
    }
}
```

### Providers supportés (Mailer)
Brevo, Mailgun, Mailjet, Postmark, Sendgrid, MailerSend, Resend, Mandrill, etc.

## Usage avec Notifier (SMS)
Similaire au Mailer, mais pour les SMS (Twilio, Vonage, etc.).

```php
use Symfony\Component\RemoteEvent\Event\Sms\SmsEvent;

#[AsRemoteEventConsumer('notifier_twilio')]
class SmsWebhookListener implements ConsumerInterface
{
    public function consume(RemoteEvent $event): void
    {
        if ($event instanceof SmsEvent) {
            // Traiter le statut du SMS
        }
    }
}
```

## Création de Webhook Custom
Vous pouvez créer vos propres webhooks pour n'importe quel service (ex: Stripe, GitHub).

Depuis MakerBundle 1.58 :
```bash
php bin/console make:webhook
```
Cela génère :
1.  Un **Request Parser** (pour valider la signature et transformer la Request en RemoteEvent).
2.  Un **Consumer** (pour traiter l'événement).

## Fonctionnement Interne

### Architecture
*   **RequestParser** : Extrait la payload de la requête entrante (JSON, Form).
*   **RemoteEvent** : Un objet normalisé qui représente l'événement (indépendant du fournisseur Mailgun, Stripe, etc.).
*   **Consumer** : Dispatch l'événement dans le système (souvent vers Messenger).

### Le Flux
1.  **Auth** : Vérifie la signature du webhook (Secret Key) pour s'assurer qu'il vient bien du fournisseur déclaré.
2.  **Parse** : Transforme le JSON propriétaire (ex: GitHub Payload) en objet `RemoteEvent` (name, id, payload).
3.  **Map** : Mappe le nom de l'événement (`push`) vers une classe de message ou un Event Symfony.

## 🧠 Concepts Clés
1.  **Sécurité** : Le composant gère la validation cryptographique des signatures (via le secret configuré) avant même d'appeler votre code.
2.  **Routing** : Le `type` dans le routing (`mailer_mailgun`) sert de clé pour lier l'URL entrante au bon parser et au bon consumer.

## Ressources
*   [Symfony Docs - Webhook](https://symfony.com/doc/current/webhook.html)

