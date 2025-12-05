# Component Process

## Concept Clé
Le composant **Process** exécute des commandes système dans des sous-processus. Il permet d'exécuter des scripts shell, des binaires ou d'autres scripts PHP de manière isolée.

## Utilisation

```php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

$process = new Process(['git', 'clone', 'https://github.com/symfony/symfony.git']);
$process->run();

// Exécute après la fin du processus
if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
}

echo $process->getOutput();
```

## Fonctionnalités
*   **Asynchrone** : `$process->start()` lance le processus en arrière-plan.
*   **Streaming** : Lire la sortie standard (stdout) et d'erreur (stderr) en temps réel via un callback.
*   **Timeout** : Définir un temps limite d'exécution.
*   **Signal** : Envoyer des signaux POSIX au processus.

## Ressources
*   [Symfony Docs - Process](https://symfony.com/doc/current/components/process.html)
