# Composant Clock

## Concept clé
Introduit dans Symfony 6.2, ce composant permet de tester le temps (Time Sensitive tests).
Au lieu d'utiliser `new \DateTime()` ou `time()` (qui prennent l'heure système et sont impossibles à mocker proprement), on utilise l'interface `ClockInterface`.

## Application dans Symfony 7.0

```php
use Symfony\Component\Clock\ClockInterface;

public function __construct(private ClockInterface $clock) {}

public function doSomething(): void
{
    $now = $this->clock->now(); // Retourne un DateTimeImmutable
    // ...
}
```

### En Test
On utilise `MockClock` pour figer le temps ou l'avancer.

```php
use Symfony\Component\Clock\MockClock;

$clock = new MockClock('2023-01-01 12:00:00');
$service = new MyService($clock);

$service->doSomething(); // Il est 12:00

$clock->sleep(3600); // Avance de 1h
$service->doSomething(); // Il est 13:00
```

## Points de vigilance (Certification)
*   **Psr\Clock** : Symfony implémente l'interface standard PSR-20 (`Psr\Clock\ClockInterface`).
*   **NativeClock** : L'implémentation utilisée en prod (utilise l'heure système).

## Ressources
*   [Symfony Docs - Clock](https://symfony.com/doc/current/components/clock.html)

