# Composant Symfony HttpClient

## Concept clé
Le composant `HttpClient` est un client HTTP bas niveau, puissant et moderne, conçu pour consommer des APIs tierces.
Il supporte nativement :
1.  **L'Asynchrone** : Les requêtes ne bloquent pas l'exécution PHP tant que la réponse n'est pas lue.
2.  **HTTP/2** et **HTTP/3** (via Curl).
3.  **Le Streaming** : Traitement des réponses flux par flux (Server-Sent Events, Téléchargements).
4.  **L'Autowiring Scopé** : Configuration spécifique par API (GitHub, Stripe, etc.).

## Application dans Symfony 7.0
Il remplace Guzzle comme standard de facto dans l'écosystème Symfony. Il implémente `Symfony\Contracts\HttpClient\HttpClientInterface` et peut agir comme un adaptateur `PSR-18`.

## Exemple de code Complet

```php
<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Response;

class GithubClient
{
    public function __construct(
        // Injection du client global ou d'un client scopé (via named autowiring)
        private HttpClientInterface $client
    ) {}

    public function getRepos(string $username): array
    {
        // 1. Préparation de la requête (NON BLOQUANT)
        // La connexion réseau ne s'ouvre pas encore forcément.
        $response = $this->client->request('GET', "https://api.github.com/users/$username/repos", [
            'headers' => ['Accept' => 'application/vnd.github+json'],
            'timeout' => 5.0,
            // 'json' => ['foo' => 'bar'], // Pour POST/PUT
            // 'query' => ['sort' => 'updated'],
        ]);

        // 2. Logique métier pendant que la requête part...
        // ...

        // 3. Accès aux données (BLOQUANT)
        // C'est ici que le script attend la réponse si elle n'est pas encore arrivée.
        
        if ($response->getStatusCode() !== 200) {
            throw new \Exception("Erreur API: " . $response->getStatusCode());
        }

        // Conversion JSON -> Array automatique
        return $response->toArray(); 
    }
}
```

## Requêtes Concurrentes (Parallélisme)
C'est la "killer feature". Vous pouvez lancer 10 requêtes en parallèle et attendre qu'elles finissent toutes, en un temps égal à la requête la plus lente (au lieu de la somme des temps).

```php
$responses = [];
$urls = ['https://api.a.com', 'https://api.b.com', 'https://api.c.com'];

// Lance les 3 requêtes
foreach ($urls as $url) {
    $responses[] = $client->request('GET', $url);
}

// Attend et traite au fil de l'eau
// stream() permet de traiter les réponses dès qu'elles arrivent, dans le désordre
foreach ($client->stream($responses) as $response => $chunk) {
    if ($chunk->isLast()) {
        // Réponse complète reçue
        echo "Fini : " . $response->getInfo('url');
    }
}
```

## Configuration "Scoped Client" (`framework.yaml`)
Ne jamais coder les URLs en dur. Utilisez les clients scopés.

```yaml
framework:
    http_client:
        scoped_clients:
            github.client:
                base_uri: 'https://api.github.com'
                headers:
                    Accept: 'application/vnd.github+json'
                auth_basic: '%env(GITHUB_TOKEN)%'
```

Injection ciblée (Named Autowiring) :
```php
public function __construct(HttpClientInterface $githubClient) { ... }
```

## Tests et Mocking
Symfony fournit un `MockHttpClient` et `MockResponse` pour les tests unitaires, évitant les appels réseaux réels.

```php
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

public function testGetRepos()
{
    $mockResponse = new MockResponse(json_encode(['repo1']), ['http_code' => 200]);
    $mockClient = new MockHttpClient($mockResponse);
    
    $service = new GithubClient($mockClient);
    // ... assertions
}
```

## 🧠 Concepts Clés
1.  **Lazy Loading** : La méthode `request()` retourne immédiatement un objet `ResponseInterface`. Le réseau est sollicité paresseusement.
2.  **PSR-18 vs Natif** : L'interface PSR-18 (`ClientInterface`) force le comportement synchrone (retourne une `Response` peuplée). L'interface Symfony (`HttpClientInterface`) est asynchrone par nature.
3.  **Retry Mechanism** : Le composant inclut un mécanisme de `RetryableHttpClient` (décorateur) pour relancer automatiquement les requêtes en cas d'erreur réseau ou code 5xx (configurable via `framework.yaml`).

## ⚠️ Points de vigilance (Certification)
*   **Exceptions** :
    *   `TransportExceptionInterface` : Erreur réseau (DNS, Timeout, Connexion refusée).
    *   `ClientExceptionInterface` : Codes 4xx (si vous appelez `$response->getContent()` ou `check=true`).
    *   `ServerExceptionInterface` : Codes 5xx.
    *   **Important** : Par défaut, `request()` ne lance PAS d'exception pour les 4xx/5xx. Les exceptions sont lancées uniquement quand vous lisez le contenu (`getContent`, `toArray`), sauf si vous passez `false` au paramètre `$throw` dans `getContent()`.
*   **Hosting** : Pour que l'asynchrone fonctionne bien, l'extension `curl` est fortement recommandée. Sans elle, le client fonctionne mais de manière synchrone (fallback PHP streams).

## Ressources
*   [Symfony Docs - HTTP Client](https://symfony.com/doc/current/http_client.html)
*   [Symfony Casts - HttpClient](https://symfonycasts.com/screencast/symfony-http-client)
