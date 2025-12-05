# Gestion des Assets

## Concept clé
Les "Assets" sont les fichiers statiques publics (CSS, JS, Images, Fonts).
Symfony fournit des outils pour référencer ces fichiers de manière robuste (versioning pour le cache-busting, CDN, sous-dossiers).

## Deux Écosystèmes

### 1. AssetMapper (Symfony 6.3+ / 7.0) - **Recommandé (Simplicité)**
Le "Modern Way" pour les applications qui n'ont pas besoin de la complexité de Node.js.
*   **Principe** : Le navigateur charge les modules ES directement. Pas de build step complexe.
*   **Configuration** : `config/packages/asset_mapper.yaml`.
*   **Utilisation** :
    ```twig
    {# templates/base.html.twig #}
    <script type="importmap">
        {{ importmap('app') }}
    </script>
    
    <img src="{{ asset('logo.png') }}">
    ```
*   **Commande** : `php bin/console importmap:require bootstrap` (télécharge les libs vendor dans `assets/vendor`).

### 2. Webpack Encore (Legacy / Avancé)
Un wrapper autour de Webpack. Nécessite Node.js et NPM.
*   **Principe** : Compile, minifie et bundle les fichiers JS/CSS dans `public/build`.
*   **Utilisation** :
    ```twig
    {{ encore_entry_link_tags('app') }}
    {{ encore_entry_script_tags('app') }}
    ```
*   **Fonction** : `asset('build/app.css')` utilise un fichier `manifest.json` pour trouver le vrai nom du fichier (`app.123456.css`).

## Fonction `asset()`
La fonction de base (composant `Asset`).
Elle pointe vers le dossier `public/`.

```twig
<link rel="stylesheet" href="{{ asset('styles/app.css') }}">
{# Résultat : /styles/app.css?v=123hash #}
```

## Packages d'Assets
Vous pouvez configurer plusieurs "packages" (ex: un pour les images locales, un pour les images CDN).

```yaml
# config/packages/framework.yaml
framework:
    assets:
        packages:
            cdn_images:
                base_urls: ['https://img.cdn.com']
```

```twig
<img src="{{ asset('logo.png', 'cdn_images') }}">
{# Résultat : https://img.cdn.com/logo.png #}
```

## 🧠 Concepts Clés
1.  **Cache Busting** : Symfony ajoute automatiquement un paramètre de version (hash du contenu ou version globale) pour forcer le navigateur à recharger le fichier si vous le modifiez.
2.  **Chemin absolu** : `asset()` génère un chemin relatif à la racine (`/css/style.css`). Si l'app est dans un sous-dossier (`/myapp/public`), `asset` gère le préfixe automatiquement.

## ⚠️ Points de vigilance (Certification)
*   **Mix** : Vous pouvez utiliser `asset()` même avec Webpack Encore pour les images statiques non traitées par Webpack.
*   **Chemins** : `asset('foo.png')` cherche dans `public/foo.png`. Pour les fichiers sources (SCSS), ils sont dans `assets/` (racine) et ne sont pas accessibles directement par le navigateur sans compilation.

## Ressources
*   [Symfony Docs - AssetMapper](https://symfony.com/doc/current/frontend/asset_mapper.html)
*   [Symfony Docs - Assets Component](https://symfony.com/doc/current/reference/twig_reference.html#asset)
