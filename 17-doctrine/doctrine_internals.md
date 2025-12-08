# Doctrine : Fonctionnement Interne

## Concept clé
Doctrine ORM est une implémentation du pattern **Data Mapper**. Contrairement à Active Record (Eloquent, Laravel), l'entité est une classe PHP pure (POJO) qui ne connaît pas la base de données. L'**EntityManager** est le chef d'orchestre qui persiste ces objets.

## Architecture et Classes Clés

### 1. Entity (L'objet)
Une classe PHP simple (`User`). Elle contient des données mais **aucune logique de persistance** (pas de méthode `save()`).

### 2. EntityManager (`EntityManagerInterface`)
C'est le point d'entrée principal.
*   Il gère le cycle de vie des entités.
*   Il maintient la **UnitOfWork**.
*   Méthodes : `persist()`, `remove()`, `flush()`, `find()`.

### 3. UnitOfWork (L'Unité de Travail)
C'est le cerveau caché de Doctrine.
*   Elle tracke tous les changements sur les objets gérés (Managed).
*   Elle calcule le **Changeset** (différence entre l'état initial et actuel).
*   Lors du `flush()`, elle génère les requêtes SQL (INSERT, UPDATE, DELETE) optimisées.

### 4. Repository
Responsable de la récupération des données (`SELECT`).
*   Utilise le **QueryBuilder** ou le **DQL** (Doctrine Query Language).

### 5. Proxy
Pour le **Lazy Loading**, Doctrine ne retourne pas toujours l'objet réel, mais un objet **Proxy** qui hérite de l'entité.
*   Exemple : `$article->getCategory()`. Doctrine retourne un Proxy de `Category` avec juste l'ID.
*   La requête SQL réelle pour charger la catégorie n'est faite que si vous appelez `$category->getName()`.

### 6. Metadata
La configuration (Attributs, XML) est parsée en objets `ClassMetadata`. Doctrine l'utilise pour savoir comment mapper `User::$name` vers la colonne `username`.

## Le Cycle de Vie (Identity Map)

1.  **Persist** : `$em->persist($user)`. L'objet entre dans l'état **Managed**. Doctrine sait qu'il doit le surveiller.
2.  **Modification** : `$user->setName('Toto')`. PHP modifie l'objet en mémoire. Doctrine ne fait rien pour l'instant.
3.  **Flush** : `$em->flush()`.
    *   La UnitOfWork compare l'état des objets Managed avec leur état original.
    *   Elle détecte que `name` a changé.
    *   Elle démarre une transaction SQL.
    *   Elle exécute `UPDATE user SET name = 'Toto' ...`.
    *   Elle commit la transaction.

## 🧠 Concepts Clés
1.  **First Level Cache** : L'EntityManager garde en mémoire tous les objets chargés par leur ID. Si vous demandez `$em->find(User::class, 1)` deux fois, la deuxième fois ne fait pas de requête SQL.
2.  **Owning vs Inverse Side** : Dans une relation bidirectionnelle, un seul côté ("Owning", généralement celui qui a la clé étrangère) est responsable de la persistance. Modifier le côté inverse sans mettre à jour le côté propriétaire ne sauvegarde rien en BDD.

## ⚠️ Points de vigilance (Certification)
*   **Performance** : `flush()` est une opération coûteuse. Appelez-le une seule fois à la fin de votre traitement, pas dans une boucle.
*   **DQL vs SQL** : DQL travaille sur les objets et leurs classes (`SELECT u FROM App\Entity\User u`). SQL travaille sur les tables.

## Ressources
*   [Doctrine Architecture](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/architecture.html)
*   [UnitOfWork](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/unitofwork.html)
