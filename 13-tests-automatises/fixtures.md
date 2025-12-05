# Fixtures & Données de Test

## Concept Clé
Pour tester efficacement, il faut des données prédictibles en base de données.
Symfony utilise le **DoctrineFixturesBundle** pour charger des jeux de données (Fixtures).

## Création de Fixtures

```php
// src/DataFixtures/AppFixtures.php
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Créer 10 produits
        for ($i = 0; $i < 10; $i++) {
            $product = new Product();
            $product->setName('Produit ' . $i);
            $product->setPrice(mt_rand(10, 100));
            $manager->persist($product);
        }

        $manager->flush();
    }
}
```

## Utilisation dans les Tests

### Problème
Si vous chargez les fixtures avant chaque test, c'est très lent (`bin/console doctrine:fixtures:load`).
Si vous ne les chargez pas, les tests sont dépendants de l'état précédent (Flaky tests).

### Solution : `DAMADoctrineTestBundle`
C'est le standard pour les tests Symfony.
Il utilise des **transactions imbriquées** (Database Transactions) pour isoler chaque test.

1.  **Début du test** : `BEGIN TRANSACTION`
2.  **Exécution** : Le test écrit en base.
3.  **Fin du test** : `ROLLBACK`

La base revient instantanément à l'état initial sans avoir besoin de recharger les fixtures.

### Installation & Config

```bash
composer require --dev dama/doctrine-test-bundle
```

```xml
<!-- phpunit.xml.dist -->
<extensions>
    <extension class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension" />
</extensions>
```

### Stratégie de Chargement
Souvent, on charge les fixtures **une seule fois** au début de la suite de tests (ou via un bootstrap), et DAMA s'occupe de resetter les modifs.

## LiperTestFixtures (Alternative)
Pour des besoins plus complexes, la librairie `liip/test-fixtures-bundle` permet de charger des fixtures spécifiques *dans* le test.

```php
$this->loadFixtures([
    UserFixtures::class,
    ProductFixtures::class
]);
```

## Factories (ZenstruckFoundry)
Une approche moderne (type Laravel Factory) qui remplace souvent les fixtures classiques.

```php
// Dans le test
ProductFactory::createMany(5); // Crée 5 produits
UserFactory::createOne(['email' => 'admin@test.com']);
```

## ⚠️ Points de vigilance (Certification)
*   **Environnement** : Les fixtures ne doivent être chargées que sur la base de test (`APP_ENV=test`). Attention à ne pas écraser la prod !
*   **Dépendances** : Utilisez `DependentFixtureInterface` pour gérer l'ordre de chargement (ex: Users avant Commandes).

## Ressources
*   [Doctrine Fixtures Documentation](https://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html)
*   [DAMA Doctrine Test Bundle](https://github.com/dmaicher/doctrine-test-bundle)
