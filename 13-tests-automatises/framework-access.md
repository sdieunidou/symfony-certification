# Accès aux Objets du Framework (Intégration)

## Concept clé
Dans les tests d'intégration (`KernelTestCase`) ou fonctionnels, vous interagissez avec le Kernel Symfony, le conteneur de services et l'environnement.

## Configuration de l'Environnement de Test

### BootKernel
La méthode `bootKernel()` démarre l'application. Elle est appelée automatiquement par `WebTestCase::createClient()`.
Vous pouvez passer des options pour surcharger l'environnement :

```php
self::bootKernel([
    'environment' => 'my_test_env',
    'debug'       => false, // Désactive le mode debug (plus rapide, pas de cache rebuild)
]);
```

*Astuce : En CI, il est recommandé de lancer les tests avec `debug => false` pour la performance.*

### Variables d'Environnement
Les tests utilisent le fichier `.env.test` (et `.env.test.local`).
Hiérarchie :
1.  `.env`
2.  `.env.test` (surcharge pour les tests)
3.  `.env.test.local` (spécifique machine)

Note : `.env.local` est **ignoré** en environnement de test pour assurer la cohérence.

### Configuration Spécifique
Le kernel de test charge la configuration depuis `config/packages/test/`.
Exemple : `config/packages/test/web_profiler.yaml` pour activer le profiler uniquement en test.

## Accès aux Services (`static::getContainer()`)
Une fois le kernel booté, on accède au **Test Container**.

```php
    self::bootKernel();
    $container = static::getContainer();
$service = $container->get(MyService::class);
```

### Services Privés
Le conteneur de test rend **tous** les services publics par défaut (non-removed).
Si un service privé a été supprimé (car inutilisé), vous devez le rendre public explicitement dans `config/services_test.yaml` pour le tester.

## Mocker des Dépendances
Pour remplacer un service réel par un Mock dans le conteneur :

```php
$mock = $this->createMock(NewsRepositoryInterface::class);
$mock->method('findRecent')->willReturn([...]);
    
self::bootKernel();
$container = static::getContainer();

// Injection du mock dans le conteneur
$container->set(NewsRepositoryInterface::class, $mock);

// Le service qui dépend de NewsRepositoryInterface utilisera le mock
$generator = $container->get(NewsletterGenerator::class);
```

## ⚠️ Points de vigilance (Certification)
*   **Set** : `$container->set()` ne fonctionne que sur le conteneur de test.
*   **Reset** : Le conteneur est réinitialisé à chaque requête dans un `WebTestCase`. Si vous settez un mock, il est perdu à la requête suivante (sauf si vous utilisez `disableReboot()`).
