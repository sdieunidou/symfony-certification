# Le Composant HttpFoundation

Le composant **HttpFoundation** fournit une couche d'abstraction orientée objet pour la spécification HTTP. Il remplace l'utilisation des variables superglobales PHP (`$_GET`, `$_POST`, `$_SESSION`, `$_COOKIE`, `$_FILES`, `$_SERVER`) par des objets `Request` et `Response`.

C'est la base fondamentale de Symfony et de nombreux autres frameworks (Laravel, Drupal, etc.).

---

## 1. L'Objet Request

Il encapsule toutes les informations de la requête entrante.

### Création
Le plus souvent, Symfony crée cet objet pour vous. En dehors du framework :
```php
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
```

### Accès aux données (ParameterBags)
Les propriétés de la Request sont des instances de `ParameterBag` (sauf `content`). N'accédez jamais directement aux propriétés publiques, utilisez les méthodes `get()`.

*   `$request->query` : Paramètres d'URL `$_GET`.
*   `$request->request` : Paramètres de corps `$_POST`.
*   `$request->attributes` : Paramètres propres à l'application (ex: variables de route `_route`, `id`).
*   `$request->cookies` : Cookies `$_COOKIE`.
*   `$request->files` : Fichiers uploadés `$_FILES`.
*   `$request->server` : Variables serveur `$_SERVER`.
*   `$request->headers` : En-têtes HTTP (`HeaderBag`).

### Méthodes utiles
```php
// Récupérer une valeur avec défaut
$page = $request->query->get('page', 1);

// Filtrer (cast en int)
$id = $request->query->getInt('id');

// Vérifier la méthode HTTP
if ($request->isMethod('POST')) { ... }

// Récupérer le contenu brut (JSON payload)
$content = $request->getContent();
$data = $request->toArray(); // Depuis Symfony 5.2, parse auto le JSON

// Récupérer l'IP client (gère les proxys si configuré)
$ip = $request->getClientIp();
```

---

## 2. L'Objet Response

Il représente la réponse HTTP qui sera envoyée au client.

### Création basique
```php
use Symfony\Component\HttpFoundation\Response;

$response = new Response(
    'Contenu HTML',
    Response::HTTP_OK, // 200
    ['content-type' => 'text/html']
);

// Envoi des en-têtes et du contenu
$response->send();
```

### Types de Réponses Spécialisés

1.  **JsonResponse** : Encode automatiquement en JSON et met le bon Content-Type.
    ```php
    return new JsonResponse(['status' => 'ok']);
    ```

2.  **BinaryFileResponse** : Optimisé pour envoyer des fichiers (supporte `Range`, `X-Sendfile`).
    ```php
    return new BinaryFileResponse('/path/to/file.pdf');
    ```

3.  **StreamedResponse** : Pour les réponses générées à la volée (gros exports CSV) afin de ne pas saturer la mémoire.
    ```php
    return new StreamedResponse(function() {
        echo 'data...';
        flush();
    });
    ```

4.  **RedirectResponse** : Redirection HTTP (301/302).
    ```php
    return new RedirectResponse('/nouvelle-page');
    ```

---

## 3. Gestion de la Session

HttpFoundation abstrait également la gestion native des sessions PHP.

```php
$session = $request->getSession();

// Stocker
$session->set('user_id', 123);

// Lire
$userId = $session->get('user_id');

// Supprimer
$session->remove('user_id');

// Invalider (Logout)
$session->invalidate();
```

### Flash Messages
Messages stockés en session qui ne durent qu'une seule requête (ex: "Formulaire envoyé avec succès").

```php
$session->getFlashBag()->add('success', 'Bravo !');

// À la requête suivante, les lire et les vider
$messages = $session->getFlashBag()->get('success');
```

---

## 4. Fonctionnalités Avancées

### HeaderBag
Normalise les noms des en-têtes (insensible à la casse).
```php
$request->headers->get('content-type'); // Fonctionne même si envoyé comme 'Content-Type'
```

### Cache HTTP
L'objet Response possède des méthodes helpers pour gérer les en-têtes de cache (`Cache-Control`, `ETag`, `Last-Modified`).

```php
$response->setPublic();
$response->setMaxAge(600);
$response->setSharedMaxAge(600);

// Validation (304 Not Modified)
$response->setEtag(md5($content));
if ($response->isNotModified($request)) {
    return $response; // Envoie juste le header 304, corps vide
}
```

### Trusted Proxies
Si l'application est derrière un Load Balancer (AWS ELB, Cloudflare, Nginx), `getClientIp()` retournera l'IP du proxy.
Il faut configurer les "Trusted Proxies" pour dire à Request de faire confiance aux headers `X-Forwarded-For`.

```php
Request::setTrustedProxies(
    ['10.0.0.0/8'], // IPs des load balancers
    Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST
);
```

---

## Fonctionnement Interne

### Architecture
*   **Request** : Wrapper orienté objet des superglobales (`$_GET`, `$_POST`, etc.).
*   **ParameterBag** : Stockage clé-valeur utilisé pour `query`, `request`, `attributes`.
*   **Response** : Wrapper pour l'envoi des headers (`header()`) et du contenu (`echo`).

### Le Flux (Request Factory)
1.  `Request::createFromGlobals()` : Lit `$_SERVER` et autres.
2.  **Normalisation** : Gestion des headers proxy (`X-Forwarded-For`), surcharge de méthodes (`_method`).
3.  **Session** : La session n'est démarrée que si `$request->getSession()` est appelé.

## 5. Points de vigilance pour la Certification

*   **ParameterBag::get() vs getAlpha() / getInt()** : Connaître les méthodes de filtrage basiques.
*   **Request::createFromGlobals()** : C'est la méthode statique qui initialise la Request dans `public/index.php`.
*   **Override Globals** : Symfony permet d'écraser les globales PHP avec `$request->overrideGlobals()`, mais c'est déconseillé.
*   **Priorité des paramètres** : `$request->get('key')` cherche d'abord dans `query` (GET), puis `attributes` (Route), puis `request` (POST). C'est ambigu et déprécié/déconseillé d'utiliser `get()` directement sur `$request`. Préférer `$request->query->get()` ou `$request->request->get()`.
