# Data Fixtures

## Concept
Les Fixtures servent à charger un jeu de "fausses" données (dummy data) dans la base de données.
C'est indispensable pour :
1.  **Le Développement** : Avoir un site rempli dès l'installation du projet pour travailler sur le frontend.
2.  **Les Tests** : Avoir un état initial connu et reproductible avant chaque test.

## Installation
Le bundle n'est pas installé par défaut.
```bash
composer require --dev orm-fixtures
```

## Écrire une Fixture
Une classe de Fixture doit étendre `Fixture` et implémenter la méthode `load(ObjectManager $manager)`.

```php
namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // On crée 20 produits
        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('Product ' . $i);
            $product->setPrice(mt_rand(10, 100));
            
            $manager->persist($product);
        }

        // Un seul flush à la fin pour la performance
        $manager->flush();
    }
}
```

## Chargement des Fixtures
Attention : Cette commande **vide la base de données** (TRUNCATE/DELETE) avant de charger les nouvelles données.

```bash
# Charge toutes les fixtures
php bin/console doctrine:fixtures:load

# Sans confirmation (utile en CI/CD)
php bin/console doctrine:fixtures:load --no-interaction

# Append (Ajoute sans vider la base)
php bin/console doctrine:fixtures:load --append
```

## Gestion des Dépendances (DependentFixtureInterface)
Si vous avez des relations (ex: Créer des Users, puis créer des Posts liés à ces Users), l'ordre de chargement est crucial.
Doctrine charge les classes par ordre alphabétique par défaut, ce qui est risqué.

Utilisez `DependentFixtureInterface` pour définir l'ordre.

```php
class PostFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        // On peut récupérer une référence créée dans UserFixtures
        $user = $this->getReference(UserFixtures::ADMIN_USER_REFERENCE);
        
        $post = new Post();
        $post->setAuthor($user); // $user est un objet Proxy
        $manager->persist($post);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class, // UserFixtures sera chargée AVANT PostFixtures
        ];
    }
}
```

### Partager des objets (References)
Dans la fixture parente (`UserFixtures`), vous devez sauvegarder la référence :

```php
// UserFixtures.php
public const ADMIN_USER_REFERENCE = 'admin-user';

public function load(ObjectManager $manager): void
{
    $user = new User();
    // ...
    $manager->persist($user);
    
    // Sauvegarde la référence pour les autres fixtures
    $this->addReference(self::ADMIN_USER_REFERENCE, $user);
    
    $manager->flush();
}
```

## Groupes
Pour ne charger qu'une partie des fixtures (ex: seulement les Users, pas les 10.000 Produits).

```php
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class UserFixtures extends Fixture implements FixtureGroupInterface
{
    public static function getGroups(): array
    {
        return ['users', 'minimal'];
    }
}
```

Commande :
```bash
php bin/console doctrine:fixtures:load --group=users
```

## Ressources
*   [Symfony Docs - Fixtures](https://symfony.com/bundles/DoctrineFixturesBundle/current/index.html)
