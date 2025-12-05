# Objets Input et Output (Symfony Style)

## Concept clé
L'interaction avec l'utilisateur se fait via `$input` (lecture) et `$output` (écriture).
Pour une interface plus riche et standardisée, on utilise `SymfonyStyle` ($io).

## Application dans Symfony 7.0

```php
use Symfony\Component\Console\Style\SymfonyStyle;

protected function execute(InputInterface $input, OutputInterface $output): int
{
    $io = new SymfonyStyle($input, $output);
    
    // Lecture
    $user = $input->getArgument('username');
    $isDry = $input->getOption('dry-run');
    
    // Écriture stylisée
    $io->title('Création utilisateur');
    $io->section('Étape 1');
    
    $io->success('Terminé !');
    $io->error('Erreur...');
    $io->warning('Attention');
    $io->note('Info');
    
    // Interaction (Questions)
    $name = $io->ask('Quel est ton nom ?', 'Default');
    $pass = $io->askHidden('Mot de passe ?');
    $color = $io->choice('Couleur ?', ['Red', 'Blue'], 'Red');
    $confirm = $io->confirm('Êtes-vous sûr ?', false);
    
    // Barre de progression
    $io->progressStart(100);
    $io->progressAdvance();
    $io->progressFinish();

    return Command::SUCCESS;
}
```

## Points de vigilance (Certification)
*   **OutputInterface** : Fournit les méthodes basiques `writeln()`, `write()`.
*   **Formatter** : On peut utiliser des balises de style : `$output->writeln('<info>Info</info> <error>Error</error> <comment>Comment</comment>')`.
*   **SymfonyStyle** : Toujours préférer `$io` pour la cohérence visuelle des commandes Symfony.

## Ressources
*   [Symfony Docs - SymfonyStyle](https://symfony.com/doc/current/console/style.html)

