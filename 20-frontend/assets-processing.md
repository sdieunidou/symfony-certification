# Traitement des Assets (Sass, Tailwind, TypeScript)

Que vous utilisiez Webpack Encore (Build) ou AssetMapper (No-Build), Symfony supporte les langages et outils modernes comme Sass, Tailwind CSS et TypeScript.

## 1. Avec Webpack Encore (Approche Classique)

Webpack Encore gère ces outils via des loaders Node.js. C'est la méthode la plus flexible.

### Sass / SCSS
```bash
npm install sass-loader sass --save-dev
```
```javascript
// webpack.config.js
Encore.enableSassLoader();
```

### TypeScript
```bash
npm install typescript ts-loader --save-dev
```
```javascript
// webpack.config.js
Encore.enableTypeScriptLoader();
```

### Tailwind CSS
Nécessite PostCSS.
```bash
npm install postcss-loader autoprefixer tailwindcss --save-dev
npx tailwindcss init -p
```
```javascript
// webpack.config.js
Encore.enablePostCssLoader();
```

## 2. Avec AssetMapper (Approche Moderne)

AssetMapper étant "No-Node", il ne compile pas nativement ces langages. Il y a deux stratégies :

### Stratégie A : Binaires PHP (Recommandé)
On utilise des wrappers PHP autour des exécutables autonomes (Go/Rust/Dart) de ces outils. Plus besoin de Node.js.

**Sass :**
```bash
composer require symfony/sass-bundle
php bin/console sass:build --watch
```
Ce bundle télécharge automatiquement le binaire Sass (Dart Sass).

**Tailwind CSS :**
```bash
composer require symfony/tailwind-bundle
php bin/console tailwind:build --watch
```
Ce bundle télécharge le binaire standalone Tailwind.

**TypeScript :**
```bash
composer require symfony/typescript-bundle
php bin/console typescript:build --watch
```

Ces commandes génèrent des fichiers CSS/JS standards dans `assets/` ou `public/` que AssetMapper peut ensuite servir.

### Stratégie B : AssetMapper Compiler (Expérimental/Léger)
Certains paquets permettent une compilation à la volée via PHP pour des besoins simples, mais l'approche "Binaires PHP" est plus robuste pour la production.

## 3. Intégration des Frameworks JS Lourds (Vue, React)

### Via Webpack Encore
C'est la voie royale. Webpack compile les fichiers `.vue` ou `.jsx` en JavaScript standard.
```javascript
Encore.enableReactPreset();
// ou
Encore.enableVueLoader();
```

### Via AssetMapper
C'est possible mais plus complexe car les navigateurs ne comprennent pas le `.vue` ou le JSX.
Il faut :
1.  Soit utiliser les versions "Buildless" des frameworks (ex: Vue via CDN qui compile dans le navigateur -> moins performant).
2.  Soit pré-compiler les composants via un outil externe (Babel/TypeScript) avant de les donner à AssetMapper.

**Recommandation** : Si vous faites une grosse application React/Vue/Svelte, préférez **Webpack Encore** ou **Vite** (via `pentatrion/vite-bundle`) pour bénéficier de tout l'outillage (Hot Module Replacement, optimisation). AssetMapper brille surtout pour les applis "Symfony-first" avec Stimulus/Turbo.

## 4. Gestion des Images et Polices

### Webpack Encore
```javascript
// Copie les images de assets/images vers public/build/images
Encore.copyFiles({
    from: './assets/images',
    to: 'images/[path][name].[hash:8].[ext]',
});
```
En CSS : `background-image: url('../images/bg.png');` est automatiquement réécrit par Webpack.

### AssetMapper
Tout fichier dans `assets/images` est accessible.
En CSS : `url('logo.png')` fonctionne si `assets/images` est dans les paths mappés, mais il est préférable d'utiliser la fonction `asset()` dans Twig pour les balises `<img>`.

