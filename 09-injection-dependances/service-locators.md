# Service Subscribers & Locators

## Concept clé
Un Service Locator est un "mini-conteneur" (PSR-11) qui donne accès uniquement à une liste définie de services. Les services à l'intérieur ne sont instanciés **que lorsqu'on les demande** (Lazy Loading).

C'est idéal pour :
*   Les **Command Bus** / **Handler Maps** (choisir le bon handler parmi 50 au runtime).
*   Les **Contrôleurs** (ne charger que les services utilisés par l'action courante).

## 1. Création via Attributs (Moderne)

### `#[AutowireLocator]`
Permet d'injecter un Locator directement dans le constructeur.

```php
use Symfony\Component\DependencyInjection\Attribute\AutowireLocator;
use Psr\Container\ContainerInterface;

class CommandBus
{
    public function __construct(
        // Crée un locator contenant uniquement ces services
        #[AutowireLocator([
            'foo' => FooHandler::class,
            'bar' => BarHandler::class,
        ])]
        private ContainerInterface $locator
    ) {}
}
```

On peut aussi créer un locator à partir d'un **Tag** :
```php
#[AutowireLocator('app.handler')] // Locator de tous les services avec ce tag
```

### Options d'indexation
Comme pour `TaggedIterator`, on peut indexer les services du locator :
```php
#[AutowireLocator('app.handler', indexAttribute: 'key', defaultIndexMethod: 'getDefaultKey')]
```

## 2. Service Subscribers
C'est le mécanisme utilisé par `AbstractController`. Votre classe implémente `ServiceSubscriberInterface` pour déclarer ce dont elle a besoin. Symfony injecte alors un locator "magique" contenant ces services.

```php
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Psr\Container\ContainerInterface;

class MyService implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $locator
    ) {}

    public static function getSubscribedServices(): array
    {
        return [
            'logger' => LoggerInterface::class,
            'router' => '?'.RouterInterface::class, // ? = Optionnel
        ];
    }
}
```

### Le Trait `ServiceMethodsSubscriberTrait` (Symfony 7.1+)
Simplifie grandement l'écriture des subscribers en se basant sur les méthodes privées annotées.

```php
use Symfony\Contracts\Service\ServiceMethodsSubscriberTrait;
use Symfony\Contracts\Service\Attribute\SubscribedService;

class MyService implements ServiceSubscriberInterface
{
    use ServiceMethodsSubscriberTrait;

    public function doWork()
    {
        // Appelle la méthode qui va chercher le service dans le locator injecté
        $this->router()->generate('...');
    }

    #[SubscribedService]
    private function router(): RouterInterface
    {
        // __METHOD__ est utilisé comme ID interne dans le locator
        return $this->container->get(__METHOD__);
    }
}
```

## 3. Configuration YAML
Pour les cas complexes ou sans autowiring.

```yaml
services:
    App\CommandBus:
        arguments:
            - !service_locator
                key1: '@service_id_1'
                key2: '@service_id_2'
```

Ou en définissant un service standalone :
```yaml
services:
    app.my_locator:
        class: Symfony\Component\DependencyInjection\ServiceLocator
        arguments:
            - { key1: '@service_1', key2: '@service_2' }
        tags: ['container.service_locator']
```

## 4. ServiceCollectionInterface (Symfony 7.1)
Si vous type-hintez `ServiceCollectionInterface` au lieu de `ContainerInterface`, vous gagnez la capacité d'itérer et de compter les services du locator.

```php
use Symfony\Component\DependencyInjection\ServiceCollectionInterface;

public function __construct(
    #[AutowireLocator('app.plugin')]
    private ServiceCollectionInterface $plugins
) {
    $count = count($this->plugins);
    foreach ($this->plugins as $id => $plugin) { ... }
}
```

## 🧠 Concepts Clés
1.  **Lazy** : C'est la différence majeure avec `AutowireIterator`. L'itérateur instancie le service dès qu'on passe dessus dans la boucle. Le Locator n'instancie le service que si on fait `get()`.
2.  **Performance** : Indispensable si vous avez des centaines de services potentiels mais que vous n'en utilisez qu'un seul par requête.

## ⚠️ Points de vigilance (Certification)
*   **Visibilité** : Les services n'ont pas besoin d'être publics pour être dans un locator.
*   **Subscribers** : `getSubscribedServices` est statique.
*   **Trait** : Avant Symfony 7.1, on utilisait `ServiceSubscriberTrait`. Le nouveau `ServiceMethodsSubscriberTrait` est plus flexible.

## Ressources
*   [Symfony Docs - Service Subscribers & Locators](https://symfony.com/doc/current/service_container/service_subscribers_locators.html)
