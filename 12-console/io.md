# Symfony Style (IO)

## Concept clé
`SymfonyStyle` (`$io`) est une classe utilitaire qui standardise les entrées/sorties de la console. Elle garantit que toutes les commandes Symfony ont la même "Look & Feel".
Elle remplace l'utilisation directe de `$input` et `$output` pour l'affichage et l'interaction.

## Instanciation
```php
use Symfony\Component\Console\Style\SymfonyStyle;

protected function execute(InputInterface $input, OutputInterface $output): int
{
    $io = new SymfonyStyle($input, $output);
    // ...
}
```

## Méthodes d'Affichage (Output)

### Blocs
*   `$io->title('Gros Titre')`
*   `$io->section('Sous-section')`
*   `$io->text('Texte normal')`
*   `$io->listing(['Point 1', 'Point 2'])` : Liste à puces.
*   `$io->table($headers, $rows)` : Tableau.

### États (Feedback)
*   `$io->success('Bravo')` : Fond vert.
*   `$io->warning('Attention')` : Fond orange.
*   `$io->error('Erreur')` : Fond rouge.
*   `$io->note('Note')` : Fond jaune clair.

## Méthodes d'Interaction (Input)

*   `$io->ask('Quel est ton nom ?', 'Défaut')` : Question simple.
*   `$io->askHidden('Mot de passe ?')` : Masque la saisie.
*   `$io->confirm('Confirmer ?', false)` : Oui/Non (retourne bool).
*   `$io->choice('Choisir une couleur', ['Rouge', 'Bleu'], 'Rouge')` : Sélection.

## 🧠 Concepts Clés
1.  **Verbosity** : `SymfonyStyle` gère intelligemment la verbosité. Par exemple, `text()` affiche toujours, mais `note()` peut être masqué en mode quiet.
2.  **Progress Bar** : `$io` intègre des méthodes simplifiées pour la barre de progression (`progressStart`, `progressAdvance`).

## ⚠️ Points de vigilance (Certification)
*   **Validation** : `ask()` accepte un 3ème argument (callback de validation) pour forcer un format.
    ```php
    $io->ask('Age', null, function ($number) {
        if (!is_numeric($number)) throw new \RuntimeException('Entier requis');
        return (int) $number;
    });
    ```

## Ressources
*   [Symfony Docs - SymfonyStyle](https://symfony.com/doc/current/console/style.html)
