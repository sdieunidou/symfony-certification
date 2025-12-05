# Tests & Doctrine

## Le Problème des Tests avec BDD
Les tests fonctionnels qui interagissent avec la base de données posent deux problèmes :
1.  **Lenteur** : Créer la base, les tables, charger les fixtures prend du temps.
2.  **Isolation** : Le test A modifie la base. Le test B échoue car la base n'est plus dans l'état attendu (Side effects).

## Solution : DAMA Doctrine Test Bundle
C'est le standard absolu dans l'écosystème Symfony.
Il utilise des **transactions imbriquées** (Nested Transactions) :
1.  Démarre une transaction globale au début du test (`BEGIN`).
2.  Laisse le test s'exécuter (INSERT, UPDATE...).
3.  Fait un `ROLLBACK` à la fin du test.

Résultat : La base est **instantanément** remise à neuf, sans aucun coût de nettoyage.

### Installation
```bash
composer require --dev dama/doctrine-test-bundle
```

### Configuration (PHPUnit)
Activer l'extension dans `phpunit.xml.dist` :

```xml
<extensions>
    <bootstrap class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
</extensions>
```

## Tester un Repository
Les Repositories contiennent souvent de la logique complexe (DQL, QueryBuilder) qu'il faut tester.
On utilise `KernelTestCase` pour accéder au conteneur et à la vraie base de test.

```php
class ProductRepositoryTest extends KernelTestCase
{
    public function testSearchActive(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $repo = $container->get(ProductRepository::class);

        // Assurez-vous d'avoir des fixtures chargées
        $products = $repo->findActiveProducts();

        $this->assertCount(5, $products);
        $this->assertEquals('Widget', $products[0]->getName());
    }
}
```

## Base de Données de Test
Symfony configure automatiquement une base distincte pour l'environnement `test` si vous suffixez votre `DATABASE_URL` dans `.env.test`.
Exemple : `DATABASE_URL="postgresql://db:db@127.0.0.1:5432/app_test?serverVersion=15"`

## Ressources
*   [Symfony Docs - Testing Database](https://symfony.com/doc/current/testing/database.html)
*   [DAMA Doctrine Test Bundle](https://github.com/dmaicher/doctrine-test-bundle)
