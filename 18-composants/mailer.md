# Component Mailer

## Concept Clé
Le composant **Mailer** permet de créer et d'envoyer des emails. Il remplace SwiftMailer depuis Symfony 4.3. Il supporte de nombreux transports (SMTP, API tierces) et l'intégration avec Twig / CSS Inliner.

## Utilisation de base

```php
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

public function sendEmail(MailerInterface $mailer)
{
    $email = (new Email())
        ->from('hello@example.com')
        ->to('you@example.com')
        ->subject('Time for Symfony Mailer!')
        ->text('Sending emails is fun again!')
        ->html('<p>See Twig integration for better HTML emails!</p>');

    $mailer->send($email);
}
```

## TemplatedEmail (Twig)
```php
$email = (new TemplatedEmail())
    ->context(['username' => 'Jane'])
    ->htmlTemplate('emails/signup.html.twig');
```

## Transports (DSN)
La configuration se fait via une chaîne DSN (Data Source Name) dans `.env`.
*   `MAILER_DSN=smtp://user:pass@smtp.example.com:25`
*   `MAILER_DSN=sendgrid://KEY@default`
*   `MAILER_DSN=native://default` (sendmail local)

## Fonctionnalités
*   **Asynchrone** : Intégration native avec Messenger pour ne pas bloquer la requête.
*   **Events** : `MessageEvent` pour modifier le mail avant envoi.

## Ressources
*   [Symfony Docs - Mailer](https://symfony.com/doc/current/mailer.html)
