# Commandes Personnalisées

## Concept clé
Les commandes personnalisées permettent d'exécuter la logique métier de l'application depuis le terminal (CLI).
C'est le point d'entrée pour les tâches CRON, les imports de données, ou les maintenances.

## Structure d'une Commande

### 1. Classe Standard (Extends Command)

```php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
// ...

#[AsCommand(name: 'app:test')]
class TestCommand extends Command
{
    // ... méthode execute() ...
}
```

### 2. Commande Invokable (Symfony 7.3+)
Depuis Symfony 7.3, il n'est plus obligatoire d'étendre la classe de base `Command`. Il suffit d'implémenter une méthode `__invoke()`.

```php
#[AsCommand(name: 'app:simple')]
class SimpleCommand
{
    public function __invoke(OutputInterface $output): int
    {
        $output->writeln('Hello !');
        return Command::SUCCESS;
    }
}
```

## Code de Retour (Exit Code)
La méthode `execute()` **DOIT** retourner un entier (int).
*   `Command::SUCCESS` (0) : Tout s'est bien passé.
*   `Command::FAILURE` (1) : Erreur (logique).
*   `Command::INVALID` (2) : Mauvaise utilisation (arguments invalides).

## 🧠 Concepts Clés
1.  **Cycle de vie** :
    *   Instanciation (Service).
    *   `configure()` : Appelé immédiatement.
    *   `initialize()` : Juste avant l'exécution (pour initialiser des variables basées sur l'input).
    *   `interact()` : Pour poser des questions interactives si des arguments manquent.
    *   `execute()` : La logique.
2.  **Exception** : Si une exception est lancée dans `execute`, la commande échoue (code != 0) et l'application affiche l'erreur (avec stack trace si `-v`).

## ⚠️ Points de vigilance (Certification)
*   **Constructeur** : L'appel à `parent::__construct()` est obligatoire si vous définissez votre propre constructeur.
*   **Interact** : La méthode `interact()` n'est appelée que si le mode interactif est activé (par défaut dans le terminal, désactivé avec `--no-interaction` ou `-n`). C'est là qu'on met la logique `$io->ask()`.

## Ressources
*   [Symfony Docs - Console](https://symfony.com/doc/current/console.html)
