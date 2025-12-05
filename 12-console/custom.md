# Commandes Personnalisées

## Concept clé
Créer ses propres commandes CLI pour des tâches récurrentes (Cron jobs, imports, maintenance).

## Application dans Symfony 7.0
Créer une classe qui étend `Command`.

```php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-user',
    description: 'Creates a new user.',
    hidden: false,
    aliases: ['app:add-user']
)]
class CreateUserCommand extends Command
{
    // Constructeur pour injecter des dépendances
    public function __construct(private UserManager $userManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        // Définition des arguments/options ici
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Logique...
        $output->writeln('User created!');

        return Command::SUCCESS; // ou Command::FAILURE, ou Command::INVALID
    }
}
```

## Points de vigilance (Certification)
*   **Retour** : La méthode `execute` DOIT retourner un entier (le code de sortie, 0 pour succès). Utiliser les constantes `Command::SUCCESS` (0), `Command::FAILURE` (1), `Command::INVALID` (2).
*   **Constructeur** : Toujours appeler `parent::__construct()` si vous définissez un constructeur.

## Ressources
*   [Symfony Docs - Custom Commands](https://symfony.com/doc/current/console.html#creating-a-command)

