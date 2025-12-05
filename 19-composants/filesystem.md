# Composant Filesystem

## Concept clé
PHP fournit des fonctions natives (`copy`, `mkdir`, `unlink`), mais elles manquent de cohérence (gestion d'erreurs) et de portabilité (Windows vs Linux).
Le composant `Filesystem` fournit une abstraction orientée objet robuste et multiplateforme pour les opérations sur les fichiers et les chemins.

## Installation
```bash
composer require symfony/filesystem
```

## Classes Principales
Le composant expose deux classes principales :
1.  `Symfony\Component\Filesystem\Filesystem` : Pour les opérations sur le système de fichiers (création, suppression, copie...).
2.  `Symfony\Component\Filesystem\Path` : Pour la manipulation de chaînes de caractères représentant des chemins (normalisation, jointure...).

## Utilisation de `Filesystem`

```php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

$filesystem = new Filesystem();

try {
    // Création récursive (mkdir -p)
    // Mode par défaut : 0777 (modifié par umask)
    $filesystem->mkdir('/tmp/random/dir', 0700);

    // Vérification d'existence (accepte string ou array)
    if ($filesystem->exists(['/tmp/file.txt', '/tmp/dir'])) {
        // ...
    }

    // Copie (fichier seulement, utiliser mirror pour dossiers)
    // 3ème arg: override (true par défaut pour écraser si plus récent)
    $filesystem->copy('source.txt', 'dest.txt', true);

    // Création/Modification de timestamp (touch)
    $filesystem->touch('file.txt', time() + 10, time() - 10);

    // Changement de propriétaire/groupe/mode (chmod/chown/chgrp)
    // Supporte la récursivité (dernier argument boolean)
    $filesystem->chown('file.txt', 'www-data');
    $filesystem->chmod('dir/', 0755, 0000, true);

    // Suppression (rm -rf) - Accepte string ou array
    $filesystem->remove(['symlink', '/path/to/directory', 'activity.log']);

    // Renommage
    $filesystem->rename('/tmp/old', '/tmp/new', true); // true = overwrite

    // Liens symboliques
    // Crée un lien (ou une copie si l'OS ne supporte pas les liens)
    $filesystem->symlink('/path/to/source', '/path/to/link');
    
    // Lecture de lien
    // true = canonique (résout les liens imbriqués)
    $path = $filesystem->readlink('/path/to/link', true); 

    // Écriture Atomique (dumpFile)
    // Écrit dans un fichier temporaire puis renomme (évite corruption)
    $filesystem->dumpFile('file.txt', 'Hello World');
    
    // Append
    $filesystem->appendToFile('logs.txt', 'New Line');

} catch (IOExceptionInterface $exception) {
    echo "Erreur sur le chemin : " . $exception->getPath();
}
```

## Utilisation de `Path` (Manipulation de chemins)

La classe statique `Path` normalise les séparateurs (`/` vs `\`) et gère les chemins relatifs/absolus.

```php
use Symfony\Component\Filesystem\Path;

// Jointure propre (gère les slashs en trop/manquants)
echo Path::join('/var/www', 'vhost', 'config.ini'); 
// => /var/www/vhost/config.ini

// Normalisation (nettoie les /./ et /../)
echo Path::normalize('/var/www/../lib'); 
// => /var/lib

// Conversion Relatif <-> Absolu
echo Path::makeAbsolute('config.yml', '/var/www'); 
// => /var/www/config.yml

echo Path::makeRelative('/var/www/config.yml', '/var/www/html'); 
// => ../config.yml

// Vérifications
Path::isAbsolute('C:\Windows'); // true
Path::isBasePath('/var/www', '/var/www/html/index.php'); // true

// Racine et Répertoire (fixe les quirks de dirname() natif)
echo Path::getRoot('/etc/nginx'); // => /
echo Path::getDirectory('C:\Programs'); // => C:/ (et non C:)

// Plus long chemin de base commun
$base = Path::getLongestCommonBasePath(
    '/var/www/html/index.php',
    '/var/www/html/css/style.css'
); 
// => /var/www/html
```

## 🧠 Concepts Clés & Certification
1.  **Atomicité** : `dumpFile()` est atomique. Elle garantit que le fichier n'est pas lu à moitié écrit par un autre processus.
2.  **Exceptions** : Le composant lance `Symfony\Component\Filesystem\Exception\IOException` (qui implémente `IOExceptionInterface`) en cas d'erreur (permission refusée, disque plein...), contrairement aux fonctions natives qui émettent des Warnings.
3.  **Mirroring** : Pour copier un **dossier entier**, il faut utiliser `$filesystem->mirror($originDir, $targetDir)`. `copy()` ne fonctionne que pour les fichiers.
4.  **Sécurité** : `Path::canonicalize()` (utilisé par `normalize`) aide à prévenir les attaques par traversée de répertoire (Directory Traversal) en résolvant les `..`.

## Ressources
*   [Symfony Docs - Filesystem](https://symfony.com/doc/current/components/filesystem.html)
