# Rendu de Contrôleur (Embedding Controllers)

## Concept clé
Comment inclure un bloc dynamique (qui nécessite sa propre logique PHP et requête DB) dans un template ?
Exemple : Un panier dans le header, une liste de "Derniers articles" dans la sidebar, présente sur toutes les pages.
L'approche "Include" ne suffit pas car il faudrait passer les variables `cart` ou `articles` à **tous** les contrôleurs du site.

## La Solution : `render(controller())`
On appelle un contrôleur depuis la vue.

### 1. Créer le contrôleur partiel
```php
// src/Controller/Partial/BlogPartialController.php
public function recentArticles(int $max = 3, ArticleRepository $repo): Response
{
    // Logique métier propre au widget
    $articles = $repo->findRecent($max);
    
    return $this->render('partials/recent_articles.html.twig', [
        'articles' => $articles
    ]);
}
```

### 2. L'appeler dans Twig
```twig
{# base.html.twig #}
<aside>
    {{ render(controller('App\\Controller\\Partial\\BlogPartialController::recentArticles', { 'max': 5 })) }}
</aside>
```

## Comment ça marche ? (Sous-Requête)
1.  Twig appelle la fonction `render()`.
2.  Symfony crée une nouvelle `Request` (sous-requête).
3.  Le `HttpKernel` traite cette requête complètement (Events `kernel.request`, `kernel.controller`, etc.).
4.  Le contrôleur retourne une `Response`.
5.  Le contenu de la `Response` est injecté dans le HTML final.

## 🧠 Concepts Clés
1.  **Isolation** : Le contrôleur embarqué ne connaît pas le contexte du contrôleur principal (sauf via les arguments passés).
2.  **Performance** : C'est lourd ! Chaque `render(controller())` relance le framework. Si vous en avez 10 sur une page, c'est lent.
3.  **ESI (Edge Side Includes)** : C'est là que ça devient puissant. Si vous utilisez un reverse proxy (Varnish / Symfony HttpCache), vous pouvez utiliser `render_esi()` au lieu de `render()`. Le proxy chargera la page principale et fera des requêtes séparées (en parallèle) pour les fragments, ou utilisera le cache.

## ⚠️ Points de vigilance (Certification)
*   **Syntaxe** : `{{ render(controller(...)) }}` est la syntaxe moderne. Le tag `{% render ... %}` est obsolète.
*   **Arguments** : Les arguments passés (ex: `{ 'max': 5 }`) sont envoyés comme attributs de requête (`$request->attributes`) ou arguments de méthode (autowiring).

## Ressources
*   [Symfony Docs - Embedding Controllers](https://symfony.com/doc/current/templates.html#embedding-controllers)
*   [Fragment Caching (ESI)](https://symfony.com/doc/current/http_cache/esi.html)
