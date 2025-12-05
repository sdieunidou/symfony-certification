# Événements de la Console

## Concept clé
Le composant Console dispose de son propre cycle de vie d'événements, indépendant du HttpKernel.
Cela permet de brancher des comportements globaux sur toutes les commandes (ex: Logging, Profiling, Maintenance check).

## Les Événements (`ConsoleEvents`)

### 1. `console.command` (`ConsoleCommandEvent`)
*   **Quand** : Juste avant l'exécution de la commande (avant `initialize`).
*   **Usage** :
    *   Désactiver une commande dynamiquement (`$event->disableCommand()`).
    *   Lire/Modifier les options d'entrée.
    *   Vérifier si l'application est en maintenance.

### 2. `console.error` (`ConsoleErrorEvent`)
*   **Quand** : Lorsqu'une exception est lancée par une commande.
*   **Usage** :
    *   Logger l'erreur spécifiquement.
    *   Nettoyer/Changer l'exception ou le code de sortie (`$event->setExitCode()`).

### 3. `console.terminate` (`ConsoleTerminateEvent`)
*   **Quand** : Après l'exécution de la commande (succès ou échec).
*   **Usage** :
    *   Nettoyage global.
    *   Afficher des stats (temps d'exécution, mémoire).

### 4. `console.signal` (`ConsoleSignalEvent`)
*   **Quand** : Le processus reçoit un signal système (SIGINT/Ctrl+C, SIGTERM).
*   **Usage** :
    *   Arrêter proprement une boucle infinie (Worker).
    *   Sauvegarder l'état avant de quitter.

## Exemple : Listener de Maintenance

```php
#[AsEventListener(event: ConsoleEvents::COMMAND)]
public function onConsoleCommand(ConsoleCommandEvent $event): void
{
    // Autorise les commandes internes même en maintenance
    if ($event->getCommand() instanceof MaintenanceCommand) {
        return;
    }

    if ($this->isMaintenanceMode()) {
        $event->getOutput()->writeln('Application en maintenance.');
        $event->disableCommand();
        $event->setExitCode(Command::FAILURE);
    }
}
```

## 🧠 Concepts Clés
1.  **Integration** : Le `FrameworkBundle` enregistre automatiquement les listeners pour connecter la Console au dispatcher d'événements global.
2.  **CLI vs HTTP** : Ces événements ne sont **jamais** déclenchés lors d'une requête HTTP.

## ⚠️ Points de vigilance (Certification)
*   **Traceability** : `console.command` est le premier point d'entrée pour auditer "Qui a lancé quoi ?".

## Ressources
*   [Symfony Docs - Console Events](https://symfony.com/doc/current/components/console/events.html)
