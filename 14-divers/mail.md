# Composant Mailer

## Concept clé
Envoyer des emails de manière simple, unifiée et moderne. Remplace SwiftMailer.
Il sépare la **création** du message (Mime) de son **envoi** (Transport).

## Création (`Symfony\Component\Mime\Email`)

```php
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

$email = (new Email())
    ->from(new Address('me@example.com', 'Me'))
    ->to('you@example.com')
    ->subject('Important Notification')
    ->text('Text version')
    ->html('<p>HTML version</p>')
    ->attachFromPath('/path/to/file.pdf');
```

### Emails Templatés (Twig)
Utiliser `Symfony\Bridge\Twig\Mime\TemplatedEmail`.

```php
$email = (new TemplatedEmail())
    ->from('...')
    ->to('...')
    ->htmlTemplate('emails/signup.html.twig')
    ->context([
        'username' => 'Fabien',
        'expiration_date' => new \DateTime('+7 days'),
    ]);
```
Dans le template Twig, vous pouvez utiliser l'inlining CSS automatique (via `twig/cssinliner-extra`).

## Envoi (`MailerInterface`)

```php
public function __construct(private MailerInterface $mailer) {}

public function send() {
    $this->mailer->send($email);
}
```

## Configuration (DSN)
Tout se configure via une URL dans `.env`.
*   `MAILER_DSN=smtp://user:pass@smtp.example.com:25`
*   `MAILER_DSN=sendgrid://KEY@default` (via bridge)
*   `MAILER_DSN=null://null` (ne rien envoyer, pour dev/test)

## 🧠 Concepts Clés
1.  **Asynchrone** : Si le composant **Messenger** est installé et configuré, `mailer.send()` ne fait qu'envoyer un message dans le Bus. L'email sera envoyé réellement par un Worker en arrière-plan. C'est transparent pour le développeur.
2.  **Envelope** : Le mailer distingue le message (contenu) de l'enveloppe (expéditeur/destinataire technique SMTP). Par défaut, il utilise les headers From/To du message, mais on peut surcharger l'enveloppe.

## ⚠️ Points de vigilance (Certification)
*   **Events** : Le mailer dispatche `MessageEvent` avant l'envoi. Utile pour ajouter des headers globaux ou modifier le destinataire en environnement de dev (Interceptor).

## Ressources
*   [Symfony Docs - Mailer](https://symfony.com/doc/current/mailer.html)
