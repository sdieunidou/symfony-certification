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

## Assertions Spécifiques (Liste Complète)
Symfony fournit un vaste jeu d'assertions via `BrowserKitAssertionsTrait` et d'autres traits.

### 1. Assertions de Réponse (Response)
*   `assertResponseIsSuccessful()`: HTTP 2xx.
*   `assertResponseStatusCodeSame(int $code)`: Code précis (ex: 404).
*   `assertResponseRedirects(?string $url, ?int $code)`: Vérifie la redirection.
*   `assertResponseHasHeader($name)` / `assertResponseNotHasHeader($name)`
*   `assertResponseHeaderSame($name, $value)`
*   `assertResponseHasCookie($name)` / `assertResponseNotHasCookie($name)`
*   `assertResponseCookieValueSame($name, $value)`
*   `assertResponseFormatSame($format)`: Vérifie le format retourné par `getFormat()` (ex: 'json').
*   `assertResponseIsUnprocessable()`: HTTP 422.

### 2. Assertions de Requête (Request)
*   `assertRequestAttributeValueSame($name, $value)`
*   `assertRouteSame($expectedRoute, array $params)`: Vérifie que la requête matche une route donnée.

### 3. Assertions de Navigateur (Browser)
Vérifie l'état du client (cookies, historique).
*   `assertBrowserHasCookie($name)`
*   `assertBrowserCookieValueSame($name, $value)`
*   `assertBrowserHistoryIsOnFirstPage()` (Nouveau 7.4)
*   `assertBrowserHistoryIsOnLastPage()` (Nouveau 7.4)

### 4. Assertions Crawler (DOM)
*   `assertSelectorExists($selector)` / `assertSelectorNotExists($selector)`
*   `assertSelectorCount(int $count, $selector)`
*   `assertSelectorTextContains($selector, $text)` / `assertSelectorTextNotContains`
*   `assertAnySelectorTextContains($selector, $text)`: Si au moins un élément matche.
*   `assertSelectorTextSame($selector, $text)`: Correspondance exacte.
*   `assertPageTitleSame($title)` / `assertPageTitleContains($title)`
*   `assertInputValueSame($fieldName, $value)`: Valeur d'un input de formulaire.
*   `assertCheckboxChecked($fieldName)` / `assertCheckboxNotChecked`
*   `assertFormValue($formSelector, $fieldName, $value)`

### 5. Assertions Mailer
Plus besoin de fouiller dans le profiler manuellement !
*   `assertEmailCount(int $count)`
*   `assertQueuedEmailCount(int $count)`
*   `assertEmailIsQueued($event)`
*   `assertEmailAttachmentCount($email, $count)`
*   `assertEmailTextBodyContains($email, $text)` / `assertEmailHtmlBodyContains`
*   `assertEmailHasHeader($email, $name)`
*   `assertEmailAddressContains($email, $header, $address)` (ex: vérifier le 'To').
*   `assertEmailSubjectContains($email, $text)`

### 6. Assertions Notifier & HttpClient
*   `assertNotificationCount($count)`
*   `assertHttpClientRequest($url)`: Vérifie qu'une requête HTTP sortante a été faite (si HttpClient est mocké/profilé).

## 🧠 Concepts Clés
1.  **Environnement** : Les tests tournent dans l'environnement `test` (`APP_ENV=test`). Le cache est séparé du dev.
2.  **Base de Données** : Les tests fonctionnels écrivent en base. Utilisez une base de test dédiée. Pour la nettoyer entre chaque test, utilisez `DAMADoctrineTestBundle` (qui wrappe chaque test dans une transaction rollbakée).

## ⚠️ Points de vigilance (Certification)
*   **Boot** : `createClient()` boote le Kernel. Si vous avez besoin d'accéder au conteneur *avant* de faire une requête (ex: créer un user en base), faites `$container = static::getContainer();` (qui boote le kernel si nécessaire).

## Ressources
*   [Symfony Docs - Functional Tests](https://symfony.com/doc/current/testing.html#functional-tests)
