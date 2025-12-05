# Helpers Natifs

## Concept clé
Avant `SymfonyStyle`, on utilisait des classes "Helper" individuelles pour formater les tableaux, poser des questions, etc.
Elles existent toujours et sont utilisées en interne par `SymfonyStyle`.

## Application dans Symfony 7.0
Accessibles via `$this->getHelper('name')`.

*   **QuestionHelper** : Poser des questions (interactivement).
*   **TableHelper** (obsolète, utiliser la classe `Table`) : Afficher des données tabulaires.
*   **ProgressBar** : Afficher une barre de progression.
*   **FormatterHelper** : Formater du texte/couleurs.
*   **ProcessHelper** : Lancer des sous-processus.

### Exemple Table
```php
use Symfony\Component\Console\Helper\Table;

$table = new Table($output);
$table
    ->setHeaders(['ISBN', 'Title', 'Author'])
    ->setRows([
        ['99921-58-10-7', 'Divine Comedy', 'Dante Alighieri'],
        ['9971-5-0210-0', 'A Tale of Two Cities', 'Charles Dickens'],
    ]);
$table->render();
```

## Points de vigilance (Certification)
*   **Helperset** : Les commandes ont accès à un `HelperSet`. On peut y enregistrer ses propres helpers.
*   **Préférence** : Pour la certification, il faut connaître leur existence, mais savoir que `SymfonyStyle` est le wrapper recommandé qui simplifie leur utilisation.

## Ressources
*   [Symfony Docs - Console Helpers](https://symfony.com/doc/current/components/console/helpers/index.html)

