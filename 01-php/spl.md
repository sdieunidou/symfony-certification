# SPL (Standard PHP Library)

## Concept clé
La SPL est une collection d'interfaces et de classes orientées objet pour résoudre des problèmes classiques, intégrée par défaut à PHP. Elle couvre principalement :
*   **Structures de données** : `SplStack`, `SplQueue`, `SplHeap`, `SplFixedArray`.
*   **Itérateurs** : `ArrayIterator`, `DirectoryIterator`, `RecursiveIteratorIterator`.
*   **Exceptions** : `LogicException`, `RuntimeException` et leurs enfants.
*   **Autoloading** : `spl_autoload_register`.
*   **Fichiers** : `SplFileInfo`, `SplFileObject`.

## Application dans Symfony 7.0
Symfony s'appuie fortement sur la SPL.
*   Le composant **Finder** utilise intensément les itérateurs SPL (`RecursiveDirectoryIterator`).
*   Le composant **HttpFoundation** étend `SplFileInfo` pour la gestion des fichiers uploadés (`UploadedFile`).
*   Les exceptions standards (`InvalidArgumentException`, `LogicException`) sont utilisées partout.

## Exemple de code

```php
<?php

// Utilisation de SplFileInfo
$info = new \SplFileInfo('/path/to/file.txt');
echo $info->getExtension(); // txt
echo $info->getSize(); // taille en octets

// Utilisation de Countable et IteratorAggregate (Interfaces SPL/Core)
class Panier implements \Countable, \IteratorAggregate
{
    private array $items = [];

    public function count(): int
    {
        return count($this->items);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }
}
```

## Points de vigilance (Certification)
*   **Exceptions** : Connaître la différence sémantique entre `LogicException` (erreur de code, bug développeur) et `RuntimeException` (erreur dépendant de l'environnement ou des données à l'exécution).
*   **Structures de données** : Savoir que `SplFixedArray` est plus rapide et consomme moins de mémoire qu'un `array` PHP classique pour des tableaux de taille fixe indexés numériquement.
*   **Interfaces** : Connaître `Countable` (permet `count($obj)`), `ArrayAccess` (permet `$obj['key']`), `Traversable` (interface parente de `Iterator` et `IteratorAggregate`, ne peut pas être implémentée directement par une classe utilisateur).

## Ressources
*   [Manuel PHP - SPL](https://www.php.net/manual/fr/book.spl.php)
*   [Manuel PHP - Exceptions SPL](https://www.php.net/manual/fr/spl.exceptions.php)

