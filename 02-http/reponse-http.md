# Réponse HTTP

## Concept clé
Une réponse HTTP est le message renvoyé par le serveur :
1.  **Ligne de statut** : Version HTTP, Code (200), Message (OK).
2.  **En-têtes** : Content-Type, Content-Length, Set-Cookie, Cache-Control...
3.  **Corps** : Le contenu HTML, JSON, image, etc.

## Application dans Symfony 7.0
L'objet `Response` (et ses sous-classes `JsonResponse`, `BinaryFileResponse`, `StreamedResponse`) encapsule ces éléments.
Le contrôleur **doit** retourner un objet `Response`.

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Cookie;

public function api(): Response
{
    // Réponse JSON
    $response = new JsonResponse(['status' => 'ok']);
    
    // Ajouter un header
    $response->headers->set('X-App-Version', '1.0');
    
    // Ajouter un cookie
    $response->headers->setCookie(new Cookie('theme', 'dark'));
    
    // Définir le cache (Cache-Control)
    $response->setPublic();
    $response->setMaxAge(3600);
    
    // Modifier le code de statut
    $response->setStatusCode(Response::HTTP_ACCEPTED);

    return $response;
}
```

## Points de vigilance (Certification)
*   **prepare()** : La méthode `$response->prepare($request)` est appelée par le framework juste avant l'envoi. Elle corrige certains headers (Content-Type, Content-Length) selon la requête et les normes HTTP.
*   **send()** : Envoie les headers puis le contenu. Dans une app Symfony full-stack, c'est le `Kernel` qui appelle `send()`, pas vous.
*   **Streaming** : Pour les gros fichiers ou les réponses longues, utiliser `StreamedResponse` pour ne pas tout charger en mémoire.
*   **JSON** : `JsonResponse` encode automatiquement les données et définit le header `Content-Type: application/json`.

## Ressources
*   [Symfony Docs - The Response Object](https://symfony.com/doc/current/components/http_foundation.html#response)

