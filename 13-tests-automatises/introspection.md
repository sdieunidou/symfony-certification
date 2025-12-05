# Introspection Request et Response

## Concept clé
Les assertions de haut niveau (`assertResponseIsSuccessful`) ne suffisent pas toujours. Parfois, il faut inspecter les objets bruts `Request` et `Response` pour vérifier des headers, des cookies, ou du JSON complexe.

## Accès via le Client

```php
$client->request('GET', '/api/me');

/** @var \Symfony\Component\HttpFoundation\Request $request */
$request = $client->getRequest();

/** @var \Symfony\Component\HttpFoundation\Response $response */
$response = $client->getResponse();
```

## Inspection

### Response
```php
// Code statut
$this->assertEquals(200, $response->getStatusCode());

// Headers
$this->assertTrue($response->headers->contains('Content-Type', 'application/json'));

// Contenu (Body)
$content = $response->getContent();
$json = json_decode($content, true);
$this->assertEquals('fabien', $json['username']);
```

### Request
Utile pour vérifier ce que le client a réellement envoyé (ex: après une redirection ou un submitForm).
```php
$this->assertEquals('POST', $request->getMethod());
```

## 🧠 Concepts Clés
1.  **État final** : `getResponse()` retourne la réponse de la **dernière** requête. Si `followRedirects` est true (défaut), c'est la réponse de la page finale après redirection. Pour inspecter la redirection elle-même (302), il faut désactiver `followRedirects`.
2.  **Raw** : C'est la réponse brute, non parsée par le Crawler.

## ⚠️ Points de vigilance (Certification)
*   **Interne** : `$client->getRequest()` retourne la requête interne de Symfony, pas celle d'Apache.

## Ressources
*   [Symfony Docs - Testing](https://symfony.com/doc/current/testing.html)
