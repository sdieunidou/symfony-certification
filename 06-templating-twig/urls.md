# Génération d'URLs (Twig)

## Concept clé
Comme dans les contrôleurs, on ne code jamais d'URLs en dur dans les templates. On utilise les helpers Twig fournis par Symfony.

## Application dans Symfony 7.0
Deux fonctions principales :
1.  `path('route_name', { params })` : Génère une URL relative (`/blog/slug`).
2.  `url('route_name', { params })` : Génère une URL absolue (`https://example.com/blog/slug`). Indispensable pour les emails ou les flux RSS.

## Exemple de code

```twig
<a href="{{ path('blog_show', { slug: post.slug }) }}">
    Lire l'article
</a>

{# Ajout de Query String automatique pour les params inconnus #}
<a href="{{ path('search', { q: 'symfony', page: 2 }) }}">
    {# Résultat : /search?q=symfony&page=2 #}
    Rechercher
</a>

{# Assets (images, css, js) ne sont pas des routes ! #}
<img src="{{ asset('images/logo.png') }}" alt="Logo">
```

## Points de vigilance (Certification)
*   **Performance** : `path()` est très rapide.
*   **Paramètres manquants** : Si un paramètre obligatoire de la route est manquant, Twig lance une exception (RuntimeError).
*   **Scheme** : `url()` respecte le scheme courant (http/https) ou celui forcé dans la route.

## Ressources
*   [Symfony Docs - Linking to Pages](https://symfony.com/doc/current/templates.html#linking-to-pages)

