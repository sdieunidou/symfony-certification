# Tests Unitaires (PHPUnit)

## Concept clé
Les tests unitaires vérifient la logique d'une classe en isolation totale.
*   Pas de base de données.
*   Pas de Kernel Symfony.
*   Rapidité d'exécution extrême.

## `TestCase` vs `KernelTestCase`

### 1. `TestCase` (Unitaire Pur)
À utiliser pour tester vos Services, DTOs, Utility classes, Events Listeners (s'ils ne dépendent pas de services complexes).

```php
use PHPUnit\Framework\TestCase;
use App\Service\Calculator;

class CalculatorTest extends TestCase
{
    public function testAdd(): void
    {
        $calc = new Calculator();
        $this->assertEquals(4, $calc->add(2, 2));
    }
}
```

### 2. `KernelTestCase` (Intégration)
À utiliser quand vous avez besoin du Conteneur de Services ou de la Base de Données (Repositories, Commandes).

```php
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    public function testFindActive(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repo = $container->get(UserRepository::class);
        
        // Test avec vraie DB (ou SQLite in-memory)
        $this->assertCount(5, $repo->findActiveUsers());
    }
}
```

## Mocking (Simuler les dépendances)
Dans un test unitaire, si votre service A dépend du service B, vous devez "mocker" B.

```php
// Service à tester : InvoiceGenerator(MailerInterface $mailer)

public function testGenerate(): void
{
    // Créer un mock de l'interface
    $mailerMock = $this->createMock(MailerInterface::class);
    
    // Configurer le comportement attendu
    $mailerMock->expects($this->once())
        ->method('send')
        ->with($this->isInstanceOf(Email::class));

    $generator = new InvoiceGenerator($mailerMock);
    $generator->generate(new Order());
}
```

## 🧠 Concepts Clés
1.  **Pyramide des tests** : La majorité de vos tests doivent être unitaires (rapides, précis). Les tests fonctionnels (lents, globaux) viennent en complément.
2.  **ClockMock** : Symfony fournit des outils pour mocker le temps (`ClockInterface` en Symfony 6.3+).

## ⚠️ Points de vigilance (Certification)
*   **BootKernel** : `KernelTestCase` nécessite `self::bootKernel()`. `WebTestCase` le fait automatiquement via `createClient()`. `TestCase` ne le fait pas (et ne peut pas le faire).

## Ressources
*   [Symfony Docs - Unit Testing](https://symfony.com/doc/current/testing.html#unit-tests)
