# Interopérabilité et PSRs

## Concept clé
Le **PHP-FIG** (Framework Interop Group) édite les **PSR** (PHP Standards Recommendations).
Symfony est un membre actif et implémente la plupart des standards pour permettre l'utilisation de librairies tierces sans friction.

## PSRs Supportées dans Symfony 7

### Standards de Code
*   **PSR-1 / PSR-12** : Coding Style. Symfony suit ces règles (avec quelques ajouts stricts gérés par `php-cs-fixer`).
*   **PSR-4** : Autoloading. Standard utilisé par Composer et le ClassLoader Symfony.

### Interfaces de Service
*   **PSR-3 (Logger)** : `Psr\Log\LoggerInterface`.
    *   Utilisé partout. Permet de remplacer Monolog par un autre logger.
*   **PSR-6 (Caching)** : `Psr\Cache\CacheItemPoolInterface`.
    *   Le composant Cache de Symfony est une implémentation de référence.
*   **PSR-16 (Simple Cache)** : `Psr\SimpleCache\CacheInterface`.
    *   API simplifiée pour le cache (get/set directs).
*   **PSR-11 (Container)** : `Psr\Container\ContainerInterface`.
    *   Permet l'injection de conteneurs (Service Locators) de manière standard.
*   **PSR-14 (Event Dispatcher)** : `Psr\EventDispatcher\EventDispatcherInterface`.
    *   Le composant EventDispatcher est compatible depuis Symfony 4.4.
*   **PSR-18 (HTTP Client)** : `Psr\Http\Client\ClientInterface`.
    *   Le composant HttpClient fournit une implémentation via `Psr18Client`.
*   **PSR-20 (Clock)** : `Psr\Clock\ClockInterface`.
    *   Nouveauté Symfony 6.3+. Permet de mocker le temps dans les tests (`ClockAwareTrait`).

### Le Cas HTTP Message (PSR-7)
Symfony **n'utilise PAS** PSR-7 (`ServerRequestInterface`, `ResponseInterface`) nativement.
*   **Raison** : PSR-7 impose l'immutabilité (chaque modif crée un nouvel objet), ce qui a été jugé trop lourd et verbeux pour l'API core de Symfony (qui utilise `HttpFoundation` mutable).
*   **Bridge** : Si vous voulez utiliser une librairie qui attend du PSR-7, installez `symfony/psr-http-message-bridge` et une factory PSR-17 (ex: `nyholm/psr7`). Le bridge convertit `Symfony Request` <-> `PSR-7 Request`.

## 🧠 Concepts Clés
1.  **Interopérabilité** : Grâce aux PSRs, je peux utiliser une librairie de log faite pour Laravel dans Symfony, car elle type-hint `LoggerInterface` et non `Monolog`.
2.  **Autowiring** : Symfony alias automatiquement les interfaces PSR vers ses implémentations par défaut.
    *   `Psr\Log\LoggerInterface` -> injecte `monolog.logger`.
    *   `Psr\Clock\ClockInterface` -> injecte le service `Clock` (natif ou système).

## ⚠️ Points de vigilance (Certification)
*   **PSR-7** : Savoir expliquer pourquoi Symfony ne l'utilise pas nativement (Performance / DX / Historique) mais qu'il est compatible.
*   **PSR-6 vs PSR-16** : PSR-6 est plus puissante (Item pools, tags, defer), PSR-16 est plus simple (clé-valeur direct). Symfony supporte les deux.

## Ressources
*   [PHP-FIG Website](https://www.php-fig.org/psr/)
*   [Symfony PSR-7 Bridge](https://symfony.com/doc/current/components/psr7.html)
