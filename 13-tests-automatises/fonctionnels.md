# Tests Fonctionnels (WebTestCase)

## Concept clé
Les tests fonctionnels (ou "Application Tests") vérifient le comportement de l'application du point de vue de l'utilisateur (Requête HTTP -> Réponse HTTP).
Ils n'ont pas besoin de mocker les services internes (sauf appels API externes).

## Structure d'un Test

```php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BlogControllerTest extends WebTestCase
{
    public function testBlogList(): void
    {
        // 1. Créer le client
        $client = static::createClient();

        // 2. Faire une requête
        $crawler = $client->request('GET', '/blog');

        // 3. Asserter la réponse technique (200 OK)
        $this->assertResponseIsSuccessful();

        // 4. Asserter le contenu (Business value)
        $this->assertSelectorTextContains('h1', 'Derniers articles');
        $this->assertCount(10, $crawler->filter('article.post'));
    }
}
```

## Assertions Spécifiques (`BrowserKitAssertionsTrait`)
Symfony fournit des assertions dédiées au Web :
*   `assertResponseIsSuccessful()`
*   `assertResponseStatusCodeSame(404)`
*   `assertResponseRedirects('/login')`
*   `assertSelectorExists('.alert-success')`
*   `assertSelectorNotExists('.error')`
*   `assertPageTitleSame('Accueil')`
*   `assertCheckboxChecked('remember_me')`

## 🧠 Concepts Clés
1.  **Environnement** : Les tests tournent dans l'environnement `test` (`APP_ENV=test`). Le cache est séparé du dev.
2.  **Base de Données** : Les tests fonctionnels écrivent en base. Utilisez une base de test dédiée. Pour la nettoyer entre chaque test, utilisez `DAMADoctrineTestBundle` (qui wrappe chaque test dans une transaction rollbakée).

## ⚠️ Points de vigilance (Certification)
*   **Boot** : `createClient()` boote le Kernel. Si vous avez besoin d'accéder au conteneur *avant* de faire une requête (ex: créer un user en base), faites `$container = static::getContainer();` (qui boote le kernel si nécessaire).

## Ressources
*   [Symfony Docs - Functional Tests](https://symfony.com/doc/current/testing.html#functional-tests)
