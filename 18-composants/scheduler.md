# Le Composant Scheduler

Introduit dans Symfony 6.3, le composant **Scheduler** fournit un système natif pour gérer les tâches récurrentes (Cron jobs), entièrement intégré à l'écosystème Messenger.

Il remplace la gestion fastidieuse de multiples entrées dans la Crontab système par un **point d'entrée unique** géré en PHP.

---

## 1. Architecture

Le Scheduler ne réinvente pas la roue, il s'appuie sur **Messenger** :
1.  **Schedule** : Un objet qui contient la liste des messages récurrents.
2.  **RecurringMessage** : Un message Messenger associé à une fréquence (Trigger).
3.  **Provider** : Une classe qui définit le planning (Schedule).
4.  **Worker** : Le worker Messenger standard qui consomme les messages au bon moment.

---

## 2. Mise en place

### Création du Provider
On crée une classe PHP qui implémente `ScheduleProviderInterface` et utilise l'attribut `#[AsSchedule]`.

```php
namespace App\Scheduler;

use App\Message\CleanupDbMessage;
use App\Message\WeeklyReportMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule('default')] // Le nom du schedule
class MainSchedule implements ScheduleProviderInterface
{
    public function getSchedule(): Schedule
    {
        return (new Schedule())
            ->add(
                // Syntaxe textuelle simple
                RecurringMessage::every('1 day', new CleanupDbMessage()),
                
                // Syntaxe Cron standard
                RecurringMessage::cron('0 12 * * 1', new WeeklyReportMessage()),
                
                // Avec objet Trigger spécifique
                RecurringMessage::trigger(
                    new CronExpressionTrigger('0 0 1 * *'),
                    new MonthlyInvoiceMessage()
                )
            )
            // Optionnel : Gestion d'état (lock) pour éviter les doublons en cas de redémarrage
            ->stateful($this->cache) 
        ;
    }
}
```

### Consommation
Puisque ce sont des messages Messenger, on utilise la commande standard :
```bash
php bin/console messenger:consume scheduler_default
```
Cette commande va "dormir" jusqu'à la prochaine échéance, dispatcher le message, puis se rendormir.

---

## 3. Fonctionnalités Avancées

### Statefulness (Verrouillage)
Par défaut, le scheduler est "stateless" (en mémoire). Si vous redémarrez le worker, il risque de relancer une tâche qui venait de s'exécuter il y a 1 minute si la fréquence le permet.
Pour éviter cela, on rend le schedule **stateful** via un Cache (Redis/Database). Il mémorisera la dernière exécution.

### Triggers Complexes
Le composant supporte des triggers avancés :
*   `PeriodicalTrigger` : Tous les X temps (intervalle fixe).
*   `CronExpressionTrigger` : Basé sur la syntaxe cron.
*   `Jitter` : Ajouter de l'aléatoire (`every('1 day', jitter: 60)`) pour éviter que tous les workers ne se réveillent à la milliseconde près (thundering herd problem).

---

## 4. Points de vigilance pour la Certification

*   **Différence avec Cron** :
    *   **Cron** : Lance un nouveau processus PHP à chaque fois. Lourd au démarrage, mais isolation totale.
    *   **Scheduler** : Processus longue durée (Daemon). Rapide, mémoire partagée (attention aux fuites mémoire).
*   **Intégration Messenger** : Les messages dispatchés par le Scheduler suivent le routing Messenger classique. Ils peuvent donc être traités par le worker scheduler lui-même (sync) OU envoyés dans une queue RabbitMQ pour être traités par d'autres workers (async).
*   **Commandes** : `debug:scheduler` pour visualiser les prochaines exécutions.
