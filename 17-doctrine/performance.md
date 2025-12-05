# Performance & Optimisation

L'ORM est un outil puissant, mais il peut tuer les performances s'il est mal utilisé.

## Le Problème N+1
Le tueur de performance #1.
Vous chargez une liste de 20 produits, puis dans une boucle Twig vous affichez le nom de la catégorie (`product.category.name`).
Doctrine va faire :
1.  1 requête pour les produits (`SELECT * FROM product ...`).
2.  **20 requêtes** supplémentaires (une par produit) pour aller chercher la catégorie (`SELECT * FROM category WHERE id = ?`).

**Solution : Eager Loading (Joins)**
Utiliser `leftJoin` et `addSelect` dans le QueryBuilder.

```php
$qb->select('p', 'c') // On sélectionne P et C
   ->from('App\Entity\Product', 'p')
   ->leftJoin('p.category', 'c'); // On fait la jointure
// Résultat : 1 seule requête SQL qui ramène tout.
```

## Lazy vs Eager Loading
*   **Lazy** (Défaut) : Ne charge rien tant qu'on n'y touche pas. Risque de N+1.
*   **Eager** (Configurable dans le mapping `fetch: 'EAGER'`) : Charge toujours la relation. À éviter globalement, car cela charge trop de données inutiles la plupart du temps. Préférez le Eager Loading **dynamique** via QueryBuilder (ci-dessus).

## Proxies
Pour faire du Lazy Loading, Doctrine crée des "Objets Proxy". Ce sont des sous-classes générées qui héritent de votre Entité.
Quand vous faites `$product->getCategory()`, vous recevez un Proxy de Category (avec juste l'ID rempli). Au moment où vous appelez `$proxy->getName()`, Doctrine déclenche la requête SQL manquante (Lazy Load).

## Partial Objects
Doctrine permet de charger des objets partiels (`SELECT p.id, p.name FROM Product p`).
**Danger** : Les champs non sélectionnés sont `null`. Si vous resauvegardez cet objet, Doctrine risque d'écraser les valeurs manquantes par `null` en base.
**Alternative** : Utilisez des DTOs (`SELECT NEW App\Dto\ProductSummary(p.id, p.name) ...`) pour les lectures optimisées (Read Model).

## Requêtes en lecture seule
Pour les pages de consultation massives, désactivez le tracking de l'EntityManager pour gagner du CPU/RAM.

```php
$query->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);
// Ou utiliser l'hydratation tableau
$query->getArrayResult();
```

## Ressources
*   [Doctrine Docs - Performance](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/batch-processing.html)
