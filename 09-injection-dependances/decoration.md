# Décoration de Service

## Concept clé
Remplacer un service existant par le vôtre pour modifier son comportement, tout en gardant la possibilité d'appeler le service original. C'est une alternative plus propre à l'héritage.

## Application dans Symfony 7.0

### YAML
```yaml
App\Mailer\MyMailer:
    decorates: 'mailer.default_transport'
    decoration_priority: 1 # Optionnel, plus haut = extérieur
    arguments: ['@.inner']
```

### Attribut PHP (Symfony 6.1+)
```php
namespace App\Mailer;

use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\Mailer\Transport\TransportInterface;

#[AsDecorator(decorates: 'mailer.default_transport')]
class MyMailer implements TransportInterface
{
    public function __construct(
        private TransportInterface $inner // Service original
    ) {}
    
    // ...
}
```

## Points de vigilance (Certification)
*   **@.inner** : Nom interne par défaut du service décoré (renommé par le conteneur).
*   **Interface** : Le décorateur doit généralement implémenter la même interface que le service décoré pour que le typage reste valide partout où le service est utilisé.

## Ressources
*   [Symfony Docs - Service Decoration](https://symfony.com/doc/current/service_container/service_decoration.html)

