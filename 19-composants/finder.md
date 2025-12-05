# Composant Finder

## Concept clé
Le composant **Finder** permet de rechercher des fichiers et des répertoires de manière fluide et intuitive.
Il remplace les fonctions natives `scandir`, `glob` ou `RecursiveDirectoryIterator` qui sont souvent verbeuses et complexes à utiliser pour des filtrages avancés.

## Utilisation

```php
use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->files()
    ->in(__DIR__)
    ->name('*.php')
    ->notName('*Test.php')
    ->contains('class')
    ->size('< 1K')
    ->date('since yesterday');

foreach ($finder as $file) {
    // $file est une instance de SplFileInfo
    echo $file->getRealPath() . "\n";
}
```

## 🧠 Concepts Clés
1.  **Itérateur** : L'objet `Finder` n'exécute la recherche que lorsque vous itérez dessus (foreach). Il implémente `IteratorAggregate`.
2.  **Fluent Interface** : Toutes les méthodes de filtrage renvoient `$this`, permettant de les chaîner.
3.  **Sécurité** : Attention si vous passez des entrées utilisateur dans `in()` ou `path()`.

## Ressources
*   [Symfony Docs - Finder](https://symfony.com/doc/current/components/finder.html)
