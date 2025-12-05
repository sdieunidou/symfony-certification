# Webpack Encore

Webpack Encore est une bibliothèque purement JavaScript qui encapsule Webpack pour fournir une API simple et puissante pour compiler et gérer les assets (CSS, JS, images). C'est la solution "Build" standard de Symfony.

## 1. Installation

Nécessite Node.js et npm/yarn.

```bash
composer require symfony/webpack-encore-bundle
npm install
```

Cela crée un fichier `webpack.config.js` et un dossier `assets/`.

## 2. Configuration (webpack.config.js)

Le fichier de configuration utilise une API fluide pour définir le build.

```javascript
// webpack.config.js
const Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    
    // Entrée JS principale (assets/app.js) -> public/build/app.js
    .addEntry('app', './assets/app.js')
    
    // Active le versioning (hash dans le nom du fichier en prod)
    .enableVersioning(Encore.isProduction())
    
    // Active SASS/SCSS support
    .enableSassLoader()
    
    // Active React si besoin
    // .enableReactPreset()
    
    // Sépare le runtime webpack dans son propre fichier (optimisation)
    .enableSingleRuntimeChunk()
;

module.exports = Encore.getWebpackConfig();
```

## 3. Utilisation

### Commandes de Build
```bash
# Compile pour le développement
npm run dev

# Compile et observe les changements (Watch)
npm run watch

# Compile pour la production (minification, tree-shaking)
npm run build
```

### Dans Twig
Le bundle fournit des fonctions Twig pour inclure les fichiers générés (qui ont des noms dynamiques avec le versioning).

```twig
{# Référence l'entrée 'app' définie dans webpack.config.js #}
{% block stylesheets %}
    {{ encore_entry_link_tags('app') }}
{% endblock %}

{% block javascripts %}
    {{ encore_entry_script_tags('app') }}
{% endblock %}
```

Cela génère automatiquement les balises `<link>` et `<script>` pointant vers `/build/app.css` et `/build/app.js` (ou `app.a3bc45.js` en prod).

## 4. Fonctionnalités Clés

*   **Versioning / Cache Busting** : Ajoute un hash unique aux noms de fichiers en production pour invalider le cache navigateur.
*   **Manifest** : Génère un `manifest.json` qui map les noms logiques ("app.js") vers les fichiers physiques versionnés.
*   **Split Chunks** : Peut extraire le code partagé (vendor) dans des fichiers séparés pour optimiser le cache.
*   **Source Maps** : Activés par défaut en dev pour le débogage.
*   **Babel** : Configuré automatiquement pour transpiler le JS moderne pour les anciens navigateurs.

## 5. Différences avec AssetMapper

| Caractéristique | Webpack Encore | AssetMapper |
| :--- | :--- | :--- |
| **Environnement** | Node.js requis | PHP natif |
| **Approche** | Build / Bundling | Import Maps / No-Build |
| **Cible** | Applications complexes (React, Vue, SPAs) | Applications Symfony standard (Stimulus, Turbo) |
| **Performance** | Optimisé pour le chargement initial (1 fichier minifié) | Optimisé pour HTTP/2 (multiplexing) |
| **CSS** | Traitement lourd possible (PostCSS, tout plugin Webpack) | Support natif ou via binaires PHP séparés |

## 6. Bonnes Pratiques

*   Utiliser `npm run build` avant le déploiement.
*   Ne jamais committer le dossier `public/build` (l'ajouter au `.gitignore`).
*   Utiliser `splitEntryChunks()` pour éviter de dupliquer le code si vous avez plusieurs entrées.

