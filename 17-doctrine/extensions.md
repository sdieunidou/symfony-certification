# Extensions Doctrine (Gedmo / Stof)

## Concept
Doctrine est extensible. La communauté a créé des extensions très populaires pour gérer des comportements récurrents :
*   **Timestampable** (createdAt, updatedAt)
*   **Sluggable** (Génération de slug automatique)
*   **Translatable** (Traductions en base)
*   **Tree** (Structures hiérarchiques / Nested Set)
*   **Loggable** (Historique des modifications)

Ces extensions sont généralement installées via le bundle `stof/doctrine-extensions-bundle`.

## Installation
```bash
composer require stof/doctrine-extensions-bundle
```
Puis activer les extensions désirées dans `config/packages/stof_doctrine_extensions.yaml` :

```yaml
stof_doctrine_extensions:
    default_locale: fr_FR
    orm:
        default:
            timestampable: true
            sluggable: true
```

## Utilisation (Attributs)
Depuis peu, la librairie `gedmo/doctrine-extensions` supporte les attributs PHP 8.

### Timestampable
```php
use Gedmo\Mapping\Annotation as Gedmo; // ou namespace Gedmo\Mapping\Annotation

class Product
{
    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'create')]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Gedmo\Timestampable(on: 'update')]
    private $updatedAt;
}
```

### Sluggable
```php
class Product
{
    #[ORM\Column(length: 128)]
    #[Gedmo\Slug(fields: ['name', 'code'])]
    private $slug;
}
```

## Extensions Natives (Doctrine)
Certaines fonctionnalités n'ont pas besoin d'extensions tierces.
Exemple : **UUID** est supporté nativement par Symfony/Doctrine sans extension.

## ⚠️ Points de vigilance (Certification)
*   **Performance** : Les extensions comme `Loggable` ou `Translatable` peuvent générer beaucoup de requêtes supplémentaires. À utiliser en connaissance de cause.
*   **Listeners** : Ces extensions fonctionnent grâce au système d'événements de Doctrine (`EventSubscriber`).

## Ressources
*   [StofDoctrineExtensionsBundle](https://symfony.com/bundles/StofDoctrineExtensionsBundle/current/index.html)
*   [Gedmo Extensions](https://github.com/doctrine-extensions/DoctrineExtensions)
