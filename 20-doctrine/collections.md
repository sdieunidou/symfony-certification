# Collections & Criteria

## ArrayCollection
Dans une relation `OneToMany` ou `ManyToMany`, Doctrine initialise la propriété avec une instance de `PersistentCollection` (qui implémente l'interface `Collection` de Doctrine).
Dans le constructeur de votre entité, vous devez l'initialiser avec `ArrayCollection` (l'implémentation PHP standard).

```php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

public function __construct() {
    $this->tags = new ArrayCollection();
}
```

## Méthodes utiles
*   `add($element)` / `removeElement($element)`
*   `contains($element)`
*   `count()` : Attention, si la collection n'est pas chargée, `count()` peut déclencher un `COUNT(*)` SQL optimisé (Extra Lazy) ou charger toute la collection en mémoire (selon config).
*   `clear()` : Vide la collection. (Attention au `orphanRemoval`).

## Criteria (Filtrage de Collection)
Le système `Criteria` permet de filtrer une collection **sans écrire de DQL** et, surtout, **sans charger toute la collection en mémoire** si la relation est marquée `EXTRA_LAZY`.

```php
use Doctrine\Common\Collections\Criteria;

public function getActiveComments(): Collection
{
    $criteria = Criteria::create()
        ->where(Criteria::expr()->eq('isPublished', true))
        ->orderBy(['createdAt' => 'DESC']);

    return $this->comments->matching($criteria);
}
```
Si `$this->comments` n'est pas encore chargée, Doctrine générera une requête SQL intelligente (`SELECT ... WHERE is_published = 1`). Si elle est déjà en mémoire, le filtre se fera en PHP (array_filter). C'est une abstraction très puissante.

## Extra Lazy
C'est une option de mapping pour les collections.

```php
#[ORM\OneToMany(fetch: 'EXTRA_LAZY')]
```
Avec Extra Lazy, des opérations comme `$user->getPosts()->count()` ou `$user->getPosts()->contains($post)` ne chargent **PAS** tous les posts. Elles exécutent juste une petite requête SQL (`SELECT COUNT` ou `SELECT 1`).

## Ressources
*   [Doctrine Docs - Collections](https://www.doctrine-project.org/projects/doctrine-collections/en/stable/index.html)
