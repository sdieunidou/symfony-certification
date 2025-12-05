# Component Scheduler

## Concept Clé
Introduit dans Symfony 6.3, le composant **Scheduler** permet de planifier des tâches récurrentes (Cron jobs) directement en PHP, sans dépendre de la crontab système (ou avec un seul point d'entrée). Il s'intègre avec le composant Messenger.

## Concepts
*   **Schedule** : Une collection de messages à dispatcher à des moments précis.
*   **Trigger** : La règle de récurrence (ex: "tous les jours à 8h").
*   **Provider** : Fournit le planning.

## Mise en place

```php
#[AsSchedule('default')]
class MyScheduleProvider implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                RecurringMessage::every('1 day', new CleanupDbCommand()),
                RecurringMessage::cron('0 12 * * 1', new WeeklyReportCommand())
            );
    }
}
```

## Exécution
Le worker Messenger standard consomme les messages planifiés.
```bash
php bin/console messenger:consume scheduler_default
```

## Avantages
*   Gestion des tâches planifiées dans le code (versionné avec Git).
*   Support natif des attributs de récurrence.
*   Intégration avec l'écosystème Messenger (Retries, Failure transport).

## Ressources
*   [Symfony Docs - Scheduler](https://symfony.com/doc/current/scheduler.html)
