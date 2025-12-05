# Tests Unitaires (PHPUnit)

## Concept clé
Les tests unitaires vérifient le fonctionnement d'une classe isolée (Unit), sans démarrer le kernel Symfony complet. On "mocker" (simuler) les dépendances.

## Application dans Symfony 7.0
On utilise `PHPUnit\Framework\TestCase`.

```php
namespace App\Tests\Service;

use App\Service\Calculator;
use PHPUnit\Framework\TestCase;

class CalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $calculator = new Calculator();
        $result = $calculator->add(10, 20);

        $this->assertEquals(30, $result);
    }
}
```

### KernelTestCase (Tests d'intégration)
Si on a besoin des vrais services du conteneur (ex: Repository, EntityManager), on utilise `KernelTestCase`.

```php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testCount(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repo = $container->get(UserRepository::class);

        $this->assertGreaterThan(0, $repo->count([]));
    }
}
```

## Points de vigilance (Certification)
*   **Vitesse** : Les tests unitaires (TestCase) sont très rapides. Les tests KernelTestCase sont plus lents (boot kernel).
*   **Service Privé** : `static::getContainer()` est un conteneur de test spécial qui permet d'accéder aux services privés (contrairement au vrai conteneur).

## Ressources
*   [Symfony Docs - Unit Tests](https://symfony.com/doc/current/testing.html#unit-tests)

