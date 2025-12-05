# Unit of Work & Cycle de vie

## L'EntityManager
C'est le chef d'orchestre. Il gère la persistance et la récupération des objets.
Il utilise le pattern **Unit of Work** : il garde en mémoire la liste de tous les objets chargés ou modifiés, et calcule le minimum de requêtes SQL à exécuter lors du `flush()`.

## Les 4 états d'une entité

1.  **New** : Un objet PHP vient d'être instancié (`new Product()`). Il n'a pas d'ID et Doctrine ne le connait pas.
2.  **Managed** : L'objet est "suivi" par l'EntityManager. Tout changement sur ses propriétés sera détecté lors du flush.
    *   Soit parce qu'il vient d'être récupéré (`find()`).
    *   Soit parce qu'il a été persisté (`persist()`).
3.  **Detached** : L'objet existe en base (il a un ID), mais l'EntityManager ne le suit plus (suite à un `detach()` ou `clear()`). Les modifications ne seront pas sauvées.
4.  **Removed** : L'objet est marqué pour suppression (`remove()`). Il sera effacé de la base au prochain flush.

## Persist vs Flush

*   **`persist($entity)`** : Dit à Doctrine "Commence à gérer cet objet". Pour un nouvel objet, cela prépare l'INSERT. Pour un objet déjà géré (récupéré via find), c'est inutile (mais pas grave).
*   **`flush()`** : C'est le commit. Doctrine regarde tous les objets Managed, calcule les différences (Dirty Checking), et exécute toutes les requêtes SQL (INSERT, UPDATE, DELETE) en une seule transaction.

**Bonne pratique** : Appelez `flush()` le moins souvent possible (idéalement une seule fois à la fin de la requête) pour optimiser la transaction.

## Clear
`$em->clear()` détache **toutes** les entités.
C'est indispensable pour les traitements par lots (Batch Processing) pour libérer la mémoire PHP.

```php
foreach ($largeIterable as $i => $entity) {
    // Traitement...
    if ($i % 100 === 0) {
        $em->flush();
        $em->clear(); // Évite l'explosion mémoire (Memory Leak)
    }
}
```

## 🧠 Concepts Clés
1.  **Dirty Checking** : Doctrine compare l'état actuel de l'objet avec son état original (stocké en interne). S'il n'y a aucune différence, aucun UPDATE n'est fait.
2.  **Identity Map** : Si vous demandez 2 fois le même ID (`find(1)`), Doctrine ne refait pas de requête SQL, il vous renvoie la même instance d'objet PHP qui est en mémoire.

## Ressources
*   [Doctrine Docs - Working with Objects](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/working-with-objects.html)
