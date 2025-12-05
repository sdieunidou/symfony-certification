# Gestion des Assets

## Concept clé
Les assets (CSS, JS, Images) doivent être référencés de manière portable (pour fonctionner en sous-dossier, ou via un CDN).

## Application dans Symfony 7.0
La fonction `asset()` du composant `Asset` (via `TwigBridge`) est utilisée.

```twig
<link rel="stylesheet" href="{{ asset('styles/app.css') }}">
<img src="{{ asset('logo.png') }}">
```

### Asset Mapper (Nouveauté moderne)
Depuis Symfony 6.3+, l'AssetMapper est la méthode recommandée pour gérer le JS/CSS sans Node.js/Webpack.
Il introduit la fonction `asset` mais aussi des tags d'import map.

```twig
{# Si AssetMapper est utilisé #}
<script type="importmap">
    {{ importmap('app') }}
</script>
```

### Versioning
`asset()` ajoute automatiquement un hash de version si configuré (`app.css?v=123`) pour buster le cache navigateur.

## Points de vigilance (Certification)
*   **Package** : On peut définir plusieurs packages d'assets (ex: pour les images CDN). `{{ asset('img.png', 'cdn_images') }}`.
*   **Manifest** : Si vous utilisez Webpack Encore, vous utiliserez plutôt `asset('build/app.css')` qui lit le `manifest.json` pour trouver le vrai nom du fichier versionné.

## Ressources
*   [Symfony Docs - Assets](https://symfony.com/doc/current/reference/twig_reference.html#asset)
*   [Symfony Docs - AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)

