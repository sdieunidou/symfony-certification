# Composant Symfony HttpClient

## Concept clé
Un composant moderne pour effectuer des requêtes HTTP synchrones ou asynchrones vers des services tiers (consommer des APIs). C'est l'équivalent serveur de `fetch` ou `Guzzle`.

## Application dans Symfony 7.0
C'est le client HTTP par défaut recommandé (remplace Guzzle dans la plupart des cas Symfony).
Il est conçu autour des contrats `Symfony\Contracts\HttpClient\HttpClientInterface`.

## Exemple de code

```php
<?php

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubClient
{
    public function __construct(
        private HttpClientInterface $client
    ) {}

    public function getRepoInfo(string $owner, string $repo): array
    {
        $response = $this->client->request(
            'GET',
            "https://api.github.com/repos/$owner/$repo",
            [
                'headers' => [
                    'Accept' => 'application/vnd.github.v3+json',
                ],
                // 'query' => [...],
                // 'json' => ['key' => 'value'] // Auto JSON encode pour POST
            ]
        );

        // Non-bloquant jusqu'ici ! La requête part réellement quand on lit les données.

        $statusCode = $response->getStatusCode(); // 200
        
        // Conversion automatique JSON -> Array
        $content = $response->toArray(); 

        return $content;
    }
}
```

## Points de vigilance (Certification)
*   **Asynchrone par défaut** : L'appel à `request()` est non-bloquant. La connexion réseau ne s'ouvre (ou ne s'attend) que lorsque vous appelez `getStatusCode()`, `getContent()`, etc.
*   **Streaming** : Permet de traiter les réponses flux par flux (`$client->stream($responses)`).
*   **Scoped Client** : On peut configurer plusieurs clients nommés dans `framework.yaml` (ex: `github.client`, `stripe.client`) avec des URL de base et des headers différents, et les injecter via le *Named Autowiring* (`HttpClientInterface $githubClient`).
*   **Contrats** : Le composant implémente `PSR-18` (HTTP Client) via un adaptateur si besoin, mais son interface native est plus riche (asynchrone).

## Ressources
*   [Symfony Docs - HTTP Client](https://symfony.com/doc/current/http_client.html)

