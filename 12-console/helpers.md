# Helpers Natifs

## Concept clé
Le composant Console fournit des classes utilitaires ("Helpers") pour effectuer des tâches d'affichage complexes (Tableaux, Barres de progression, Questions).
Bien que `SymfonyStyle` soit recommandé pour l'usage courant, il utilise ces helpers en interne.

## Liste des Helpers

### 1. Table
Affiche des données sous forme de tableau ASCII.

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

### 2. ProgressBar
Affiche une barre de progression pour les tâches longues.

```php
use Symfony\Component\Console\Helper\ProgressBar;

$progressBar = new ProgressBar($output, 100);
$progressBar->start();

// Dans une boucle
$progressBar->advance();

$progressBar->finish();
```

### 3. QuestionHelper
Gère l'interactivité (demander une info à l'utilisateur).

### 4. ProcessHelper
Wrapper autour du composant `Process` pour exécuter des commandes système et afficher leur sortie en temps réel.

## HelperSet
Les commandes ont accès à un `HelperSet` via `$this->getHelper('name')`.
C'est le mécanisme d'extension historique de la Console.

## 🧠 Concepts Clés
1.  **SymfonyStyle** : La classe `Symfony\Component\Console\Style\SymfonyStyle` ($io) est une surcouche haut niveau qui simplifie l'utilisation de ces helpers. Préférez `$io->table()` ou `$io->progressStart()` plutôt que d'instancier les helpers manuellement.
2.  **Formattage** : Le `FormatterHelper` permet de convertir les balises `<info>...</info>` en codes couleurs ANSI.

## ⚠️ Points de vigilance (Certification)
*   **TableSeparator** : Pour ajouter une ligne horizontale dans un tableau, utilisez `new TableSeparator()`.
*   **Question** : `QuestionHelper` gère le masquage des mots de passe (stty) et l'autocomplétion.

## Ressources
*   [Symfony Docs - Console Helpers](https://symfony.com/doc/current/components/console/helpers/index.html)
