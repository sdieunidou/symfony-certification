# Mapping ORM (Attributs)

## Concept
Le Mapping est l'art de dire à Doctrine comment faire correspondre une classe PHP (Entité) à une table de base de données.
Depuis PHP 8, les **Attributs** sont la méthode standard, remplaçant les Annotations (`@ORM\...`) et le YAML/XML (dépréciés ou moins utilisés).

## Configuration de base

Une entité est une simple classe PHP.

```php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'products')]
#[ORM\HasLifecycleCallbacks] // Si on utilise des callbacks comme PrePersist
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue] // Auto-increment
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;
    
    #[ORM\Column]
    private bool $isActive = true;

    // Getters & Setters...
}
```

## Types de Champs (DBAL Types)
Doctrine convertit les types SQL en types PHP et vice-versa.

*   `string` (VARCHAR)
*   `text` (CLOB/TEXT)
*   `integer`, `smallint`, `bigint`
*   `boolean`
*   `float` (DOUBLE PRECISION)
*   `decimal` (NUMERIC - pour l'argent, évite les erreurs d'arrondi flottant)
*   `datetime_immutable` (Recommandé vs `datetime`)
*   `json` (Stocke un array PHP sérialisé en JSON)
*   `simple_array` (CSV string, attention aux virgules)

## Enums (PHP 8.1+)
Doctrine supporte nativement les Enums PHP depuis la version 2.11+.

```php
#[ORM\Column(enumType: Status::class)]
private Status $status;
```

## Clés Primaires
*   **Auto-increment** : `#[GeneratedValue]` (Stratégie par défaut 'AUTO').
*   **UUID/ULID** : Symfony/Doctrine gère très bien les UUIDs (v4, v6, v7) pour éviter les IDs prédictibles.
    ```php
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;
    ```

## 🧠 Concepts Clés
1.  **Nullable** : Par défaut, `#[ORM\Column]` implique `nullable: false`. Si vous voulez autoriser le NULL en base, il faut explicitement `nullable: true`.
2.  **Propriétés typées** : Si vous typez votre propriété PHP (`private string $name`), Doctrine saura souvent déduire le type. Mais pour `string`, il faut préciser `length` sinon erreur SQL.

## Ressources
*   [Doctrine Docs - Basic Mapping](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/basic-mapping.html)
