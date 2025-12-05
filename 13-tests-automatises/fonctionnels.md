# Tests Fonctionnels (Application Tests)

## Concept clé
Vérifier que l'application fonctionne de bout en bout (HTTP Request -> HTTP Response).
On simule un navigateur qui navigue sur le site.

## Application dans Symfony 7.0
On utilise `WebTestCase` (qui étend `KernelTestCase`).

```php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Blog Index');
    }
}
```

## Points de vigilance (Certification)
*   **Assertions** : Symfony ajoute de nombreuses assertions pratiques : `assertResponseStatusCodeSame(200)`, `assertResponseRedirects()`, `assertSelectorExists()`.
*   **Base de données** : Le test fonctionnel utilise la vraie base de données (souvent une DB de test dédiée). Il faut penser à la remettre à zéro entre chaque test (`DAMADoctrineTestBundle` ou transactions rollback).

## Ressources
*   [Symfony Docs - Functional Tests](https://symfony.com/doc/current/testing.html#functional-tests)

