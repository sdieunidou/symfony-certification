# Tags de Service

## Concept clé
Les tags sont des étiquettes qu'on colle sur des services pour dire au framework : "Ce service est spécial, utilise-le".
Exemple : Tous les services tagués `twig.extension` sont chargés par Twig.

## Application dans Symfony 7.0
Grâce à `autoconfigure: true`, la plupart des tags sont ajoutés automatiquement si votre classe implémente une certaine interface.

*   `EventSubscriberInterface` -> `kernel.event_subscriber`
*   `Command` -> `console.command`
*   `ConstraintValidatorInterface` -> `validator.constraint_validator`

### Tag manuel (YAML)
```yaml
App\EventListener\MyListener:
    tags:
        - { name: 'kernel.event_listener', event: 'kernel.request' }
```

### Tag manuel (Attribut PHP)
```php
#[AsTaggedItem('my_tag')]
class MyService {}
```

## Points de vigilance (Certification)
*   **Injection** : On peut injecter tous les services ayant un tag spécifique dans un autre service (souvent un "Registry" ou "Chain").
    ```php
    public function __construct(
        #[TaggedIterator('app.handler')] iterable $handlers
    ) {}
    ```

## Ressources
*   [Symfony Docs - Service Tags](https://symfony.com/doc/current/service_container/tags.html)

