# Décoration de Service

## Concept clé
Le pattern Décorateur permet de modifier ou d'étendre le comportement d'un service existant sans modifier sa classe et sans utiliser l'héritage (qui est souvent bloqué par `final` ou complexe).
Le décorateur "enveloppe" le service original.

## Application dans Symfony 7.0
L'attribut `#[AsDecorator]` est la méthode recommandée.

Exemple : On veut logger chaque envoi d'email, sans toucher au `Mailer` de Symfony.

```php
namespace App\Mailer;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Envelope;

#[AsDecorator(decorates: MailerInterface::class)] // Ou l'ID 'mailer'
class LoggableMailer implements MailerInterface
{
    public function __construct(
        private MailerInterface $inner, // Le service original (décoré)
        private LoggerInterface $logger
    ) {}

    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        $this->logger->info('Sending email...');
        
        // Délègue le travail au vrai mailer
        $this->inner->send($message, $envelope);
    }
}
```

## Configuration YAML (Alternative)
```yaml
App\Mailer\LoggableMailer:
    decorates: 'mailer'
    arguments: ['@.inner'] # @.inner référence le service décoré
```

## Priorité
Si plusieurs décorateurs s'appliquent au même service, on peut définir une priorité (`priority: 10`). Le plus haut priorité enveloppe les autres (c'est le "plus à l'extérieur", donc le premier exécuté).

## 🧠 Concepts Clés
1.  **Transparence** : Partout où `MailerInterface` (ou l'ID `mailer`) était injecté, c'est maintenant votre `LoggableMailer` qui est injecté. Le reste de l'application ne voit pas la différence.
2.  **Composition** : C'est l'application stricte du principe "Composition over Inheritance".

## ⚠️ Points de vigilance (Certification)
*   **Interface** : Le décorateur doit implémenter la même interface que le service décoré.
*   **Renommage** : En interne, le service original `mailer` est renommé (ex: `mailer.inner`) et votre service prend l'ID `mailer`.

## Ressources
*   [Symfony Docs - Decorating Services](https://symfony.com/doc/current/service_container/service_decoration.html)
