# Composant Filesystem

## Concept clé
PHP fournit des fonctions natives (`copy`, `mkdir`, `unlink`), mais elles manquent de cohérence (gestion d'erreurs) et de portabilité (Windows vs Linux).
Le composant `Filesystem` fournit une abstraction orientée objet robuste.

## Utilisation

```php
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

$filesystem = new Filesystem();

try {
    // Création récursive (mkdir -p)
    $filesystem->mkdir('/tmp/random/dir');

    // Copie (fichier ou dossier)
    $filesystem->copy('/tmp/foo', '/tmp/bar');

    // Création de fichier (touch)
    $filesystem->touch('/tmp/file.txt');

    // Suppression (rm -rf)
    $filesystem->remove(['/tmp/file.txt', '/tmp/random']);

    // Changement de propriétaire/groupe/mode (chmod/chown)
    $filesystem->chmod('/tmp/bar', 0700);
    
    // Gestion des liens symboliques
    $filesystem->symlink('/path/to/target', '/path/to/link');
    
    // Chemin relatif
    $path = $filesystem->makePathRelative('/var/lib/symfony', '/var/lib'); 
    // retourne 'symfony/'

} catch (IOExceptionInterface $exception) {
    echo "Erreur : " . $exception->getPath();
}
```

## 🧠 Concepts Clés
1.  **Atomicité** : Certaines opérations (comme `dumpFile`) sont atomiques (écrit dans un fichier temporaire puis renomme) pour éviter la corruption de fichier en cas de crash pendant l'écriture.
2.  **Exceptions** : Lance toujours `IOException` en cas d'échec, ce qui est plus propre que les warnings PHP.

## ⚠️ Points de vigilance (Certification)
*   **Exists** : `exists()` vérifie l'existence d'un fichier ou dossier.
*   **Temp** : `tempnam()` crée un fichier temporaire avec un préfixe unique.

## Ressources
*   [Symfony Docs - Filesystem](https://symfony.com/doc/current/components/filesystem.html)
