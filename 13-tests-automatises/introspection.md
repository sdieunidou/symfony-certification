# Introspection Request et Response

## Concept clé
Après avoir fait une requête avec le client, on veut inspecter ce qui s'est passé.

## Application dans Symfony 7.0

```php
$client->request('GET', '/');

// La requête envoyée (Symfony\Component\HttpFoundation\Request)
$request = $client->getRequest();
echo $request->getUri();

// La réponse reçue (Symfony\Component\HttpFoundation\Response)
$response = $client->getResponse();
echo $response->getStatusCode();
echo $response->headers->get('Content-Type');
```

### Assertions pratiques
Plutôt que d'inspecter manuellement :
```php
$this->assertResponseIsSuccessful(); // 2xx
$this->assertResponseStatusCodeSame(404);
$this->assertResponseRedirects('/login');
$this->assertResponseHeaderSame('Content-Type', 'application/json');
$this->assertResponseHasCookie('PHPSESSID');
```

## Points de vigilance (Certification)
*   **Type** : `$client->getResponse()` retourne la réponse *brute* (avant qu'elle soit envoyée au navigateur). C'est utile pour tester le contenu binaire ou JSON sans passer par le Crawler.

## Ressources
*   [Symfony Docs - Test Assertions](https://symfony.com/doc/current/testing.html#troubleshooting-application-errors)

