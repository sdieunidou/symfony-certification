# Validation : Fonctionnement Interne

## Concept clé
Le composant **Validator** vérifie la validité d'une donnée (objet, tableau, scalaire) par rapport à des règles appelées **Contraintes**. Il ne lance pas d'exception en cas d'erreur, mais retourne une liste de **Violations**.

## Architecture et Classes Clés

### 1. Validator (`ValidatorInterface`)
C'est le point d'entrée. L'implémentation par défaut est `RecursiveValidator`.
*   Il gère la traversée du graphe d'objets (Validation en cascade).
*   Il orchestre le chargement des métadonnées et l'appel aux validateurs de contraintes.

### 2. Constraint
Une classe simple qui représente une règle de validation (ex: `NotNull`, `Email`).
*   Elle contient les options de configuration (message d'erreur, payload, groups).
*   Elle ne contient **aucune logique** de validation.

### 3. ConstraintValidator
C'est la classe qui contient la logique de validation pour une contrainte donnée.
*   Exemple : `EmailValidator` vérifie si la valeur ressemble à un email.
*   Si la validation échoue, il ajoute une **Violation** via le `ExecutionContext`.

### 4. MetadataFactory
Avant de valider, Symfony doit savoir *quelles* contraintes s'appliquent à l'objet.
*   Le `MetadataFactory` lit la configuration (Attributs PHP, YAML, XML) et la met en cache.
*   Il produit un objet `ClassMetadata` contenant toutes les contraintes de la classe.

### 5. ExecutionContext
C'est l'objet "État" qui est passé de validateur en validateur.
*   Il stocke les violations trouvées (`ConstraintViolationList`).
*   Il connaît le "chemin" actuel dans le graphe d'objets (ex: `address.city`).
*   Il permet d'ajouter de nouvelles violations (`$context->buildViolation(...)`).

## Le Flux de Validation

1.  **Appel** : `$validator->validate($user)`.
2.  **Metadata** : Le Validator charge les métadonnées de la classe `User`.
3.  **Traversée** : Pour chaque propriété, getter ou contrainte de classe :
    *   Il instancie le `ConstraintValidator` approprié (si pas déjà fait).
    *   Il appelle `validate($value, $constraint)`.
4.  **Logique** : Le `ConstraintValidator` vérifie la valeur.
    *   Si invalide -> `$context->buildViolation('Erreur')->addViolation()`.
5.  **Cascade** : Si l'attribut `#[Valid]` est présent sur une relation (ex: `$user->address`), le Validator descend dans l'objet enfant et répète le processus.

## 🧠 Concepts Clés
1.  **Séparation Règle/Logique** : La `Constraint` est la définition (DTO), le `ConstraintValidator` est le service (Logique).
2.  **Groupes** : Le Validator ne valide que les contraintes appartenant au(x) groupe(s) demandé(s) (Par défaut: `Default`).

## ⚠️ Points de vigilance (Certification)
*   **Services** : Les `ConstraintValidator` sont définis comme des services. Ils peuvent donc avoir des dépendances injectées (ex: `RequestStack`, `EntityManager`).
*   **ViolationList** : `validate()` retourne toujours un objet `ConstraintViolationListInterface`, jamais `true/false`. Il faut vérifier `count($errors) > 0`.

## Ressources
*   [Symfony Docs - Validation Internals](https://symfony.com/doc/current/validation.html)
