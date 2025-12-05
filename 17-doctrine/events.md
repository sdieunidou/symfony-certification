# Événements Doctrine

## Concept
Doctrine permet de se brancher à différents moments du cycle de vie d'une entité pour exécuter de la logique (ex: mettre à jour une date `updatedAt`, hasher un mot de passe, uploader un fichier).

## Types d'Événements

*   `prePersist` : Avant l'insertion (INSERT). Idéal pour `createdAt`.
*   `postPersist` : Après l'insertion (l'ID est disponible).
*   `preUpdate` : Avant la mise à jour (UPDATE). Idéal pour `updatedAt`.
*   `postUpdate` : Après la mise à jour.
*   `preRemove` / `postRemove` : Autour de la suppression.
*   `postLoad` : Après le chargement depuis la base (find).
*   `onFlush` : Le plus puissant mais le plus complexe. Permet d'intervenir sur le calcul des changements (UnitOfWork).

## Lifecycle Callbacks (Interne à l'entité)
Simple et rapide pour des logiques internes (sans dépendance externe).

```php
#[ORM\HasLifecycleCallbacks]
class Product
{
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}
```

## Entity Listeners (Services externes)
Si vous avez besoin d'injecter des services (ex: Slugger, Mailer, Filesystem), utilisez un Entity Listener. C'est une classe séparée.

```php
#[ORM\EntityListeners([ProductListener::class])]
class Product { ... }

class ProductListener
{
    public function __construct(private SluggerInterface $slugger) {}

    #[ORM\PrePersist]
    public function prePersist(Product $product, LifecycleEventArgs $event): void
    {
        $product->setSlug($this->slugger->slug($product->getName()));
    }
}
```
Symfony enregistre et injecte automatiquement les dépendances dans ces listeners.

## Event Listeners / Subscribers
Moins performants que les Entity Listeners car ils se déclenchent pour **toutes** les entités (sauf filtrage manuel). À utiliser pour des comportements globaux (ex: Loggable extension).

## 🧠 Concepts Clés
1.  **Pas de `flush()` dans un listener** : Il est dangereux d'appeler `flush()` à l'intérieur d'un événement de cycle de vie (risque de boucle infinie).
2.  **PreUpdate** : Cet événement n'est déclenché QUE si l'entité a effectivement des changements.

## Ressources
*   [Doctrine Docs - Events](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/events.html)
*   [Symfony Docs - Doctrine Events](https://symfony.com/doc/current/doctrine/events.html)
