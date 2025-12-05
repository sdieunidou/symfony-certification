# Interaction Client / Serveur

## Concept clé
Le modèle Client-Serveur est l'architecture fondamentale du Web.
1.  **Client** (Navigateur, Mobile, API Client) : Envoie une **Requête** HTTP.
2.  **Serveur** (Apache, Nginx, PHP-FPM) : Reçoit la requête, la traite, et renvoie une **Réponse** HTTP.
3.  Le protocole **HTTP** (HyperText Transfer Protocol) définit le format de ces échanges (texte brut structuré).

## Application dans Symfony 7.0
Symfony est construit autour de ce modèle "Request -> Response".
Le framework encapsule la requête HTTP globale (superglobales PHP) dans un objet `Request` et attend que le développeur retourne un objet `Response`.

```
Request (Objet) -> [Front Controller -> Kernel -> Controller] -> Response (Objet)
```

## Exemple de code

```php
<?php
// index.php (Front Controller) simplifé

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

// 1. Création de l'objet Request à partir des globales ($_GET, $_POST, etc.)
$request = Request::createFromGlobals();

// 2. Traitement (simulé ici, normalement fait par le Kernel)
$name = $request->query->get('name', 'World');

// 3. Création de la Réponse
$response = new Response(
    "Hello $name",
    Response::HTTP_OK,
    ['content-type' => 'text/plain']
);

// 4. Envoi de la réponse (headers + contenu) au client
$response->send();
```

## Points de vigilance (Certification)
*   **Stateless** : HTTP est un protocole sans état. Chaque requête est indépendante. La persistance (sessions, cookies) est une couche ajoutée par-dessus.
*   **Cycle de vie** : Bien comprendre que PHP est "mort" entre deux requêtes (architecture *Shared Nothing*), contrairement à Node.js ou Java Servlet.
*   **Symfony** : La méthode `handle()` du `Kernel` transforme une `Request` en `Response`.

## Ressources
*   [Symfony Docs - HTTP Fundamentals](https://symfony.com/doc/current/introduction/http_fundamentals.html)

