# Autowiring

## Concept clé
L'autowiring est un mécanisme qui permet au conteneur de deviner les dépendances à injecter dans le constructeur d'un service, en se basant uniquement sur le **typage** (Type Hinting) des arguments.
Il supprime 95% de la configuration explicite dans `services.yaml`.

## Fonctionnement
1.  Symfony analyse le constructeur : `public function __construct(LoggerInterface $logger)`.
2.  Il cherche un service dont l'ID ou l'alias correspond à `Psr\Log\LoggerInterface`.
3.  Il l'injecte automatiquement.

## Gestion des Scalaires et Conflits
Si l'autowiring échoue (ex: argument string `$adminEmail` ou plusieurs implémentations de `MailerInterface`), vous devez aider Symfony.

### 1. Attribut `#[Autowire]` (Symfony 6.1+)
C'est la méthode moderne pour injecter des paramètres ou des services spécifiques directement dans le code PHP.

```php
use Symfony\Component\DependencyInjection\Attribute\Autowire;

public function __construct(
    // Injecter un paramètre
    #[Autowire('%kernel.debug%')] 
    private bool $isDebug,

    // Injecter une variable d'environnement
    #[Autowire(env: 'DATABASE_URL')] 
    private string $dsn,

    // Injecter un service spécifique (si plusieurs implémentent l'interface)
    #[Autowire(service: 'monolog.logger.request')] 
    private LoggerInterface $requestLogger
) {}
```

### 2. Attribut `#[Target]`
Alias sémantique pour cibler une implémentation nommée.

```php
use Symfony\Component\DependencyInjection\Attribute\Target;

public function __construct(
    #[Target('filesystem.public')] FilesystemOperator $storage
) {}
```

### 3. Binding Global (`services.yaml`)
Pour définir une règle globale (ex: `$adminEmail` vaut toujours la même chose partout).

```yaml
services:
    _defaults:
        bind:
            string $adminEmail: 'admin@example.com'
            LoggerInterface $requestLogger: '@monolog.logger.request'
```

## 🧠 Concepts Clés
1.  **Performance** : L'autowiring est résolu à la **compilation** du conteneur (cache warmup). En production, il n'y a aucune réflexion, le code généré contient les injections en dur (`new MyService(new Logger())`). Impact runtime = Zéro.
2.  **Logique** : Symfony ne regarde pas le nom de la variable (`$logger`), sauf pour les bindings globaux. Il regarde le **Type** (`LoggerInterface`).

## ⚠️ Points de vigilance (Certification)
*   **Ambiguïté** : Si une interface a 2 implémentations et qu'aucune n'est définie comme alias par défaut, l'autowiring échoue avec une erreur explicite.
*   **Controller** : Les méthodes des contrôleurs bénéficient aussi de l'autowiring (via le `ServiceValueResolver`), ce qui est une exception (normalement seule l'injection constructeur est autowirée).

## Ressources
*   [Symfony Docs - Autowiring](https://symfony.com/doc/current/service_container/autowiring.html)
