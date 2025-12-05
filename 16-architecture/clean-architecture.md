# Clean Architecture (Hexagonale / Onion)

## Concept Clé
L'objectif est de créer des systèmes :
1.  Indépendants des Frameworks.
2.  Testables.
3.  Indépendants de l'UI.
4.  Indépendants de la Base de Données.
5.  Indépendants de tout agent externe.

## La Règle de Dépendance
C'est la règle absolue : **Les dépendances de code source ne peuvent pointer que vers l'intérieur.**
Rien dans un cercle interne ne peut connaître quelque chose d'un cercle externe.

## Les Couches (Cercles)

### 1. Enterprise Business Rules (Entities / Domain) - Le Cœur
*   **Contenu** : Objets métier, règles métier de l'entreprise (universelles).
*   **Dépendances** : Aucune. PHP Pur.
*   **Exemple** : Entité `User`, VO `Email`, Service `PricingCalculator`.

### 2. Application Business Rules (Use Cases)
*   **Contenu** : Règles métier spécifiques à l'application. Orchestration.
*   **Rôle** : Prend les données de l'utilisateur, les passe au Domaine, et retourne le résultat.
*   **Dépendances** : Dépend du Domaine.
*   **Exemple** : `RegisterUserHandler`, `FindProductQuery`.

### 3. Interface Adapters (Infrastructure / Adapters)
*   **Rôle** : Convertir les données du format pratique pour les Use Cases et Entities vers le format pratique pour les agents externes (DB, Web).
*   **Contenu** :
    *   *Contrôleurs* : Reçoivent la Request HTTP, créent la Commande, appellent le Handler.
    *   *Présenteurs* : Formattent la réponse pour la vue.
    *   *Gateways (Implémentations)* : Repository Doctrine, Client API Stripe, Mailer SMTP.
*   **Dépendances** : Dépend des Use Cases (via interfaces).

### 4. Frameworks & Drivers (External)
*   **Contenu** : Le Framework (Symfony), la Base de Données (Postgres), le Frontend (Twig/React).
*   **Rôle** : Détails techniques. On veut pouvoir en changer avec un minimum d'impact.

## Pattern "Ports & Adapters" (Hexagonale)
C'est une variante très proche.
*   **Hexagone (Cœur)** : Domaine + Application.
*   **Ports (Interfaces)** : Ce que le cœur expose (API In) ou demande (SPI Out).
    *   *Input Port (Primary)* : Interface du Use Case (ex: `RequestHandlerInterface`).
    *   *Output Port (Secondary)* : Interface du Repository (`UserRepositoryInterface`).
*   **Adapters (Implémentations)** :
    *   *Driving Adapter (Input)* : Controller Symfony, Command Console.
    *   *Driven Adapter (Output)* : Doctrine Repository, SwiftMailer.

## Implémentation Concrète dans Symfony

### Structure de dossiers "Screaming Architecture"
```text
src/
|-- User/                       # Contexte
|   |-- Domain/                 # Cercle 1 (Interne)
|   |   |-- User.php
|   |   |-- UserRepositoryInterface.php  <-- Output Port
|   |
|   |-- Application/            # Cercle 2
|   |   |-- Register/
|   |   |   |-- RegisterUserCommand.php  <-- DTO Input
|   |   |   |-- RegisterUserHandler.php  <-- Use Case
|   |
|   |-- Infrastructure/         # Cercle 3 & 4 (Externe)
|       |-- Controller/
|       |   |-- RegistrationController.php <-- Driving Adapter
|       |-- Doctrine/
|           |-- DoctrineUserRepository.php <-- Driven Adapter
```

### Inversion de Dépendance (DIP)
Comment le Handler (Application) peut-il sauvegarder en base s'il ne doit pas dépendre de Doctrine (Infra) ?
1.  **Application** définit l'interface `UserRepositoryInterface`.
2.  **Application** utilise cette interface (type-hint).
3.  **Infrastructure** implémente l'interface avec `DoctrineUserRepository`.
4.  **Symfony (DI)** injecte l'implémentation dans le Handler au runtime.
-> Le flux de contrôle va vers la DB, mais la dépendance de code source pointe vers l'intérieur (vers l'interface).

### Request / Response Flow
1.  **Request HTTP** arrive sur Symfony.
2.  **Controller** : Extrait les données et crée un **Request Model** (DTO / Command).
3.  **Bus** : Passe le DTO au **Handler** (Use Case).
4.  **Handler** :
    *   Valide le DTO.
    *   Charge les **Entités** via le **Repository** (Interface).
    *   Exécute la logique métier sur les Entités.
    *   Sauvegarde via le Repository.
    *   Retourne un **Response Model** (DTO) ou void.
5.  **Controller** : Transforme le Response Model en `JsonResponse` ou `Response` (Twig).

## Boundary (Frontière)
Il est crucial de maintenir des frontières strictes.
*   Ne jamais passer l'`Entity` directement au Controller ou à la Vue (risque de Lazy Loading, exposition de données sensibles).
*   Utiliser des **DTOs** pour traverser les frontières.

## Avantages
*   **Testabilité** : Tester les Use Cases sans boot le Kernel ni la DB (Tests Unitaires purs).
*   **Flexibilité** : Changer de MySQL à MongoDB ne demande que de réécrire l'Adapter Repository.
*   **Maintenance** : Le code métier est isolé du "bruit" du framework.

## Ressources
*   *Clean Architecture* (Robert C. Martin - Uncle Bob)
