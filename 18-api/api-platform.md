# API Platform

## Qu'est-ce que c'est ?
API Platform n'est pas un simple bundle, c'est un **framework complet** construit au-dessus de Symfony pour créer des APIs hypermedia (REST & GraphQL) modernes.
Il est le standard de facto dans l'écosystème Symfony pour les projets API-centric.

## Philosophie
Au lieu de créer manuellement des Contrôleurs, des Routes, et de la Sérialisation pour chaque entité, API Platform automatise tout cela en se basant sur vos **Entités** (ou DTOs).

Il gère nativement :
*   CRUD complet (GET, POST, PUT, PATCH, DELETE).
*   Pagination.
*   Filtres (Recherche, Tri, Date...).
*   Validation (via Symfony Validator).
*   Sérialisation (via Symfony Serializer).
*   Documentation (OpenAPI / Swagger UI).
*   Formats modernes (JSON-LD, Hydra, HAL, JSON:API).
*   GraphQL.

## Exemple Minimaliste

```php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ApiResource] // <--- Cette seule ligne génère toute l'API REST pour cette entité !
class Book
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column]
    public ?string $title = null;
}
```

## Quand l'utiliser vs Symfony Core ?

| Critère | Symfony Core (Manuelle) | API Platform |
| :--- | :--- | :--- |
| **Complexité** | Vous écrivez tout (Contrôleur, DTO, Route). Contrôle total. | Magique au début. Nécessite de l'apprentissage pour les cas complexes (State Providers/Processors). |
| **Vitesse de dev** | Plus lent pour du CRUD standard. | Extrêmement rapide pour démarrer. |
| **Standards** | Vous devez implémenter les standards (Pagination, RFCs) vous-même. | Standards web (JSON-LD, Hydra) intégrés par défaut. |
| **Cas d'usage** | Actions métier complexes "RPC-style" (ex: `/api/cart/checkout`). | Gestion de ressources "REST-style" (ex: `/api/books`). |

Il est tout à fait possible (et courant) de mélanger les deux dans le même projet : API Platform pour les ressources standards, et des contrôleurs Symfony custom pour les actions métier très spécifiques.

## 🧠 Concepts Clés
1.  **Resource** : L'unité de base d'API Platform. Une classe PHP exposée via l'API.
2.  **State Provider** : La classe qui va chercher les données (remplace le Repository/Controller GET).
3.  **State Processor** : La classe qui gère les changements (remplace le Controller POST/PUT/DELETE).
4.  **DTOs** : API Platform encourage désormais l'utilisation de DTOs (via `input` et `output`) pour séparer la ressource API de l'entité Doctrine.

## Ressources
*   [Site Officiel API Platform](https://api-platform.com/)
