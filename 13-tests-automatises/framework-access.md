# Accès aux Objets du Framework (Tests)

## Concept clé
Dans un test (Integration ou Functional), on a souvent besoin d'accéder aux services (EntityManager, Router, Custom Service).

## Application dans Symfony 7.0
Dans un `KernelTestCase` ou `WebTestCase` :

```php
self::bootKernel();
$container = static::getContainer();

// Accès à un service (même privé)
$router = $container->get('router');
$myService = $container->get(MyService::class);
```

## Points de vigilance (Certification)
*   **Client vs Container** :
    *   `static::getContainer()` : Donne le conteneur de test (accès aux services privés).
    *   `$client->getContainer()` : Donne le conteneur utilisé par le client dans la requête courante (accès limité, services souvent recréés). Préférer `static::getContainer()` pour l'injection de données ou les vérifications post-requête.
*   **Service non partagé** : Attention, le service obtenu via `$container->get()` est la même instance que celle utilisée par l'application *uniquement si le kernel n'a pas redémarré*.

## Ressources
*   [Symfony Docs - Accessing the Container](https://symfony.com/doc/current/testing.html#accessing-the-container)

