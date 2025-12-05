# Composant Clock (Horloge)

## Concept clé
Le temps est l'ennemi des tests. Utiliser `time()`, `date()` ou `new \DateTime()` couple votre code à l'horloge système, rendant impossible le test de scénarios précis ("Que se passe-t-il si je valide le token 1h et 1s plus tard ?").
Le composant **Clock** fournit une abstraction testable.

## Interface `ClockInterface`
Introduite dans Symfony 6.2 (et standardisée PSR-20).
*   `now(): DateTimeImmutable` : Retourne l'heure courante.
*   `sleep(float|int $seconds): void` : Attend X secondes.
*   `withTimeZone(...)` : Retourne une nouvelle horloge avec le fuseau horaire spécifié.

## Application dans Symfony 7.0

### 1. Injection
Ne faites plus `new DateTime()`. Injectez l'horloge.

```php
use Psr\Clock\ClockInterface;

class TokenManager
{
    public function __construct(
        private ClockInterface $clock
    ) {}

    public function createToken(): Token
    {
        $expiresAt = $this->clock->now()->modify('+1 hour');
        return new Token($expiresAt);
    }
}
```

### 2. En Production (`NativeClock`)
Symfony injecte automatiquement `Symfony\Component\Clock\NativeClock`, qui utilise l'heure système réelle.

### 3. En Test (`MockClock`)
Vous pouvez figer le temps ou le faire avancer manuellement.

```php
use Symfony\Component\Clock\MockClock;

public function testTokenExpiration(): void
{
    // On fixe l'heure à une date connue
    $clock = new MockClock('2024-01-01 12:00:00');
    $manager = new TokenManager($clock);

    $token = $manager->createToken(); // Expire à 13:00

    // On avance le temps de 1h et 1min
    $clock->sleep(3660); 

    // Il est maintenant 13:01 pour le service
    $this->assertTrue($token->isExpired($clock->now()));
}
```

## 🧠 Concepts Clés
1.  **Immutabilité** : `now()` retourne toujours un `DateTimeImmutable`. C'est une bonne pratique.
2.  **PSR-20** : Symfony 7 utilise l'interface standard du PHP-FIG.

## ⚠️ Points de vigilance (Certification)
*   **DatePoint** : Le composant fournit aussi une classe `DatePoint` qui étend `DateTimeImmutable` avec une API plus fluide, mais `ClockInterface` reste le point d'entrée principal.

## Ressources
*   [Symfony Docs - Clock](https://symfony.com/doc/current/components/clock.html)
