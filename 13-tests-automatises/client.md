# Objet Client (KernelBrowser)

## Concept clé
L'objet `KernelBrowser` est votre interface principale pour les tests fonctionnels. Il remplace le navigateur web.
Il permet d'envoyer des requêtes et d'interagir avec la réponse via le `Crawler`.

## Méthodes Principales

### 1. Navigation (`request`)
```php
// Signature : method, uri, parameters, files, server, content
$crawler = $client->request('GET', '/posts');

// POST API JSON
$client->request(
    'POST', 
    '/api/posts', 
    [], 
    [], 
    ['CONTENT_TYPE' => 'application/json'], 
    json_encode(['title' => 'Test'])
);
```

### 2. Gestion des Redirections
Par défaut, le client ne suit **pas** les redirections (pour vous permettre de les tester).

```php
// Suivre une redirection manuellement après une requête
$crawler = $client->followRedirect();

// Forcer le client à suivre toutes les redirections automatiquement
$client->followRedirects();

// Désactiver le suivi automatique
$client->followRedirects(false);
```

### 3. Authentification (`loginUser`)
Helper pour connecter un utilisateur sans passer par le formulaire de login.

```php
$user = $userRepository->findOneByEmail('admin@test.com');
// Simule le login sur le firewall 'main' (par défaut)
$client->loginUser($user);

// Sur un firewall spécifique
$client->loginUser($user, 'my_firewall');
```

Avec un utilisateur en mémoire (`InMemoryUser`) :
```php
use Symfony\Component\Security\Core\User\InMemoryUser;
// Doit être défini dans security.yaml (users_in_memory)
$testUser = new InMemoryUser('admin', 'password', ['ROLE_ADMIN']);
$client->loginUser($testUser);
```

### 4. AJAX (`xmlHttpRequest`)
Raccourci pour `request()` avec le header `X-Requested-With: XMLHttpRequest`.

```php
$client->xmlHttpRequest('POST', '/submit', ['name' => 'Fabien']);
```

### 5. En-têtes HTTP Personnalisés
Vous pouvez définir des headers globaux pour le client ou par requête.

```php
// Globalement (lors de la création)
$client = static::createClient([], [
    'HTTP_HOST'       => 'en.example.com',
    'HTTP_USER_AGENT' => 'MySuperBrowser/1.0',
]);

// Par requête (5ème argument $server)
$client->request('GET', '/', [], [], [
    'HTTP_HOST' => 'en.example.com',
]);
```
*Note : Les headers doivent être en majuscules et préfixés par `HTTP_` (sauf `CONTENT_TYPE` etc.).*

## Historique et Navigation
Le client simule un navigateur :
*   `$client->back()`
*   `$client->forward()`
*   `$client->reload()`
*   `$client->restart()` : Efface les cookies et l'historique.

## Gestion du Kernel (Reboot) & Isolation
Par défaut, le client **reboote le kernel** entre chaque requête.
*   **Avantage** : Isolation totale (nouveaux services, pas de fuite de mémoire).
*   **Inconvénient** : Les entités Doctrine sont détachées, le token de sécurité est perdu.

### Empêcher le Reboot
```php
$client->disableReboot();
```

### Le problème `kernel.reset`
Même sans reboot, Symfony appelle `reset()` sur les services taggés `kernel.reset` (dont le token storage). Pour garder l'authentification entre plusieurs requêtes dans un même test sans tout casser, la doc suggère de retirer ce tag via un `CompilerPass` dans `Kernel.php` (environnement de test uniquement).

## Accès aux Objets Internes
Utile pour le débuggage ou des assertions avancées.

```php
$history = $client->getHistory();
$cookieJar = $client->getCookieJar();

// Objets de la couche HttpKernel
$request = $client->getRequest();
$response = $client->getResponse();

// Objets de la couche BrowserKit (Interne)
$internalRequest = $client->getInternalRequest();
$internalResponse = $client->getInternalResponse();

$crawler = $client->getCrawler();
```

## Profiler & Exceptions

### Activer le Profiler
Pour vérifier les performances ou les requêtes SQL d'une page :

```php
$client->enableProfiler(); // Pour la prochaine requête
$crawler = $client->request('GET', '/profiler');
$profile = $client->getProfile();
```

### Rapporter les Exceptions
Par défaut, le client "attrape" les exceptions (pour afficher la page d'erreur 500). Pour laisser PHPUnit échouer sur l'exception réelle (et voir la stack trace) :

```php
$client->catchExceptions(false);
```

## ⚠️ Points de vigilance (Certification)
*   **Interactions** : `$client->clickLink('Text')` et `$client->submitForm('Button', ...)` sont des raccourcis pratiques qui évitent de passer manuellement par le Crawler pour sélectionner le lien/bouton.
