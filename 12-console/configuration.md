# Configuration des Commandes

## Concept clé
Comment enregistrer une commande dans l'application ?
Comment la configurer (Nom, Description, Aide) ?

## Application dans Symfony 7.0

### Enregistrement
Grâce à l'autoconfiguration (activée par défaut), toute classe étendant `Command` dans `src/Command` est automatiquement enregistrée comme service et taguée `console.command`.
Si l'autoconfiguration est désactivée, il faut taguer le service manuellement :
```yaml
services:
    App\Command\MyCommand:
        tags: ['console.command']
```

### Configuration (Méta-données)
Depuis PHP 8, l'attribut `#[AsCommand]` est la méthode recommandée.
```php
#[AsCommand(name: 'app:my-command', description: '...')]
```

Ancienne méthode (toujours valide, via `configure()`):
```php
protected function configure(): void
{
    $this
        ->setName('app:my-command')
        ->setDescription('...')
        ->setHelp('This command allows you to...');
}
```

## Points de vigilance (Certification)
*   **Lazy Loading** : Symfony charge les commandes de manière "paresseuse". Le constructeur de la commande n'est instancié que si la commande est réellement appelée (sauf si elle ne suit pas les conventions de nommage statiques). `#[AsCommand]` permet au framework de connaître le nom sans instancier la classe.

## Ressources
*   [Symfony Docs - Console Configuration](https://symfony.com/doc/current/console/commands_as_services.html)

