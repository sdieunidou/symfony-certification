# Options et Arguments

## Concept clé
Paramétrer l'exécution de la commande.
*   **Argument** : Valeur positionnelle requise ou optionnelle (`cp source dest`).
*   **Option** : Valeur nommée avec drapeaux (`--force`, `-f`, `--iterations=10`).

## Application dans Symfony 7.0
Dans la méthode `configure()`.

```php
protected function configure(): void
{
    $this
        // Argument Requis
        ->addArgument('username', InputArgument::REQUIRED, 'The username')
        
        // Argument Optionnel
        ->addArgument('password', InputArgument::OPTIONAL, 'Initial password')
        
        // Argument Tableau (dernier argument uniquement)
        ->addArgument('roles', InputArgument::IS_ARRAY, 'User roles')
        
        // Option simple (booléen, présent ou absent)
        ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Do not execute query')
        
        // Option avec valeur (--queue=priority)
        ->addOption('queue', 'q', InputOption::VALUE_REQUIRED, 'Queue name', 'default')
        
        // Option avec valeur optionnelle (--yell ou --yell=loud)
        ->addOption('yell', null, InputOption::VALUE_OPTIONAL, 'Yell config', 'UPPER')
    ;
}
```

## Points de vigilance (Certification)
*   **REQUIRED vs OPTIONAL** : On ne peut pas avoir un argument REQUIRED *après* un argument OPTIONAL (logique positionnelle).
*   **VALUE_NONE** : Utilisé pour les drapeaux (`--verbose`). Si présent = true, sinon false.
*   **VALUE_NEGATABLE** : (Symfony 5.4+) `--no-foo` (`InputOption::VALUE_NEGATABLE`).

## Ressources
*   [Symfony Docs - Console Input](https://symfony.com/doc/current/console/input.html)

