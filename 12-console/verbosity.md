# Niveaux de Verbosité

## Concept clé
Permet de contrôler la quantité d'informations affichées par la commande.
L'utilisateur contrôle cela avec les drapeaux `-v`, `-vv`, `-vvv`, `-q`.

## Application dans Symfony 7.0
Les constantes `OutputInterface` :

*   `VERBOSITY_QUIET` (`-q`) : Rien n'est affiché.
*   `VERBOSITY_NORMAL` (Défaut) : Infos standards.
*   `VERBOSITY_VERBOSE` (`-v`) : Infos détaillées.
*   `VERBOSITY_VERY_VERBOSE` (`-vv`) : Infos de débug.
*   `VERBOSITY_DEBUG` (`-vvv`) : Tout (Stack traces complètes).

### Utilisation
```php
if ($output->isVerbose()) {
    $output->writeln('Starting process...');
}

if ($output->isDebug()) {
    $output->writeln('Memory usage: ' . memory_get_usage());
}

// Écriture conditionnelle
$output->writeln('Only for verbose', OutputInterface::VERBOSITY_VERBOSE);
```

## Points de vigilance (Certification)
*   **Logique** : `isVerbose()` retourne true si le niveau est >= VERBOSE. Donc `-vvv` déclenche aussi `isVerbose()`.
*   **Exceptions** : En mode quiet, les exceptions ne sont pas affichées (sauf erreur fatale script). En mode debug, les exceptions affichent toute la trace.

## Ressources
*   [Symfony Docs - Verbosity](https://symfony.com/doc/current/console/verbosity.html)

