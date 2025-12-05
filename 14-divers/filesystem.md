# Composants Filesystem et Finder

## Filesystem
Abstraction pour manipuler les fichiers et dossiers (copier, supprimer, créer dossier, toucher, changer droits, liens symboliques).
Gère les différences d'OS (Windows vs Linux).

```php
$fs = new Filesystem();
$fs->mkdir('/tmp/photos');
$fs->copy('origin.txt', 'target.txt');
$fs->remove(['symlink', '/path/to/dir']);
```

## Finder
Trouver des fichiers selon des critères (nom, taille, date, contenu) de manière fluide.

```php
$finder = new Finder();
$finder->files()
    ->in(__DIR__)
    ->name('*.php')
    ->contains('class')
    ->size('> 10K');

foreach ($finder as $file) {
    // $file est un SplFileInfo
}
```

## Points de vigilance (Certification)
*   **Iterator** : Le Finder implémente `IteratorAggregate`, on peut boucler dessus directement.

## Ressources
*   [Symfony Docs - Filesystem](https://symfony.com/doc/current/components/filesystem.html)
*   [Symfony Docs - Finder](https://symfony.com/doc/current/components/finder.html)

