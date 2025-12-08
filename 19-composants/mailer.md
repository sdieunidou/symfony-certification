# Le Composant Mailer

Le composant **Mailer** permet de créer et d'envoyer des emails. Il remplace l'historique SwiftMailer depuis Symfony 4.3 en apportant une architecture plus modulaire, le support natif de l'asynchrone, et une intégration poussée avec Twig et CSS Inliner.

## 1. Concepts Fondamentaux

L'envoi d'un email se décompose en deux parties distinctes :
1.  **Mime Message** : La structure de l'email (Sujet, Corps, Pièces jointes, Headers).
2.  **Transport** : Le mécanisme de livraison (SMTP, API SendGrid, Mailgun, Amazon SES, etc.).

---

## 2. Création d'Emails

### Email Standard (`Symfony\Component\Mime\Email`)
```php
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;

$email = (new Email())
    ->from(new Address('no-reply@example.com', 'Mon App'))
    ->to('user@example.com')
    // ->cc(), ->bcc(), ->replyTo(), ->priority()
    ->subject('Bienvenue !')
    ->text('Version texte brut pour les clients sans HTML')
    ->html('<h1>Bienvenue</h1><p>...</p>')
    ->attachFromPath('/path/to/contract.pdf');
```

### Email Templaté (`Symfony\Bridge\Twig\Mime\TemplatedEmail`)
C'est la méthode recommandée pour les applications Symfony. Elle utilise Twig pour le rendu.

```php
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

$email = (new TemplatedEmail())
    ->from('no-reply@example.com')
    ->to(new Address($user->getEmail(), $user->getName()))
    ->subject('Votre commande')
    
    // Chemin vers le template Twig
    ->htmlTemplate('emails/order_confirmation.html.twig')
    
    // Variables passées au template
    ->context([
        'user' => $user,
        'order' => $order,
    ]);
```

#### Inlining CSS
Avec le package `twig/cssinliner-extra` (`composer require twig/cssinliner-extra`), vous pouvez écrire du CSS dans une balise `<style>` ou lier un fichier CSS, et il sera automatiquement converti en attributs `style="..."` sur chaque balise HTML, car les clients mails (Gmail, Outlook) supportent mal les feuilles de style externes.

#### Intégration d'images (Twig)
```twig
{# Référence une image locale via un namespace Twig #}
<img src="{{ email.image('@images/logo.png') }}" alt="Logo">
```
Cela embarque l'image directement dans l'email (CID attachment) pour éviter le blocage des images externes.

---

## 3. Configuration des Transports

La configuration se fait via la variable d'environnement `MAILER_DSN`.

### Exemples de DSN
*   **SMTP** : `smtp://user:pass@smtp.example.com:25`
*   **Services Tiers (API)** : `sendgrid://API_KEY@default` (nécessite `symfony/sendgrid-mailer`)
*   **Local (Dev)** : `null://null` (ne rien envoyer) ou `native://default` (sendmail).
*   **Mailpit / MailHog** : `smtp://localhost:1025`

### Options TLS (DSN)
*   `verify_peer=0` : Désactiver la vérification du certificat (Dev uniquement).
*   `auto_tls=false` : Désactiver le STARTTLS automatique.
*   `require_tls=true` : Forcer le TLS.

### Haute Disponibilité (Failover & Load Balancing)
Symfony supporte nativement ces stratégies dans le DSN.

```env
# Failover : Essaie le premier, si échec, essaie le second (retry_period configurable)
MAILER_DSN="failover(sendgrid://... postmark://...)?retry_period=60"

# Round Robin : Alterne entre les transports (répartition de charge)
MAILER_DSN="roundrobin(sendgrid://... postmark://...)"
```

---

## 4. Fonctionnalités Avancées

### Signature et Chiffrement (S/MIME)
Pour sécuriser vos emails (intégrité et confidentialité).

```php
use Symfony\Component\Mime\Crypto\SMimeSigner;
use Symfony\Component\Mime\Crypto\SMimeEncrypter;

// Signature
$signer = new SMimeSigner('/path/to/cert.crt', '/path/to/key.key');
$signedEmail = $signer->sign($email);

// Chiffrement
$encrypter = new SMimeEncrypter('/path/to/recipient.crt');
$encryptedEmail = $encrypter->encrypt($email);
```
*Note : Peut aussi être configuré globalement dans `mailer.yaml`.*

### Tags & Métadonnées
Certains transports tiers (Sendgrid, Mailgun...) supportent les tags pour le tracking.

```php
use Symfony\Component\Mailer\Header\TagHeader;
use Symfony\Component\Mailer\Header\MetadataHeader;

$email->getHeaders()->add(new TagHeader('password-reset'));
$email->getHeaders()->add(new MetadataHeader('ClientId', '12345'));
```

### Draft Email
Pour générer un email "Brouillon" téléchargeable (format `.eml`) sans l'envoyer.

```php
use Symfony\Component\Mime\DraftEmail;
$draft = (new DraftEmail())->html('...')->toString(); // Retourne le contenu raw
```

---

## 5. Envoi Asynchrone (Messenger)

Si le composant **Messenger** est installé (`composer require symfony/messenger`), le Mailer détecte automatiquement la configuration.

Si vous routez le message `Symfony\Component\Mailer\Messenger\SendEmailMessage` vers un transport async (ex: RabbitMQ ou Doctrine), l'appel `$mailer->send($email)` ne bloquera pas la requête. Il dispatchera simplement le message dans le bus.

```yaml
# config/packages/messenger.yaml
framework:
    messenger:
        routing:
            'Symfony\Component\Mailer\Messenger\SendEmailMessage': async
```

---

## 6. Événements et Personnalisation

Le Mailer dispatch plusieurs événements :
1.  `MessageEvent` : Avant l'envoi. Permet de modifier le message ou l'enveloppe.
2.  `SentMessageEvent` : Après l'envoi (contient les infos de debug et l'ID du message).
3.  `FailedMessageEvent` : En cas d'erreur.

**Cas d'usage : L'Interceptor en Dev**
En développement, on veut souvent intercepter tous les mails pour ne pas spammer les vrais utilisateurs.
Symfony le fait nativement via la config `envelope` listener :

```yaml
# config/packages/mailer.yaml
framework:
    mailer:
        envelope:
            recipients: ['dev@my-company.com'] # Force ce destinataire pour TOUS les mails
```

---

## Fonctionnement Interne

### Architecture
*   **Transport** : L'interface d'envoi (Smtp, Api, Null).
*   **TransportFactory** : Crée le transport à partir du DSN.
*   **Message** : Hérite de `Mime\Email` (standard RFC).

### Le Flux
1.  **Send** : `Mailer::send($email)`.
2.  **Render** : Si le message contient du Twig, le `BodyRenderer` compile le template.
3.  **Dispatch** : Un événement `MessageEvent` permet de modifier le mail avant envoi.
4.  **Transport** : Le transport délivre le message (ou l'envoie dans Messenger si configuré asynchrone).

## 7. Points de vigilance pour la Certification

*   **Envelope vs Header** : L'enveloppe SMTP (MAIL FROM / RCPT TO) peut être différente des headers MIME (From / To). Par défaut, Mailer copie les headers dans l'enveloppe, mais on peut les dissocier via `MessageEvent`.
*   **Attachments** : `attachFromPath()` (fichier disque) vs `attach()` (contenu string en mémoire).
*   **Exceptions** : `TransportExceptionInterface` est levée si l'envoi échoue (ex: serveur SMTP injoignable).
