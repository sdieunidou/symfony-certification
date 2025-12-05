# Service Locators

## Concept clé
Un Service Locator est un conteneur restreint qui ne donne accès qu'à une liste spécifique de services.
C'est une alternative au "Container Injection" (anti-pattern où on injecte tout le conteneur) pour les cas où on a besoin de récupérer des services dynamiquement (au runtime).

## Cas d'usage
*   Choisir un "Handler" parmi une liste selon une clé (Pattern Strategy).
*   Contrôleurs (pour accéder aux helpers optionnels).

## Création et Injection

### Via Attribut `#[MapDecorated]` ou injection manuelle
Le moyen le plus courant est d'injecter un itérable tagué et de le transformer en Locator, ou de définir explicitement le locator dans `services.yaml`.

```yaml
# services.yaml
App\Handler\PaymentHandlerLocator:
    arguments:
        # Crée un ServiceLocator contenant ces 2 services
        - !service_locator
            paypal: '@App\Handler\PaypalHandler'
            stripe: '@App\Handler\StripeHandler'
```

### Via `ServiceSubscriberInterface`
Si une classe implémente cette interface, Symfony crée automatiquement un Service Locator pour elle contenant les services retournés par `getSubscribedServices()`. C'est ce qu'utilise `AbstractController`.

```php
class MyService implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator // C'est le locator, pas le conteneur global
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            'logger' => LoggerInterface::class,
            'router' => RouterInterface::class,
        ];
    }

    public function doSomething()
    {
        // OK
        $this->locator->get('logger')->info('...');
        
        // Erreur (service non souscrit)
        $this->locator->get('mailer'); 
    }
}
```

## 🧠 Concepts Clés
1.  **Lazy Loading** : Les services référencés dans un Service Locator ne sont instanciés **que** lorsqu'on appelle `$locator->get('id')`. C'est très performant pour les listes de handlers dont on n'utilise qu'un seul élément.
2.  **PSR-11** : Les Service Locators implémentent `Psr\Container\ContainerInterface`.

## ⚠️ Points de vigilance (Certification)
*   **Différence** : Ne confondez pas l'injection de dépendance (le service est prêt dans le constructeur) et le Service Locator (on va chercher le service quand on en a besoin). L'injection est préférée, sauf si on ne sait pas à l'avance de quel service on aura besoin.

## Ressources
*   [Symfony Docs - Service Locators](https://symfony.com/doc/current/service_container/service_locators.html)
