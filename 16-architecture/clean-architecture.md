# Clean Architecture avec Symfony

## Concept Clé
Popularisée par "Uncle Bob", la Clean Architecture vise à séparer les préoccupations en couches concentriques.
L'objectif : **L'indépendance**.
1.  Indépendant du Framework.
2.  Indépendant de l'UI.
3.  Indépendant de la Base de Données.

**Règle de dépendance** : Les dépendances ne vont que vers l'intérieur. Le Cœur ne connaît rien de la DB ou du Web.

## Les Couches (Layers)

### 1. Domain (Entities)
Le centre. Règles métier pures. Aucun framework, aucune annotation Doctrine. Juste du PHP pur.

### 2. Use Cases (Application)
Orchestration des règles métier. Contient les `CommandHandler`, `QueryHandler`.
Définit les interfaces (Ports) pour accéder aux données (`UserRepositoryInterface`).

### 3. Interface Adapters (Infrastructure)
C'est ici que Symfony vit.
*   Contrôleurs (Web).
*   Commandes Console (CLI).
*   Implémentations Doctrine des Repositories.
*   Services d'envoi de mail (Mailer).

## Implémentation Symfony
Pour respecter Clean Arch, on évite le couplage fort :
*   Pas d'annotations Doctrine (`#[ORM\Entity]`) directement sur les classes du Domaine (on utilise le mapping XML/YAML externe ou on sépare le modèle de persistance du modèle de domaine).
*   Les Contrôleurs ne contiennent aucune logique, ils appellent des Use Cases (via Messenger par exemple).
*   Le Domaine ne dépend pas de `Symfony\Component\...`.

### Exemple : Créer un User

1.  **Domain** : Classe `User` (sans ORM), Interface `UserRepositoryInterface`.
2.  **Application** : `CreateUserCommand` (DTO), `CreateUserHandler` (Logique).
3.  **Infra** :
    *   `UserController` (reçoit Request, dispatche Command).
    *   `DoctrineUserRepository` (implémente l'interface, parle à la DB).
    *   `mapping/User.orm.xml` (colle le Domain à la DB).

## Avantages vs Inconvénients
*   **+** : Testabilité extrême (Tests unitaires faciles sur le domaine).
*   **+** : Évolutivité (On peut changer Doctrine pour autre chose).
*   **-** : Complexité et verbosité (beaucoup de fichiers, DTOs, mappers).
*   **-** : Perte de la rapidité de développement Symfony ("Rapid Application Development").

*Note : Souvent, une architecture "Hexagonale" (Ports & Adapters) simplifiée est un meilleur compromis pour Symfony qu'une Clean Arch puriste.*

