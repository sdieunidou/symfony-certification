# PHPUnit : Les Fondamentaux pour Symfony

## Concept Clé
PHPUnit est le standard de facto pour les tests unitaires en PHP. Bien que Symfony fournisse une surcouche (`KernelTestCase`, `WebTestCase`), celles-ci héritent toutes de `PHPUnit\Framework\TestCase`. Maîtriser PHPUnit pur est donc indispensable.

---

## 1. Structure d'un Test
Une classe de test doit étendre `TestCase` et les méthodes de test doivent commencer par `test` ou utiliser l'annotation `#[Test]`.

```php
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

class CalculatorTest extends TestCase
{
    // Méthode 1 : Préfixe 'test'
    public function testAdd(): void
    {
        $res = 1 + 1;
        $this->assertEquals(2, $res);
    }

    // Méthode 2 : Attribut (PHP 8+)
    #[Test]
    public function subtraction_works(): void
    {
        $this->assertSame(0, 1 - 1);
    }
}
```

---

## 2. Assertions Courantes
*   `assertSame($expected, $actual)` : Vérifie la valeur ET le type (`===`). **À privilégier.**
*   `assertEquals($expected, $actual)` : Vérifie la valeur (`==`).
*   `assertTrue($condition)` / `assertFalse($condition)`
*   `assertNull($variable)`
*   `assertCount($count, $array)` : Vérifie la taille d'un tableau/Countable.
*   `assertInstanceOf(ExpectedClass::class, $object)`
*   `expectException(MyException::class)` : Vérifie que le code suivant lève une exception.

---

## 3. Data Providers (Fournisseurs de données)
Permet de lancer le même test plusieurs fois avec des jeux de données différents.

### Interne (Méthode statique)
C'est la méthode la plus courante. La méthode doit être `public` et `static` et retourner un `iterable`.

```php
use PHPUnit\Framework\Attributes\DataProvider;

class PriceCalculatorTest extends TestCase
{
    #[DataProvider('providePrices')]
    public function testCalculateTtc(float $ht, float $expectedTtc): void
    {
        $calculator = new PriceCalculator();
        $this->assertEquals($expectedTtc, $calculator->calculateTtc($ht));
    }

    public static function providePrices(): \Generator
    {
        // Format: 'nom du cas' => [arg1, arg2]
        yield 'prix standard' => [100, 120];
        yield 'gratuit' => [0, 0];
        yield 'décimales' => [10.50, 12.60];
    }
}
```

### Externe (Classe séparée)
Utile pour partager des jeux de données entre plusieurs fichiers de tests.

```php
#[DataProviderExternal(PriceFixtures::class, 'providePrices')]
public function testCalculateTtc($ht, $ttc) { ... }
```

---

## 4. Doubles de Test (Mocks & Stubs)
Quand on teste une classe A qui dépend d'une classe B, on ne veut pas instancier la vraie classe B (surtout si elle appelle une BDD ou une API). On utilise un double.

### Création du Double
```php
// Crée un objet qui "ressemble" à UserRepository mais dont toutes les méthodes retournent null par défaut
$userRepo = $this->createMock(UserRepository::class);
```

### Configuration (Stubbing)
Définir ce que le mock doit répondre ("Stub" = Bouchon).

```php
// Quand on appelle find(1), retourne l'utilisateur Admin
$userRepo->method('find')
    ->with(1) // Optionnel : vérifie les arguments
    ->willReturn(new User('admin'));

// Pour n'importe quel argument
$userRepo->method('findAll')->willReturn([]);
```

### Vérification (Mocking)
Vérifier que le mock a bien été appelé ("Mock" = Espion).

```php
$mailer = $this->createMock(MailerInterface::class);

// On s'attend à ce que 'send' soit appelé exactement une fois
$mailer->expects($this->once())
    ->method('send');

// On injecte le mock
$service = new RegistrationService($mailer);
$service->register('test@test.com');
```

---

## 5. Cycle de Vie (Fixtures)
Méthodes spéciales exécutées avant/après les tests.

*   `setUp()` : Avant **chaque** test. (Ex: créer une nouvelle instance de la classe à tester).
*   `tearDown()` : Après **chaque** test. (Ex: nettoyer, `Mockery::close()`).
*   `setUpBeforeClass()` : Avant le **premier** test de la classe (statique).
*   `tearDownAfterClass()` : Après le **dernier** test de la classe (statique).

```php
protected function setUp(): void
{
    // Bonnes pratiques : toujours appeler le parent dans un contexte Symfony (KernelTestCase)
    parent::setUp(); 
    $this->calculator = new Calculator();
}
```

---

## ⚠️ Points de vigilance (Certification)
*   **Exception Testing** : `expectException` doit être appelé **avant** le code qui lève l'exception.
*   **Private/Protected** : PHPUnit ne peut pas tester directement des méthodes privées. C'est normal : on teste l'interface publique (contrat). Si besoin, passez par la méthode publique qui l'utilise ou utilisez la réflexion (déconseillé).
*   **Void** : N'oubliez pas le type de retour `: void` sur vos méthodes de test, PHPUnit 10/11 est strict là-dessus.
*   **XML** : La configuration se trouve dans `phpunit.xml.dist`. On peut y définir les suites de tests, les variables d'environnement (`APP_ENV`, `DATABASE_URL`) et la couverture de code.
