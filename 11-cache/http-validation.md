# Modèle de Validation (Validation Model)

## Concept clé
Lorsque le cache a expiré (Expiration Model), le client doit contacter le serveur.
Au lieu de télécharger à nouveau tout le contenu, il demande : **"Ma version du 1er Janvier est-elle toujours bonne ?"** (Validation Model).
Si oui, le serveur répond **304 Not Modified** (sans corps). Gain immense de bande passante.

## Les Validateurs (RFC 7232)

### 1. ETag (Entity Tag)
Un hash unique du contenu (`"abc-123"`).
*   **Strong ETag** (`"123"`): Le contenu est identique octet par octet.
*   **Weak ETag** (`W/"123"`): Le contenu est sémantiquement identique (ex: généré différemment mais même HTML final).
*   Client envoie: `If-None-Match: "abc-123"`.

### 2. Last-Modified
La date de dernière modification.
*   Client envoie: `If-Modified-Since: Fri, 01 Jan 2024...`.
*   Moins précis que l'ETag (résolution à la seconde).

## Application dans Symfony 7.0

Symfony automatise la logique via `$response->isNotModified($request)`.

```php
public function show(Article $article, Request $request): Response
{
    $response = new Response();

    // 1. Définir les validateurs (Métadonnées légères)
    $response->setEtag(md5($article->getContent()));
    $response->setLastModified($article->getUpdatedAt());
    $response->setPublic();

    // 2. Vérifier la validité
    // Compare les headers de la Request (If-None-Match) avec la Response
    if ($response->isNotModified($request)) {
        // Si match : Symfony configure le status 304, enlève le contenu
        return $response; // Retour immédiat (Pas de render lourd)
    }

    // 3. Génération lourde (si nécessaire)
    $response->setContent($this->renderView('article/show.html.twig', [
        'article' => $article
    ]));

    return $response;
}
```

### Optimisation
Pour que la validation soit utile, le calcul de l'ETag/Date doit être **plus léger** que la génération de la page.
Si vous devez faire toutes les requêtes SQL et le rendu Twig juste pour calculer le MD5, vous n'économisez que la bande passante, pas le CPU.

## 🧠 Concepts Clés
1.  **304 Not Modified** : Une réponse 304 ne contient jamais de corps (`Content-Length: 0`).
2.  **Priorité** : Si `If-None-Match` (ETag) et `If-Modified-Since` (Date) sont présents, l'ETag a la priorité car plus précis.

## ⚠️ Points de vigilance (Certification)
*   **Sessions** : Comme pour l'expiration, attention à ne pas valider publiquement une page contenant des données privées.
*   **Safe Methods** : La validation s'applique aux méthodes GET et HEAD.

## Ressources
*   [Symfony Docs - Validation](https://symfony.com/doc/current/http_cache/validation.html)
