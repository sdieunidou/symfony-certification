# Le Composant Process

Le composant **Process** permet d'exécuter des commandes système dans des sous-processus indépendants. Il abstrait les différences entre les systèmes d'exploitation et gère les échappements d'arguments pour prévenir les failles de sécurité.

## 1. Utilisation Synchrone (`run`)

C'est la méthode la plus simple : le script PHP attend que la commande soit terminée.

```php
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

// Commande, argument 1, argument 2...
$process = new Process(['ls', '-lsa']);

// Exécute la commande
$process->run();

// Vérification du succès (code de sortie 0)
if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
}

echo $process->getOutput();
```

---

## 2. Utilisation Asynchrone (`start`)

Pour lancer des tâches longues sans bloquer le script PHP principal.

```php
$process = new Process(['php', 'bin/console', 'cache:clear']);
$process->start();

// Faire autre chose pendant que le processus tourne...
while ($process->isRunning()) {
    echo '.';
    sleep(1);
}

echo $process->getOutput();
```

### Streaming (Callback)
Pour récupérer la sortie en temps réel (ex: afficher une barre de progression).

```php
$process->run(function (string $type, string $buffer): void {
    if (Process::ERR === $type) {
        echo 'ERR > '.$buffer;
    } else {
        echo 'OUT > '.$buffer;
    }
});
```

---

## 3. Fonctionnalités Avancées

### Timeouts
Par défaut, un processus a un timeout de 60 secondes.
*   `setTimeout($seconds)` : Durée totale maximale.
*   `setIdleTimeout($seconds)` : Durée maximale sans output (utile si le processus hang).

### Signaux
On peut envoyer des signaux POSIX au processus (comme `kill` ou `CTRL+C`).
```php
$process->signal(SIGKILL);
```

### PhpProcess
Une classe helper pour exécuter du code PHP arbitraire dans un processus isolé (sandbox).
```php
use Symfony\Component\Process\PhpProcess;

$process = new PhpProcess("<?php echo 'Hello World'; ?>");
$process->run();
```

### Pipes & Input
On peut passer des données à l'entrée standard (`stdin`) du processus.
```php
$process = new Process(['cat']);
$process->setInput('Hello World');
$process->run();
```

---

## 4. Points de vigilance pour la Certification

*   **Array vs String** : Toujours passer un tableau d'arguments au constructeur (`['grep', 'foo', 'file.txt']`). Passer une chaîne unique (`'grep foo file.txt'`) est possible via `Process::fromShellCommandline()`, mais déconseillé car moins portable et plus risqué (injection).
*   **Portabilité** : Le composant gère les différences `'` vs `"` entre Windows et Linux.
*   **Environnement** : Par défaut, le processus hérite des variables d'environnement actuelles. On peut les surcharger via `$process->setEnv(['APP_ENV' => 'test'])`.
