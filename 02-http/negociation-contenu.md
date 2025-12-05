# Négociation de Contenu

## Concept clé
Mécanisme HTTP permettant de servir différentes versions d'une ressource à la même URI, selon les préférences du client (en-têtes `Accept-*`).
*   `Accept` : Format (HTML, JSON, XML)
*   `Accept-Language` : Langue (fr, en)
*   `Accept-Encoding` : Compression (gzip, br)

## Application dans Symfony 7.0
Symfony ne fait pas de négociation "automatique" magique (comme `FOSRestBundle` pouvait le faire), mais fournit les outils pour la faire manuellement ou via le format de requête.
Le routing peut inclure le `_format` (ex: `/api/posts.json`).

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

public function show(Request $request, int $id): Response
{
    // 1. Via l'extension d'URL (Routing: /post/{id}.{_format})
    // $format est automatiquement 'html', 'json', etc.
    $format = $request->getRequestFormat();

    // 2. Via le header Accept (si pas d'extension)
    // getPreferredFormat vérifie l'ordre de préférence du client
    $format = $request->getPreferredFormat(['json', 'xml', 'html']);

    $data = ['id' => $id, 'name' => 'Product'];

    return match ($format) {
        'json' => new JsonResponse($data),
        'xml' => new Response($this->convertToXml($data), 200, ['Content-Type' => 'text/xml']),
        default => $this->render('product/show.html.twig', $data),
    };
}
```

## Points de vigilance (Certification)
*   **Priorité** : Symfony détermine le format (`getRequestFormat`) d'abord via l'attribut de requête `_format` (souvent défini par le routing), puis via le header `Accept` si configuré.
*   **Mime Types** : On peut ajouter de nouveaux formats via `$request->setFormat('csv', 'text/csv')`.

## Ressources
*   [Symfony Docs - Content Negotiation](https://symfony.com/doc/current/components/http_foundation.html#content-negotiation)

