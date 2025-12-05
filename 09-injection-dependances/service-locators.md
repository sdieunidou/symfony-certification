# Service Locators

## Concept clé
Un Service Locator est un "mini-conteneur" qui donne accès à une liste restreinte de services.
C'est une alternative propre à l'injection de tout le conteneur (qui est anti-pattern).

## Application dans Symfony 7.0
Symfony génère des Service Locators automatiquement pour :
*   Les contrôleurs (`AbstractController` utilise un locator pour `router`, `twig`, etc.).
*   Les Lazy Services.

### Création manuelle
Pour éviter d'injecter 50 services dans un constructeur, on peut injecter un Locator qui contient ces services (chargés uniquement si appelés).

```yaml
App\Handler\HandlerCollection:
    arguments:
        - !service_locator
            handler_a: '@App\Handler\HandlerA'
            handler_b: '@App\Handler\HandlerB'
```

```php
public function __construct(ServiceProviderInterface $locator)
{
    $handler = $locator->get('handler_a');
}
```

## Points de vigilance (Certification)
*   **PSR-11** : Les Service Locators implémentent `Psr\Container\ContainerInterface` (ou `ServiceProviderInterface` qui étend PSR-11).
*   **Lazy** : L'avantage principal est que les services à l'intérieur du locator ne sont instanciés que si on appelle `get()`.

## Ressources
*   [Symfony Docs - Service Locators](https://symfony.com/doc/current/service_container/service_locators.html)

