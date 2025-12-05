# Requête HTTP

## Concept clé
Une requête HTTP est un message texte envoyé par le client contenant :
1.  **Ligne de requête** : Méthode (GET, POST...), URI (`/path?query=1`), Version HTTP (HTTP/1.1).
2.  **En-têtes (Headers)** : Métadonnées (Host, User-Agent, Content-Type, Accept...).
3.  **Corps (Body)** : Données optionnelles (JSON, XML, Form data), séparé des en-têtes par une ligne vide.

## Application dans Symfony 7.0
Le composant `HttpFoundation` fournit l'objet `Request`.
Il normalise l'accès aux superglobales PHP (`$_GET`, `$_POST`, `$_COOKIE`, `$_FILES`, `$_SERVER`).

L'objet `Request` possède des propriétés publiques ("ParameterBags") pour accéder aux données :
*   `$request->query` : Paramètres d'URL (`$_GET`)
*   `$request->request` : Paramètres POST (`$_POST`)
*   `$request->attributes` : Attributs de routage (paramètres d'URL parsés comme `{id}`) et données internes.
*   `$request->cookies` : Cookies (`$_COOKIE`)
*   `$request->files` : Fichiers uploadés (`$_FILES`)
*   `$request->server` : Variables serveur (`$_SERVER`)
*   `$request->headers` : En-têtes HTTP (dérivé de `$_SERVER`)

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Request;

public function index(Request $request): Response
{
    // Lire un paramètre GET ?page=2
    $page = $request->query->getInt('page', 1);

    // Lire un paramètre POST
    $token = $request->request->get('_token');

    // Lire un header (insensible à la casse)
    $userAgent = $request->headers->get('User-Agent');

    // Lire le contenu brut (Body) pour une API JSON
    $content = $request->getContent();
    $data = $request->toArray(); // Helper Symfony pour décoder le JSON (si content-type json)

    // Vérifier la méthode
    if ($request->isMethod('POST')) {
        // ...
    }
    
    // Récupérer l'IP client
    $ip = $request->getClientIp();
    
    return new Response('...');
}
```

## Points de vigilance (Certification)
*   **Priorité** : Ne jamais utiliser `$_GET` ou `$_POST` directement dans Symfony. Utiliser l'objet `Request`.
*   **Attributes** : `$request->attributes` est le seul "sac" modifiable par le framework (pour stocker les paramètres de route `_route`, `_controller`, `id`, etc.).
*   **getContent()** : Permet de lire le flux `php://input`. Ne peut être lu qu'une seule fois en PHP natif, mais Symfony met en cache le contenu pour permettre des lectures multiples.
*   **Override** : La méthode `enableHttpMethodParameterOverride()` permet de simuler des méthodes PUT/DELETE via un champ caché `_method` dans un formulaire POST (utile car les formulaires HTML ne supportent que GET et POST).

## Ressources
*   [Symfony Docs - The Request Object](https://symfony.com/doc/current/components/http_foundation.html#request)

