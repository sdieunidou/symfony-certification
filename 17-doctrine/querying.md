# Querying (DQL & QueryBuilder)

## Les méthodes de récupération
Pour lire des données, Doctrine offre 3 niveaux d'abstraction :

1.  **Méthodes Magiques** (`findBy`, `findOneBy`) : Simple, pour des requêtes basiques.
2.  **QueryBuilder** : L'outil orienté objet pour construire des requêtes complexes dynamiquement.
3.  **DQL** (Doctrine Query Language) : Un langage proche du SQL mais orienté Objet (on sélectionne des classes et des propriétés, pas des tables et des colonnes).
4.  **SQL Natif** : Pour les cas extrêmes (Performance, fonctionnalités SGBD spécifiques).

## Le Repository Pattern
Dans Symfony, on centralise les requêtes dans des classes `Repository`.

```php
// src/Repository/ProductRepository.php
class ProductRepository extends ServiceEntityRepository
{
    public function findExpensiveProducts(float $price): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.price > :price')
            ->setParameter('price', $price)
            ->orderBy('p.price', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
```

## DQL vs SQL
DQL comprend votre modèle objet.
*   SQL : `SELECT * FROM product p JOIN category c ON p.category_id = c.id`
*   DQL : `SELECT p, c FROM App\Entity\Product p JOIN p.category c` (Doctrine gère les clés étrangères tout seul).

## Fonctions DQL Personnalisées
Par défaut, DQL supporte les fonctions standards (UPPER, LOWER, CONCAT...).
Si vous avez besoin de fonctions spécifiques à votre SGBD (ex: `MD5()`, `RAND()`, `LEVENSHTEIN()`), vous devez les enregistrer.

1. Créer la classe de définition DQL (héritant de `FunctionNode`).
2. Enregistrer dans `doctrine.yaml` :

```yaml
doctrine:
    orm:
        dql:
            string_functions:
                MD5: App\Doctrine\DQL\Md5Function
            numeric_functions:
                RAND: App\Doctrine\DQL\RandFunction
```

Vous pouvez aussi utiliser le pack `beberlei/doctrineextensions` qui contient des centaines de fonctions pré-codées pour MySQL, PostgreSQL, etc.

## Hydratation
C'est le format de retour des données.

*   `getResult()` (ou `HYDRATE_OBJECT`) : Retourne un tableau d'objets Entités. C'est le plus flexible mais le plus coûteux (mémoire/CPU).
*   `getArrayResult()` (ou `HYDRATE_ARRAY`) : Retourne un tableau associatif (array de arrays). Beaucoup plus rapide pour de l'affichage simple (read-only).
*   `getSingleScalarResult()` : Pour retourner un compte (`COUNT`) ou une somme.
*   `toIterable()` : Retourne un itérateur pour parcourir de gros volumes sans saturer la mémoire (Remplace `iterate()` déprécié).

## Paramètres
**TOUJOURS** utiliser `setParameter()` pour éviter les injections SQL. Ne jamais concaténer des variables dans la chaîne DQL.

```php
// BAD
$qb->where("p.name = '$name'");

// GOOD
$qb->where('p.name = :name')->setParameter('name', $name);
```

## Alias
Dans le QueryBuilder, l'alias racine (ex: 'p') est obligatoire.

## Ressources
*   [Doctrine Docs - DQL](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/dql-doctrine-query-language.html)
*   [Doctrine Docs - QueryBuilder](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/query-builder.html)
