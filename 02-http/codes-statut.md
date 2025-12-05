# Codes de Statut HTTP

## Concept clé
Le code de statut (Status Code) est un entier de 3 chiffres présent dans la première ligne de la réponse HTTP. Il indique au client le résultat de sa requête.

Les classes de codes :
*   **1xx** : Information (ex: 100 Continue)
*   **2xx** : Succès (ex: 200 OK, 201 Created)
*   **3xx** : Redirection (ex: 301 Moved Permanently, 302 Found, 304 Not Modified)
*   **4xx** : Erreur Client (ex: 400 Bad Request, 401 Unauthorized, 403 Forbidden, 404 Not Found)
*   **5xx** : Erreur Serveur (ex: 500 Internal Server Error, 502 Bad Gateway)

## Application dans Symfony 7.0
La classe `Response` contient des constantes pour tous les codes de statut standards, ce qui évite d'utiliser des "chiffres magiques".
Exemple : `Response::HTTP_OK` (200), `Response::HTTP_NOT_FOUND` (404).

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

// Réponse 200 OK
$response = new Response('Contenu', Response::HTTP_OK);

// Réponse 201 Created (API)
$apiResponse = new JsonResponse(['id' => 123], Response::HTTP_CREATED);

// Réponse 404 Not Found
if (!$user) {
    // Dans un contrôleur Symfony, on lance souvent une exception
    // qui sera convertie en réponse 404 par le framework
    throw $this->createNotFoundException('User not found');
    // Ou manuellement :
    // return new Response('Introuvable', Response::HTTP_NOT_FOUND);
}
```

## Points de vigilance (Certification)
*   **301 vs 302** : 301 est permanent (le navigateur met en cache la redirection), 302 est temporaire.
*   **401 vs 403** :
    *   401 (Unauthorized) = "Qui êtes-vous ?" (Authentification requise ou échouée).
    *   403 (Forbidden) = "Je sais qui vous êtes, mais vous n'avez pas le droit" (Autorisation refusée).
*   **405** : Method Not Allowed (ex: faire un POST sur une route qui n'accepte que GET).
*   **500** : Erreur non gérée dans le code (exception non catchée).
*   **I'm a teapot** : Code 418, présent dans les constantes Symfony (`Response::HTTP_I_AM_A_TEAPOT`) !

## Ressources
*   [MDN - Codes de statut HTTP](https://developer.mozilla.org/fr/docs/Web/HTTP/Status)
*   [Symfony API - Response Constants](https://github.com/symfony/http-foundation/blob/7.0/Response.php)

