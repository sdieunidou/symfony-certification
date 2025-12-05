# Tester les Commandes

## Concept clé
Comme les contrôleurs, les commandes console doivent être testées. Symfony fournit `CommandTester` et `ApplicationTester` pour exécuter des commandes dans un environnement de test, sans lancer un vrai processus shell, et pour inspecter la sortie.

## CommandTester (Test Unitaire/Intégration)
Utilisé pour tester une commande spécifique isolée.

```php
namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateUserCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        // Récupère la commande enregistrée dans le conteneur
        $command = $application->find('app:create-user');
        
        $commandTester = new CommandTester($command);
        
        // Exécution avec arguments et options
        $commandTester->execute([
            'username' => 'Wouter',      // Argument
            '--admin' => true,           // Option booléenne
            // '--iter' => 5,
        ]);

        $commandTester->assertCommandIsSuccessful(); // Vérifie exit code == 0

        // Inspection de la sortie
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Username: Wouter', $output);
    }
}
```

## ApplicationTester (Test Global)
Utilisé pour tester l'application console entière (ex: dispatcher des événements console, enchaîner des commandes).

```php
use Symfony\Component\Console\Tester\ApplicationTester;

$application = new Application();
$application->setAutoExit(false); // Important !

$tester = new ApplicationTester($application);
$tester->run(['command' => 'list']);
```

## Terminal Mocking
Si votre commande dépend de la taille du terminal (width/height), vous pouvez mocker la classe `Terminal`.

```php
use Symfony\Component\Console\Terminal;

$terminal = new Terminal();
$width = $terminal->getWidth();
```

## ⚠️ Points de vigilance (Certification)
*   **AutoExit** : Par défaut, `Application` appelle `exit()` après l'exécution. En test, cela stopperait PHPUnit. `CommandTester` gère cela, mais pour `ApplicationTester` il faut `setAutoExit(false)`.
*   **Inputs** : Pour les options booléennes (`VALUE_NONE`), il faut passer `true` (`'--verbose' => true`).
*   **Services** : Dans un test KernelTestCase, récupérez toujours la commande via `$application->find()` pour qu'elle soit correctement initialisée avec ses dépendances (Service Container).

## Ressources
*   [Symfony Docs - Testing Commands](https://symfony.com/doc/current/console.html#testing-commands)
