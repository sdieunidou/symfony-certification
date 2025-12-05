# Edge Side Includes (ESI)

## Concept clé
ESI permet de cacher une page entière tout en gardant des parties dynamiques (ou avec des durées de cache différentes).
Le cache (Varnish/HttpCache) sert la page, voit un tag `<esi:include src="..." />`, fait une requête interne pour récupérer ce fragment, l'assemble, et renvoie le tout.

## Application dans Symfony 7.0
1.  Activer ESI dans `framework.yaml` : `esi: { enabled: true }`.
2.  Utiliser le helper `render_esi` dans Twig.

```twig
{# index.html.twig (Caché 1h) #}
<h1>Bienvenue</h1>

{# Sidebar dynamique (Non cachée ou cachée 5min) #}
{{ render_esi(controller('App\\Controller\\NewsController::latest')) }}
```

Le contrôleur `latest` doit retourner une `Response` avec ses propres headers de cache.

## Points de vigilance (Certification)
*   **Fallback** : Si aucun Gateway Cache ne gère l'ESI (ex: en dev sans HttpCache), Symfony remplace automatiquement `render_esi` par un `render` classique (sous-requête synchrone). C'est transparent.
*   **Fragments** : L'URL générée pour l'ESI est une route interne `/_fragment`. Elle est signée pour la sécurité.

## Ressources
*   [Symfony Docs - ESI](https://symfony.com/doc/current/http_cache/esi.html)

