# Composants Mime et Mailer

## Concept clé
*   **Mime** : Création de messages email (structure, headers, attachments, HTML/Text).
*   **Mailer** : Envoi du message via un transport (SMTP, API tierce comme SendGrid/Mailgun).

## Application dans Symfony 7.0

```php
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;

public function send(MailerInterface $mailer): void
{
    $email = (new Email())
        ->from('hello@example.com')
        ->to('you@example.com')
        ->subject('Time for Symfony Mailer!')
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML emails</p>');

    $mailer->send($email);
}
```

### TemplatedEmail
Pour utiliser Twig dans les emails, on utilise `Symfony\Bridge\Twig\Mime\TemplatedEmail` qui ajoute la méthode `htmlTemplate()`.

## Points de vigilance (Certification)
*   **DSN** : La configuration se fait via une URL DSN : `MAILER_DSN=smtp://user:pass@smtp.example.com:25`.
*   **Async** : Si Messenger est installé, l'envoi d'email est asynchrone par défaut (le Mailer dispatche un message Messenger au lieu d'envoyer directement).

## Ressources
*   [Symfony Docs - Mailer](https://symfony.com/doc/current/mailer.html)
*   [Symfony Docs - Mime](https://symfony.com/doc/current/components/mime.html)

