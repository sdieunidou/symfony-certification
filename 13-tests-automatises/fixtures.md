# Fixtures & Base de Données

## Configuration de la Base de Test
Les tests doivent utiliser une base de données séparée pour ne pas écraser les données de développement.

1.  Créer un fichier `.env.test.local` :
    ```env
    DATABASE_URL="mysql://user:pass@127.0.0.1:3306/db_name_test"
    ```
2.  Créer la base et le schéma :
    ```bash
    php bin/console --env=test doctrine:database:create
    php bin/console --env=test doctrine:schema:create
    ```

*Astuce : Convention de nommage `nom_projet_test`.*

## Chargement des Fixtures
Utilisation de `DoctrineFixturesBundle` pour créer des données initiales.

### Création
```php
// src/DataFixtures/ProductFixture.php
class ProductFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
            $product = new Product();
        $product->setName('Widget');
            $manager->persist($product);
        $manager->flush();
    }
}
```

### Chargement Manuel
Pour charger les fixtures dans la base de test :
```bash
php bin/console --env=test doctrine:fixtures:load
```

## Isolation des Tests (`DAMADoctrineTestBundle`)
Pour éviter de recharger les fixtures à chaque test (lent) ou d'avoir des tests interdépendants (flaky), utilisez ce bundle.
Il enveloppe chaque test dans une **transaction** base de données et fait un **rollback** à la fin.

### Installation
```bash
composer require --dev dama/doctrine-test-bundle
```

### Configuration (PHPUnit)
Activer l'extension dans `phpunit.dist.xml` :
```xml
<extensions>
    <bootstrap class="DAMA\DoctrineTestBundle\PHPUnit\PHPUnitExtension"/>
</extensions>
```

Ainsi, les modifications faites par un test (création, suppression) sont annulées automatiquement.

## 🧠 Concepts Clés
1.  **Tests Fonctionnels** : Ils écrivent réellement en base. Sans isolation, le test B échouera car le test A a modifié les données.
2.  **SQLite** : Pour des tests simples, on peut utiliser SQLite en mémoire (`DATABASE_URL="sqlite:///:memory:"`), ce qui est très rapide mais peut différer de la prod (MySQL/PG).

## ⚠️ Points de vigilance (Certification)
*   **Make Fixtures** : `php bin/console make:fixtures` génère la classe.
*   **Ordre** : Si les fixtures dépendent les unes des autres, implémentez `DependentFixtureInterface`.
