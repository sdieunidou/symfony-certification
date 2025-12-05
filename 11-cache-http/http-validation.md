# Modèle de Validation (Validation Model)

## Concept clé
Le modèle de validation (ou "Revalidation") permet d'économiser la bande passante et le temps CPU lorsque le cache a expiré mais que le contenu n'a pas changé.
Au lieu de renvoyer toute la page, le serveur répond **304 Not Modified**.

## Les Validateurs

### 1. ETag (Entity Tag)
Un identifiant unique (hash) du contenu.
*   Serveur : Génère le contenu, calcule le hash (`md5($content)`), envoie `ETag: "abc"`.
*   Client : Stocke "abc". À la requête suivante, envoie `If-None-Match: "abc"`.
*   Serveur : Recalcule le hash. Si c'est toujours "abc", répond 304.

### 2. Last-Modified
La date de dernière modification.
*   Serveur : Envoie `Last-Modified: Fri, 01 Jan 2024...`.
*   Client : Stocke la date. Envoie `If-Modified-Since: Fri, 01 Jan 2024...`.
*   Serveur : Compare avec la date de l'objet. Si <=, répond 304.

L'`ETag` est plus précis (car le contenu peut changer sans que la date change, ou inversement la date change sans que le contenu change).

## Application dans Symfony 7.0
La méthode `isNotModified()` de l'objet `Response` automatise tout le travail.

```php
public function show(Article $article, Request $request): Response
{
    $response = new Response();

    // 1. Configurer les validateurs (Métadonnées légères)
    $response->setEtag(md5($article->getContent()));
    $response->setLastModified($article->getUpdatedAt());
    
    // Met le cache en public pour que les proxies puissent aussi l'utiliser
    $response->setPublic();

    // 2. Vérifier la requête
    // Symfony compare les headers de Request avec ceux de Response
    if ($response->isNotModified($request)) {
        // Si match : Symfony configure le status 304, enlève le contenu
        // On retourne immédiatement la réponse vide.
        // Gain : On ne fait pas le render() lourd ni la sérialisation.
        return $response;
    }

    // 3. Si pas match (ou première visite), on fait le travail lourd
    $response->setContent($this->renderView('article/show.html.twig', [
        'article' => $article
    ]));

    return $response;
}
```

## 🧠 Concepts Clés
1.  **Optimisation CPU** : Pour que la validation soit utile, il faut pouvoir calculer l'ETag ou le Last-Modified de manière **légère** (sans tout générer). Si vous devez générer tout le HTML pour calculer son MD5, vous économisez la bande passante mais pas le CPU serveur.
2.  **304 Not Modified** : Une réponse 304 ne contient **pas de corps**.

## ⚠️ Points de vigilance (Certification)
*   **Weak ETag** : Par défaut, Symfony génère des ETags "forts" (`"abc"`). On peut générer des "faibles" (`W/"abc"`) qui signifient "sémantiquement identique" (le HTML peut différer légèrement, espaces, mais le sens est le même).
*   **Priorité** : Si ETag et Last-Modified sont présents, ETag est généralement prioritaire pour la comparaison (plus fiable).

## Ressources
*   [Symfony Docs - Validation Cache](https://symfony.com/doc/current/http_cache/validation.html)
