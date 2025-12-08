# SPL (Standard PHP Library)

## Concept clé
La SPL est une collection d'interfaces et de classes orientées objet intégrées au cœur de PHP pour résoudre des problèmes standards. Elle n'est pas une extension désactivable.
Elle transforme PHP d'un langage de script en un langage applicatif robuste.

## Application dans Symfony 7.0
Symfony est bâti sur la SPL.
*   **`SplFileInfo`** : Base de la gestion de fichiers (`UploadedFile`, `Finder`).
*   **`Iterator`** : Le composant Finder renvoie des itérateurs pour parcourir efficacement des millions de fichiers sans saturation mémoire.
*   **`ArrayAccess`** : Permet d'accéder à des objets comme à des tableaux (ex: `$session['key']`).
*   **`Countable`** : Permet d'utiliser `count($obj)`.

## Structures de Données (Data Structures)
PHP propose des structures plus optimisées que le simple `array` pour des cas spécifiques.
*   **`SplDoublyLinkedList`**, **`SplStack`** (LIFO), **`SplQueue`** (FIFO).
*   **`SplHeap`**, **`SplMinHeap`**, **`SplMaxHeap`** (Tas pour tri).
*   **`SplFixedArray`** : Tableau à taille fixe, clés numériques uniquement. Plus rapide et moins gourmand en mémoire que `array`.
*   **`SplObjectStorage`** : Permet d'utiliser des **objets comme clés** dans un map (ce que le `array` natif ne permet pas) et de leur associer des données. Très utilisé dans l'Unit of Work de Doctrine.

## Autoloading
La fonction centrale de la SPL moderne est **`spl_autoload_register`**.
Elle permet d'enregistrer plusieurs fonctions qui seront appelées séquentiellement quand PHP rencontre une classe inconnue. C'est le moteur de **Composer**.

```php
spl_autoload_register(function ($class) {
    // Logique pour trouver le fichier de la classe $class et l'inclure
    include 'classes/' . $class . '.class.php';
});
```

## Les Itérateurs (Iterators)
La gestion des itérateurs et des générateurs est traitée en détail dans le cours dédié.

La SPL fournit néanmoins les implémentations concrètes (comme `DirectoryIterator` ou `ArrayIterator`).

## Exceptions SPL
La SPL fournit une hiérarchie d'exceptions standards à utiliser de préférence aux exceptions génériques.
1.  **LogicException** (Problème de code/développeur)
    *   `DomainException` : Valeur hors du domaine valide (logique).
    *   `InvalidArgumentException` : Argument méthode incorrect.
    *   `BadMethodCallException`.
2.  **RuntimeException** (Problème d'exécution/environnement)
    *   `OutOfBoundsException` : Index invalide.
    *   `OverflowException` / `UnderflowException`.
    *   `UnexpectedValueException`.

## 🧠 Concepts Clés
1.  **Interfaces Magiques** :
    *   `Traversable` : Interface mère de `Iterator` et `IteratorAggregate`. Seul l'interpréteur PHP peut l'implémenter. Vos classes doivent implémenter `Iterator` ou `IteratorAggregate` pour être utilisables dans un `foreach`.
    *   `IteratorAggregate` : Plus simple à implémenter. On définit juste `getIterator()` qui renvoie un itérateur externe (souvent un `ArrayIterator` ou un `Generator`).
2.  **Performance** : Les structures `Spl*` sont implémentées en C. Elles sont souvent plus performantes pour des usages spécifiques (Queue, Stack) que l'utilisation de `array_push/pop`.

## ⚠️ Points de vigilance (Certification)
*   **ArrayAccess n'est pas itérable** : Implémenter `ArrayAccess` permet `$obj['key']`, mais ne permet pas `foreach ($obj)`. Pour le foreach, il faut `Iterator` ou `IteratorAggregate`.
*   **Exceptions** : La certification demande souvent de choisir la "meilleure" exception pour un scénario donné.
    *   Ex: "Un argument passé est du bon type mais négatif alors qu'attendu positif" -> `InvalidArgumentException`.
    *   Ex: "Impossible d'écrire dans le fichier car disque plein" -> `RuntimeException`.

## Ressources
*   [Manuel PHP - SPL](https://www.php.net/manual/fr/book.spl.php)
*   [Manuel PHP - Interfaces prédéfinies](https://www.php.net/manual/fr/reserved.interfaces.php)
