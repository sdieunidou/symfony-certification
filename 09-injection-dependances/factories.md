# Factories (Usines)

## Concept clé
Parfois, on ne peut pas instancier un service avec un simple `new Class()`. On a besoin d'appeler une méthode statique ou un autre service pour créer l'objet.

## Application dans Symfony 7.0
Utilisation de l'option `factory` dans la définition du service.

### YAML
```yaml
services:
    # 1. Factory Statique
    App\Service\MyService:
        factory: ['App\Factory\MyFactory', 'createService']
        arguments: ['some_arg']

    # 2. Factory via un autre Service
    App\Client\ApiClient:
        factory: ['@App\Factory\ApiClientFactory', 'createClient']
```

### PHP
Avec PHP 8 et l'autowiring, on utilise souvent simplement une méthode qui retourne l'objet, mais pour l'enregistrer comme service :

```php
// Factory class
class NewsletterManagerFactory
{
    public function createNewsletterManager(LoggerInterface $logger): NewsletterManager
    {
        $manager = new NewsletterManager();
        $manager->setLogger($logger);
        return $manager;
    }
}
```

## Points de vigilance (Certification)
*   **Pourquoi ?** : Utile pour intégrer des librairies tierces legacy qui utilisent des Singletons ou des constructeurs complexes.

## Ressources
*   [Symfony Docs - Service Factories](https://symfony.com/doc/current/service_container/factories.html)

