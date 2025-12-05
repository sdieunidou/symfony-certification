# Domain Driven Design (DDD) dans Symfony

## Concept Clé
Le DDD est une approche de conception logicielle qui se concentre sur la complexité métier. Il ne s'agit pas d'une architecture technique (comme MVC), mais d'une façon de modéliser le logiciel pour qu'il reflète fidèlement la réalité du métier.

## 1. Strategic Design (La Vision)
C'est la partie "Politique" et "Organisationnelle". Elle permet de définir **quoi** construire et **comment** découper le système.

### Ubiquitous Language (Langage Omniprésent)
*   **Définition** : Un langage commun rigoureux partagé par les développeurs et les experts métier.
*   **Règle** : Le code doit parler ce langage.
    *   *Métier* : "Un client passe une commande."
    *   *Code* : `$customer->placeOrder($order)` (et non `$orderDAO->save($order)`).
*   **Bénéfice** : Élimine les ambiguïtés et la traduction mentale.

### Bounded Contexts (Contextes Délimités)
*   **Définition** : Une frontière explicite à l'intérieur de laquelle un modèle s'applique.
*   **Problème** : Un mot a des sens différents selon le contexte.
    *   *Contexte Vente* : `Product` (Prix, Description, Images).
    *   *Contexte Logistique* : `Product` (Poids, Dimensions, Emplacement Entrepôt).
*   **Solution** : Ne pas créer une classe `Product` géante. Créer deux modèles distincts dans deux contextes séparés (`Sales\Product` et `Shipping\Product`) qui peuvent être mappés par ID.

### Context Mapping
Comment les contextes communiquent entre eux :
*   **Shared Kernel** : Code partagé (rare et risqué).
*   **Customer/Supplier** : Une équipe fournit une API à l'autre.
*   **Anti-Corruption Layer (ACL)** : Une couche de traduction pour empêcher un modèle externe (legacy ou autre contexte) de polluer notre modèle pur.

## 2. Tactical Design (L'Implémentation)
C'est la boîte à outils pour construire le modèle du domaine.

### Value Objects (VO)
*   **Définition** : Objet défini par ses attributs, sans identité propre. Immutable.
*   **Exemples** : `Email`, `Color`, `Money`, `Address`, `GpsCoordinates`.
*   **Égalité** : Deux VOs sont égaux si toutes leurs propriétés sont égales.
*   **Symfony** : Utiliser les "Embeddables" Doctrine ou des Types Custom.

```php
// Value Object
final class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {}

    public function add(Money $other): self
    {
        if ($this->currency !== $other->currency) {
            throw new DomainException('Currency mismatch');
        }
        return new self($this->amount + $other->amount, $this->currency);
    }
}
```

### Entities (Entités)
*   **Définition** : Objet défini par une identité (ID) qui persiste dans le temps. Mutable (mais contrôlé).
*   **Cycle de vie** : A une naissance et une mort.
*   **Entité Riche** : Contient la logique métier (méthodes `changeAddress()`, `activate()`) plutôt que des setters anémiques (`setAddress()`, `setStatus()`).

### Aggregates (Agrégats)
*   **Définition** : Une grappe d'entités et de VOs traités comme une unité de cohérence.
*   **Aggregate Root (Racine)** : La seule entité accessible de l'extérieur.
*   **Règle** : On ne manipule jamais les enfants directement.
    *   *Exemple* : `Order` (Racine) contient `OrderLine` (Enfant).
    *   *Interdit* : `$orderLineRepo->save($line)`.
    *   *Obligatoire* : `$order->addLine($item); $orderRepo->save($order)`.
*   **Transaction** : Une transaction de base de données ne doit modifier qu'un seul agrégat à la fois (idéalement).

### Domain Services
*   **Définition** : Opération métier qui ne "rentre" pas naturellement dans une Entité ou un VO.
*   **Usage** : Souvent pour des opérations impliquant plusieurs entités.
*   **Exemple** : `FundTransferService::transfer(Account $from, Account $to, Money $amount)`. Ce n'est pas la responsabilité de A de connaître B.

### Domain Events
*   **Définition** : Quelque chose qui s'est passé dans le passé et qui intéresse le métier.
*   **Nommage** : Verbe au passé (`OrderPlaced`, `UserRegistered`).
*   **Utilité** : Découpler les systèmes (Side effects).
    *   1. L'Agrégat enregistre l'événement : `$this->recordEvent(new OrderPlaced($this->id))`.
    *   2. Au `flush()`, les événements sont dispatchés (via Messenger).
    *   3. Des Listeners réagissent (Envoi mail, Logistique, etc.).

### Repositories
*   **Rôle** : Illusion d'une collection en mémoire.
*   **Interface** : Définie dans le Domaine (`OrderRepositoryInterface`).
*   **Implémentation** : Définie dans l'Infrastructure (`DoctrineOrderRepository`).
*   **Contrat** : `save(Aggregate $root)`, `getById(Identity $id)`, `remove(Aggregate $root)`.

### Factories
*   **Rôle** : Création d'agrégats complexes.
*   **Usage** : Encapsuler la logique de construction, surtout si elle nécessite des invariants forts dès la création.

## Implémentation Symfony Recommandée
1.  **Dossiers** : `src/Context/Domain`, `src/Context/Application`, `src/Context/Infrastructure`.
2.  **Doctrine** : Mapper les classes du domaine (XML/PHP Attributes) mais garder les classes "pures" (éviter les dépendances directes à l'ORM dans le code métier si possible, ou être pragmatique).
3.  **Messenger** : Pour les Commandes (Application) et les Domain Events.

## Ressources
*   *Domain-Driven Design* (Eric Evans)
*   *Implementing Domain-Driven Design* (Vaughn Vernon)
