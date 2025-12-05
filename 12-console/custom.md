# Commandes Personnalisées

## Concept clé
Les commandes personnalisées permettent d'exécuter la logique métier de l'application depuis le terminal (CLI).
C'est le point d'entrée pour les tâches CRON, les imports de données, ou les maintenances.

## Structure d'une Commande

```php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:test', description: 'Test command')]
class TestCommand extends Command
{
    public function __construct(
        private MyService $service // Injection de dépendance
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        // Définition inputs
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        try {
            $this->service->doSomething();
            $io->success('Opération réussie.');
            
            return Command::SUCCESS; // 0
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            
            return Command::FAILURE; // 1
        }
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
