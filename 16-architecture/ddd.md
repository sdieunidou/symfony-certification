# Domain Driven Design (DDD) dans Symfony

## Concept Clé
Le DDD n'est pas un pattern de code, mais une approche de conception logicielle centrée sur le **Domaine Métier** (le problème réel à résoudre) plutôt que sur la technique (Base de données, Framework).

On distingue :
*   **Strategic Design** : Découpage en Contextes Bornés (Bounded Contexts), Langage Ubiquitaire (Ubiquitous Language).
*   **Tactical Design** : Patterns de code (Entity, Value Object, Aggregate, Repository, Domain Service).

## Application Tactique dans Symfony

### 1. Structure des dossiers
On s'éloigne du découpage standard `src/Entity`, `src/Controller`. On découpe par contexte.
```
src/
  Catalog/        # Bounded Context Catalogue
    Domain/       # Le Cœur (Agnostique du framework)
      Entity/
      ValueObject/
      Repository/ # Interfaces uniquement
    Application/  # Cas d'utilisation (Handlers, DTOs)
    Infrastructure/ # Implémentations (Doctrine, Symfony Controllers)
```

### 2. Value Objects (VO)
Objets immuables définis par leur valeur, pas leur identité.
Exemple : `Email`, `Money`, `GpsCoordinates`.
*Symfony* : On utilise les `Embeddables` Doctrine ou des Types DB personnalisés pour les persister.

```php
#[ORM\Embeddable]
class Money
{
    #[ORM\Column(type: 'integer')]
    private int $amount;
    
    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;
    
    // Constructeur, Getters, mais PAS de Setters (Immuable)
    public function add(Money $other): self { ... }
}
```

### 3. Entités & Agrégats
*   **Entité** : Objet défini par une identité (ID). Elle possède un cycle de vie.
*   **Agrégat** : Grappe d'entités traitées comme une unité. L'accès se fait uniquement via la racine de l'agrégat (Aggregate Root).
*   **Règle** : Une entité ne doit pas être anémique (juste des Getters/Setters). Elle doit contenir des méthodes métier (`$order->ship()` au lieu de `$order->setStatus('shipped')`).

### 4. Repository
En DDD, le Repository est une collection d'objets en mémoire.
L'interface est dans le Domaine, l'implémentation (Doctrine) est dans l'Infrastructure.

```php
// Domain/Repository/ProductRepositoryInterface.php
interface ProductRepositoryInterface {
    public function save(Product $product): void;
}

// Infrastructure/Doctrine/DoctrineProductRepository.php
class DoctrineProductRepository implements ProductRepositoryInterface { ... }
```

## Ressources
*   Livre "Domain-Driven Design" (Eric Evans).
*   Livre "Implementing Domain-Driven Design" (Vaughn Vernon).

