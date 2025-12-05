# Factories (Usines)

## Concept clé
Une Factory est utilisée pour créer des services qui nécessitent une logique d'instanciation complexe (calculs, conditions) ou qui proviennent de bibliothèques tierces non conçues pour l'injection de dépendances (legacy code, static constructors).

## Application dans Symfony 7.0

### 1. Factory en PHP (Recommandé)
Avec l'autowiring, si vous créez une méthode qui retourne un objet, Symfony peut l'utiliser comme factory.

```php
namespace App\Factory;

class PaymentClientFactory
{
    public function __construct(private string $apiKey) {}

    public function create(): PaymentClient
    {
        // Logique complexe d'initialisation
        $client = new PaymentClient();
        $client->authenticate($this->apiKey);
        return $client;
    }
}
```

Configuration `services.yaml` pour dire que `PaymentClient` vient de l'usine :

```yaml
services:
    # Enregistre la factory
    App\Factory\PaymentClientFactory: ~

    # Enregistre le service produit
    App\Lib\PaymentClient:
        factory: ['@App\Factory\PaymentClientFactory', 'create']
```

### 2. Static Factory
Si la méthode de création est statique.

```yaml
App\Service\MyService:
    factory: ['App\Service\MyService', 'createStatic']
```

## 🧠 Concepts Clés
1.  **Découplage** : La factory encapsule la complexité de la création. Le code consommateur ne voit que le service final prêt à l'emploi.
2.  **Lazy** : La méthode de la factory n'est appelée que lorsque le service est réellement demandé.

## ⚠️ Points de vigilance (Certification)
*   **Arguments** : On peut passer des arguments à la méthode de la factory via la clé `arguments` dans le YAML, ou via l'autowiring si la méthode factory a des arguments typés.

## Ressources
*   [Symfony Docs - Factories](https://symfony.com/doc/current/service_container/factories.html)
