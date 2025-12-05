# Enregistrement de Services (`services.yaml`)

## Concept clé
Le fichier `config/services.yaml` est le point d'entrée principal pour dire à Symfony comment instancier vos classes.
Avec la configuration moderne par défaut, l'intervention manuelle est minime.

## La Configuration Standard (Best Practice)

```yaml
services:
    # 1. Configuration par défaut pour TOUS les services de ce fichier
    _defaults:
        autowire: true      # Injection de dépendances automatique
        autoconfigure: true # Ajout automatique de tags (Twig extension, Command...)

    # 2. Enregistrement en masse (Service Discovery)
    # Rend toutes les classes de src/ disponibles comme services
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'

    # 3. Surcharges spécifiques (si nécessaire)
    # Ex: passer un argument scalaire qui ne peut pas être autowiré
    App\Service\ReportGenerator:
        arguments:
            $reportLimit: 100
```

## Explications
*   **Autowire** : Symfony regarde le constructeur `__construct(LoggerInterface $logger)` et injecte le service `logger`.
*   **Autoconfigure** : Si votre classe implémente `Command`, Symfony ajoute le tag `console.command`. Si elle implémente `EventSubscriberInterface`, elle ajoute `kernel.event_subscriber`.
*   **Exclude** : On n'enregistre PAS les Entités (ce sont des données, pas des services) ni le Kernel.

## Fonctionnalités Avancées

### 1. Limitation par Environnement (Attributs)
Vous pouvez restreindre un service à un environnement spécifique directement depuis la classe PHP.

```php
use Symfony\Component\DependencyInjection\Attribute\When;
use Symfony\Component\DependencyInjection\Attribute\WhenNot;

#[When(env: 'dev')]
#[When(env: 'test')]
class FakeMailer implements MailerInterface { ... }

#[WhenNot(env: 'dev')] // Nouveau Symfony 7.2
class RealMailer implements MailerInterface { ... }
```

### 2. Supprimer un Service
Utile pour retirer un service dans un environnement spécifique (ex: `test`).

```php
// config/services_test.php
$services->remove(App\Service\HeavyService::class);
```

### 3. Arguments Abstraits
Si un argument doit être défini dynamiquement (par un Compiler Pass), on peut le marquer comme abstrait.

```yaml
App\Service\MyService:
    arguments:
        $rootNamespace: !abstract 'sera défini par un Pass'
```

### 4. Imports Multiples et Namespaces
Si vous importez plusieurs dossiers sous le même namespace PHP, vous devez utiliser l'option `namespace` pour éviter les conflits de clés YAML.

```yaml
services:
    command_handlers:
        namespace: App\Domain\
        resource: '../src/Domain/*/CommandHandler'
        tags: [command_handler]

### 5. Injecter une Closure
Vous pouvez injecter un callable (service invoke ou méthode) sous forme de Closure.

```yaml
App\Service\MessageGenerator:
    arguments:
        $generateMessageHash: !closure '@App\Hash\MessageHashGenerator'
```
```

## 🧠 Concepts Clés
1.  **ID du service** : Par défaut, l'ID d'un service est son **FQCN** (Fully Qualified Class Name, ex: `App\Service\Mailer`).
2.  **Alias** : On peut créer un alias pour référencer un service par un nom court ou une interface.
    ```yaml
    App\Contract\MailerInterface: '@App\Service\SmtpMailer'
    ```

## ⚠️ Points de vigilance (Certification)
*   **App Namespace** : La clé `App\` dans le yaml correspond au namespace PHP défini dans `composer.json` (autoload psr-4). Si vous changez le namespace racine, il faut adapter le yaml.
*   **Ordre** : Les définitions spécifiques (en bas du fichier) écrasent les définitions glob (en haut). C'est pour cela qu'on met `App\` en premier, puis les exceptions en dessous.

## Ressources
*   [Symfony Docs - Service Configuration](https://symfony.com/doc/current/service_container.html#creating-configuring-services-in-the-container)
