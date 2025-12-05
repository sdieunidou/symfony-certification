# Itérables, Générateurs et Mémoire

## Concept clé : Le type `iterable`
Introduit en PHP 7.1, le pseudo-type `iterable` regroupe deux concepts :
1.  Les **tableaux** (`array`).
2.  Les **objets** qui implémentent l'interface `Traversable` (c'est-à-dire qui peuvent être parcourus avec `foreach`).

C'est le type idéal à utiliser dans vos signatures de méthodes pour accepter indifféremment une liste en mémoire ou un flux de données.

```php
function analyserDonnees(iterable $donnees): void
{
    foreach ($donnees as $ligne) {
        // Traitement...
    }
}

analyserDonnees([1, 2, 3]); // Valide (array)
analyserDonnees(new ArrayIterator([1, 2, 3])); // Valide (objet Traversable)
```

## Array vs Itérateurs : Impact Mémoire

L'une des problématiques majeures des applications PHP (et Symfony) est la consommation mémoire (Memory Limit).

### 1. L'approche Array (En mémoire)
Charge **toutes** les données en RAM avant de commencer le traitement.
*   ✅ Rapide pour de petits volumes.
*   ✅ Accès aléatoire (`$data[42]`).
*   ❌ **Crash (Out of Memory)** si le volume dépasse la limite (ex: 1 million de lignes DB).

### 2. L'approche Itérateur (Lazy Loading)
Traite les données **une par une**, sans jamais tout stocker simultanément.
*   ✅ Empreinte mémoire constante et très faible (quelques Ko), quelle que soit la taille des données.
*   ✅ Permet de traiter des flux infinis.
*   ❌ Pas d'accès aléatoire (on ne peut pas aller directement à la ligne 5000 sans lire les 4999 avant).
*   ❌ `rewind()` (recommencer au début) n'est pas toujours possible (ex: flux socket).

**Exemple comparatif :**

```php
// Mauvaise pratique pour gros volumes : Crée un tableau géant en RAM
function getLargeDataArray(int $max): array {
    $data = [];
    for ($i = 0; $i < $max; $i++) {
        $data[] = $i;
    }
    return $data;
}

// Bonne pratique : Utilise un Générateur (Itérateur)
function getLargeDataGenerator(int $max): \Generator {
    for ($i = 0; $i < $max; $i++) {
        yield $i; // La valeur est émise, puis la mémoire est libérée
    }
}

// Test rapide
// $arr = getLargeDataArray(1_000_000); // ~32 Mo de RAM (dépend de PHP)
// $gen = getLargeDataGenerator(1_000_000); // < 1 Ko de RAM
```

## Les Générateurs (`yield`)

Les générateurs fournissent une façon simple de créer des itérateurs sans avoir à implémenter l'interface `Iterator` complète (qui est verbeuse).
Une fonction contenant le mot-clé `yield` renvoie automatiquement un objet de type `\Generator`.

### Fonctionnement
Quand la fonction est appelée, le code **ne s'exécute pas tout de suite**. Il retourne un objet `Generator`.
Le code s'exécute uniquement lorsque l'on itère dessus. À chaque `yield`, l'exécution se met en "pause" et la valeur est envoyée. Au tour suivant de boucle, l'exécution reprend exactement là où elle s'était arrêtée.

### Yield avec Clés
On peut émettre des paires clé/valeur.

```php
function getMap(): \Generator {
    yield 'id' => 123;
    yield 'nom' => 'Symfony';
}
```

### Yield From (Délégation)
Permet de déléguer l'itération à un autre itérable (tableau ou autre générateur).

```php
function countToTen(): \Generator {
    yield from [1, 2, 3];   // Émet 1, 2, 3
    yield from range(4, 5); // Émet 4, 5
    // ...
}
```

## Les Itérateurs de la SPL (Standard PHP Library)

PHP fournit une suite d'itérateurs robustes prêts à l'emploi pour éviter de réinventer la roue.

*   **`ArrayIterator`** : Transforme un tableau en itérateur. Utile pour typer un objet comme `Iterator` à partir d'un simple array.
*   **`DirectoryIterator`** / **`FilesystemIterator`** : Pour parcourir efficacement le système de fichiers (utilisé par le composant Finder de Symfony).
*   **`RecursiveDirectoryIterator`** : Pour parcourir une arborescence de dossiers récursivement.
*   **`LimitIterator`** : Décorateur pour ajouter une pagination (Offset/Limit) sur n'importe quel itérateur.
*   **`FilterIterator`** : Décorateur pour filtrer les éléments à la volée.
*   **`CallbackFilterIterator`** : Version plus simple de `FilterIterator` utilisant une closure.
*   **`NoRewindIterator`** : Empêche le retour au début (utile pour garantir qu'un flux n'est lu qu'une fois).

**Exemple : Parcourir un dossier sans Array**

```php
$dir = new \FilesystemIterator(__DIR__);
foreach ($dir as $fileInfo) {
    echo $fileInfo->getFilename() . "\n";
}
```

## Interfaces `Traversable`, `Iterator` et `IteratorAggregate`

1.  **`Traversable`** : L'interface mère. **On ne peut pas l'implémenter directement**. Elle sert juste à dire "cet objet peut aller dans un foreach".
2.  **`Iterator`** : L'interface complète si vous voulez créer un itérateur "à la main". Elle impose 5 méthodes :
    *   `current()` : Valeur actuelle.
    *   `key()` : Clé actuelle.
    *   `next()` : Avancer.
    *   `rewind()` : Revenir au début.
    *   `valid()` : Vérifier s'il y a encore des données.
3.  **`IteratorAggregate`** : L'interface la plus simple à utiliser dans vos classes. Elle impose une seule méthode `getIterator()` qui doit renvoyer un itérateur (souvent un `ArrayIterator` ou un `Generator`).

```php
// Exemple typique dans une classe métier
class Panier implements \IteratorAggregate, \Countable
{
    private array $produits = [];

    public function getIterator(): \Traversable
    {
        // On délègue l'itération à un ArrayIterator
        return new \ArrayIterator($this->produits);
        
        // OU on utilise un générateur pour transformer les données à la volée
        // foreach ($this->produits as $p) { yield $p; }
    }
    
    public function count(): int
    {
        return count($this->produits);
    }
}
```

## 🧠 Concepts Clés
1.  **Lazy Evaluation** : Les itérateurs/générateurs calculent la valeur "juste à temps" (JIT). Si vous arrêtez la boucle `foreach` au milieu (`break`), les valeurs suivantes ne sont jamais calculées (économie CPU/RAM).
2.  **Type de retour** : Si vous utilisez `yield`, le type de retour de la fonction doit être `\Generator` (ou `\Iterator`, `\Traversable`, `iterable`).
3.  **Doctrine** : Quand vous faites `$entity->getCollection()`, Doctrine renvoie une `PersistentCollection` qui implémente `Collection` (et donc `IteratorAggregate`). Les données ne sont chargées de la base que si vous itérez dessus (si le fetch est LAZY).

## ⚠️ Points de vigilance (Certification)
*   **`array` vs `iterable`** : `iterable` accepte les tableaux ET les objets. `array` n'accepte que les tableaux. Soyez précis dans vos type-hints.
*   **Rewindable** : Un générateur ne peut pas être "rewindé" (rembobiné) une fois que l'itération a commencé ou est finie, sauf s'il est recréé. Si vous devez itérer plusieurs fois sur la même source, un simple `Generator` posera problème (exception).
*   **Destruction** : Un objet Generator est détruit (et ses ressources libérées) dès qu'il n'est plus référencé ou que l'itération est finie.

## Ressources
*   [Manuel PHP - Générateurs](https://www.php.net/manual/fr/language.generators.overview.php)
*   [Manuel PHP - Itérateurs SPL](https://www.php.net/manual/fr/spl.iterators.php)
