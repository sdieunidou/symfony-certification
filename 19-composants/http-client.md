# Composant Symfony HttpClient

## Concept clé
Le composant `HttpClient` est un client HTTP bas niveau, puissant et moderne, conçu pour consommer des APIs tierces.
Il supporte nativement :
1.  **L'Asynchrone** : Les requêtes ne bloquent pas l'exécution PHP tant que la réponse n'est pas lue.
2.  **HTTP/2** et **HTTP/3** (via Curl).
3.  **Le Streaming** : Traitement des réponses flux par flux (Server-Sent Events, Téléchargements).
4.  **L'Autowiring Scopé** : Configuration spécifique par API (GitHub, Stripe, etc.).

## Installation
```bash
composer require symfony/http-client
```

## Basic Usage
L'interface principale est `Symfony\Contracts\HttpClient\HttpClientInterface`.

```php
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GithubService
{
    public function __construct(
        private HttpClientInterface $client,
    ) {}

    public function getRepos(): array
    {
        $response = $this->client->request(
            'GET',
            'https://api.github.com/repos/symfony/symfony-docs'
        );

        $statusCode = $response->getStatusCode(); // 200
        $contentType = $response->getHeaders()['content-type'][0]; // 'application/json'
        $content = $response->getContent(); // string body
        $content = $response->toArray(); // array (si JSON)

        return $content;
    }
}
```

### Utilisation Standalone
```php
use Symfony\Component\HttpClient\HttpClient;

$client = HttpClient::create();
$response = $client->request('GET', 'https://...');
```

## Options de Requête (`request`)
Le 3ème argument de `request()` accepte de nombreuses options :

*   `headers`: Tableau d'en-têtes HTTP.
*   `body`: Corps de la requête (string, array pour form-data, resource).
*   `json`: Encodage automatique en JSON et ajout du header `Content-Type: application/json`.
*   `query`: Paramètres de l'URL (Query String).
*   `auth_basic`: Authentification Basic (`['username', 'password']` ou `'token'`).
*   `auth_bearer`: Token Bearer.
*   `timeout`: Timeout en secondes (float).
*   `max_redirects`: Nombre max de redirections suivies.
*   `verify_peer`: Validation SSL (true/false).
*   `proxy`: URL du proxy.
*   `user_data`: Données arbitraires attachées à la réponse.

## Configuration (`framework.yaml`)

### Options par défaut (Globales)
```yaml
framework:
    http_client:
        default_options:
            max_redirects: 7
            headers:
                User-Agent: 'My App/1.0'
        max_host_connections: 10 # Connexions max par hôte
```

### Scoped Clients (Clients Scopés)
Permet d'auto-configurer le client selon l'URL ou le service injecté.

```yaml
framework:
    http_client:
        scoped_clients:
            # Option 1 : Matching sur URL (regex)
            github.client:
                scope: 'https://api\.github\.com'
                headers:
                    Authorization: 'token %env(GITHUB_TOKEN)%'
            
            # Option 2 : Base URI (URLs relatives dans request())
            content_api:
                base_uri: 'https://content.example.com'
                timeout: 2.5
```

**Injection :**
Utilisez le "Named Autowiring" (nom du service = nom de la variable en camelCase).
```php
public function __construct(HttpClientInterface $githubClient) { ... }
```

### Retry Failed Requests
Symfony peut relancer automatiquement les requêtes échouées (réseau ou 5xx).

```yaml
framework:
    http_client:
        default_options:
            retry_failed:
                max_retries: 3
                delay: 1000 # ms
                multiplier: 2
                http_codes: [429, 500, 502, 503, 504]
```

## Requêtes Concurrentes & Streaming
Le client est asynchrone par défaut. Tant que vous n'appelez pas une méthode bloquante (`getContent()`, `getStatusCode()`), le réseau peut travailler en arrière-plan.

### Multiplexing (Exécution parallèle)
Pour attendre plusieurs réponses simultanément :

```php
$responses = [
    $client->request('GET', 'https://api.a.com'),
    $client->request('GET', 'https://api.b.com'),
];

foreach ($client->stream($responses) as $response => $chunk) {
    if ($chunk->isTimeout()) { /* ... */ }
    if ($chunk->isFirst()) {
        // Headers reçus
    }
    if ($chunk->isLast()) {
        // Réponse complète reçue
        $data = $response->toArray();
    }
}
```
Le temps total sera égal à la requête la plus lente, pas la somme.

## Tests et Mocking
Le composant fournit des outils puissants pour tester sans appels réseaux.

### MockHttpClient
Remplace le client réel dans les tests.

```php
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

// Mock simple
$mockResponse = new MockResponse(json_encode(['id' => 123]), [
    'http_code' => 201,
    'response_headers' => ['Content-Type' => 'application/json'],
]);

$client = new MockHttpClient($mockResponse, 'https://example.com');
$result = $client->request('POST', '/users');

// Assertions sur la requête envoyée
$this->assertSame('POST', $mockResponse->getRequestMethod());
$this->assertSame('https://example.com/users', $mockResponse->getRequestUrl());
$this->assertStringContainsString('Content-Type', $mockResponse->getRequestOptions()['headers'][0] ?? '');
```

### Mock avec Callback
Pour une logique dynamique ou simuler des exceptions réseau.

```php
$callback = function ($method, $url, $options) {
    if ($method === 'DELETE') {
        return new MockResponse('', ['http_code' => 204]);
    }
    return new MockResponse('{"error": "Not Found"}', ['http_code' => 404]);
};

$client = new MockHttpClient($callback);
```

### Simulation d'erreurs réseau
```php
// Simule un DNS failure ou connection timeout
$response = new MockResponse([], ['error' => 'Network unreachable']);
```

### Profiling & HAR
Le client est intégré au Profiler Symfony. En test, vous pouvez même rejouer des fichiers `.har` (HTTP Archive) enregistrés par votre navigateur.

## Fonctionnement Interne

### Architecture
*   **HttpClientInterface** : Le contrat public.
*   **NativeHttpClient** : Utilise `stream_socket_client` (PHP natif). Léger et portable.
*   **CurlHttpClient** : Utilise l'extension `curl`. Plus performant pour HTTP/2 et PUSH.
*   **ResponseInterface** : Un itérateur qui yield des "Chunks" de données.

### Le Flux
1.  **Request** : Les headers et le body sont préparés mais la connexion n'est pas forcément ouverte immédiatement (Lazy).
2.  **Stream** : Lors de l'accès au contenu, les données sont streamées (pas de chargement complet en RAM).
3.  **Async** : `curl_multi_exec` est utilisé en interne pour gérer plusieurs requêtes en parallèle sur le même thread PHP.

## ⚠️ Points de vigilance (Certification)

1.  **Exceptions** :
    *   `TransportExceptionInterface` : Erreur réseau pure (DNS, pas de connexion). Lancée souvent dès l'appel à une méthode de `Response`.
    *   `ClientExceptionInterface` (4xx) & `ServerExceptionInterface` (5xx) : Lancées UNIQUEMENT si vous lisez le contenu (`getContent()`, `toArray()`) ET que `$throw` est true (défaut).
    *   `RedirectionExceptionInterface` : 3xx (si max_redirects atteint).

2.  **Idempotence** : `HttpClient::create()` détecte automatiquement si `curl` est installé. Si oui, il l'utilise (meilleures perfs, HTTP/2). Sinon, fallback sur les streams PHP natifs.

3.  **Server-Sent Events (SSE)** :
    ```php
    $response = $client->request('GET', 'https://...', ['headers' => ['Accept' => 'text/event-stream']]);
    foreach ($client->stream($response) as $chunk) {
        // Traitement des événements chunk par chunk
    }
    ```

## Ressources
*   [Symfony Docs - HTTP Client](https://symfony.com/doc/current/http_client.html)
*   [Symfony Casts - HttpClient](https://symfonycasts.com/screencast/symfony-http-client)
