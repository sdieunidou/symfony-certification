# Configuration des Commandes

## Concept clé
Pour qu'une commande soit utilisable via `bin/console`, elle doit être enregistrée dans le conteneur de services et configurée (Nom, Description, Arguments).

## Enregistrement
Par défaut, grâce à l'**Autoconfiguration** (`autoconfigure: true` dans `services.yaml`), toute classe étendant `Symfony\Component\Console\Command\Command` est automatiquement :
1.  Enregistrée comme service.
2.  Taguée avec `console.command`.

## Configuration (Méta-données)

### 1. Attribut PHP `#[AsCommand]` (Recommandé)
Depuis Symfony 5.3+, on utilise un attribut PHP pour définir le nom et la description statiquement. Cela permet le **Lazy Loading** (la commande n'est pas instanciée tant qu'on ne l'appelle pas).

```php
namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;

#[AsCommand(
    name: 'app:user:create|app:add-user', // Alias via pipe (Symfony 7.4+)
    description: 'Crée un nouvel utilisateur.',
    aliases: ['app:new-user'], // Alias classique (tableau)
    usages: ['bob', 'alice --admin'], // Exemples d'usage (Symfony 7.4+)
    hidden: false
)]
class CreateUserCommand extends Command
{
    // ...
}
```

### 2. Méthode `configure()` (Legacy / Dynamique)
Toujours utilisée pour définir les Arguments et Options (qui sont dynamiques).
Peut aussi être utilisée pour le nom/description, mais casse le Lazy Loading si on fait des calculs lourds.

```php
protected function configure(): void
{
    $this
        // Si pas d'attribut AsCommand
        // ->setName('app:user:create')
        // ->setDescription('...')
        
        ->setHelp('This command allows you to create a user...')
        ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
    ;
}
```

## 🧠 Concepts Clés
1.  **Lazy Loading** : Si une commande est lourde à construire (beaucoup de dépendances), `#[AsCommand]` est vital. `bin/console list` n'instanciera pas votre commande, il lira juste l'attribut.
2.  **Nommage** : Convention `namespace:action` (ex: `doctrine:migrations:migrate`).

## ⚠️ Points de vigilance (Certification)
*   **Commandes cachées** : `hidden: true` (ou préfixer le nom par `_`) cache la commande de la liste `bin/console list`, mais elle reste exécutable.
*   **Service** : Une commande est un service. Vous pouvez utiliser l'injection de dépendances dans le constructeur (`__construct`). N'oubliez pas d'appeler `parent::__construct()`.

## Ressources
*   [Symfony Docs - Console Commands](https://symfony.com/doc/current/console.html)
