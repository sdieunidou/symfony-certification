# Événements de la Console

## Concept clé
Le composant Console dispatche des événements (comme le HttpKernel) permettant de s'interfacer avec le cycle de vie d'une commande.

## Application dans Symfony 7.0
Les événements (`Symfony\Component\Console\ConsoleEvents`) :

1.  `console.command` (`ConsoleCommandEvent`) : Avant l'exécution. Permet de désactiver la commande ($event->disableCommand()) ou de changer l'input.
2.  `console.error` (`ConsoleErrorEvent`) : Si une exception est lancée. Permet de logger ou de nettoyer l'exception.
3.  `console.terminate` (`ConsoleTerminateEvent`) : Après l'exécution (même en cas d'erreur). Permet de faire du nettoyage.
4.  `console.signal` (`ConsoleSignalEvent`) : Quand un signal système (SIGINT, SIGTERM) est reçu.

## Points de vigilance (Certification)
*   **Traceability** : Il existe aussi un `console.trace` (expérimental ou moins utilisé) dans certaines configurations, mais les 3 principaux (command, error, terminate) sont le standard.
*   **Listeners** : On enregistre des listeners/subscribers classiques sur ces événements.

## Ressources
*   [Symfony Docs - Console Events](https://symfony.com/doc/current/components/console/events.html)

