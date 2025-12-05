# Accès aux Objets du Framework (Services)

## Concept clé
Dans les tests d'intégration (`KernelTestCase`) ou fonctionnels (`WebTestCase`), vous avez besoin d'accéder aux services de l'application (EntityManager, Router, MonService).

## `static::getContainer()`
C'est la méthode magique. Elle retourne une instance spéciale du conteneur de test (`TestContainer`).

```php
public function testService(): void
{
    self::bootKernel();
    $container = static::getContainer();

    // Accès à un service (même privé !)
    $myService = $container->get(MyService::class);
    $result = $myService->complexCalculation();

    $this->assertEquals(42, $result);
}
```

## Pourquoi un Conteneur de Test ?
En production, les services sont privés (inaccessibles via `get()`).
Le `TestContainer` rend **tous** les services publics pour faciliter les tests.

## Mocker un Service
Parfois, on veut remplacer un vrai service (ex: StripeClient) par un faux dans le conteneur pour les tests fonctionnels.

```php
public function testPayment(): void
{
    $client = static::createClient();
    
    // Créer un mock
    $mockStripe = $this->createMock(StripeClient::class);
    $mockStripe->method('charge')->willReturn(true);

    // Remplacer le service dans le conteneur
    // Note: Cela ne marche que si le service n'a pas encore été utilisé/instancié
    self::getContainer()->set(StripeClient::class, $mockStripe);

    $client->request('POST', '/pay');
}
```

## 🧠 Concepts Clés
1.  **Client Container** : `$client->getContainer()` existe aussi mais est déprécié ou limité. Préférez toujours `static::getContainer()`.
2.  **Persistance** : Le conteneur est recréé à chaque `request()` du client. Si vous remplacez un service (`set`), il sera perdu à la prochaine requête.

## ⚠️ Points de vigilance (Certification)
*   **Boot** : Il faut impérativement que le kernel soit booté (`self::bootKernel()` ou `createClient()`) avant de demander le conteneur.

## Ressources
*   [Symfony Docs - Container in Tests](https://symfony.com/doc/current/testing.html#accessing-the-container)
