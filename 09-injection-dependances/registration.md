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
