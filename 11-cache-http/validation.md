# Modèle de Validation (Validation Model)

## Concept clé
Quand le cache a expiré (ou s'il veut vérifier la fraîcheur), il contacte le serveur avec une "Requête Conditionnelle".
*   "J'ai la version du `Last-Modified: ...`. Est-elle toujours bonne ?" (`If-Modified-Since`)
*   "J'ai la version avec l'`ETag: "abc"`. Est-elle toujours bonne ?" (`If-None-Match`)

Si la réponse n'a pas changé, le serveur répond **304 Not Modified** (sans contenu). On économise la bande passante et le temps de génération du body.

## Application dans Symfony 7.0
Symfony gère la logique conditionnelle avec `isNotModified()`.

```php
public function show(Article $article, Request $request): Response
{
    $response = new Response();
    
    // Définir les validateurs
    $response->setEtag(md5($article->getContent()));
    $response->setLastModified($article->getUpdatedAt());
    $response->setPublic();

    // Vérifier si la requête correspond (If-None-Match == ETag ?)
    if ($response->isNotModified($request)) {
        // Configure le status 304 et vide le contenu
        return $response;
    }

    // Sinon, on génère le contenu (coûteux)
    $response->setContent($this->renderView('...'));
    
    return $response;
}
```

## Points de vigilance (Certification)
*   **ETag** (Entity Tag) : Une empreinte (hash) du contenu. Plus précis que la date (qui est à la seconde près).
*   **Weak ETag** : `W/"..."`. Indique que le contenu est sémantiquement identique mais peut différer octet par octet. Symfony génère des Strong ETags par défaut.

## Ressources
*   [Symfony Docs - Validation](https://symfony.com/doc/current/http_cache/validation.html)

