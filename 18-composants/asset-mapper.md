# Le Composant AssetMapper

Le composant **AssetMapper** (introduit dans Symfony 6.3) est une alternative moderne et simplifiée à Webpack Encore pour la gestion des assets JavaScript et CSS. Il repose sur les standards du web (**Import Maps**) pour charger les modules JavaScript directement dans le navigateur, sans étape de build complexe (pas de Node.js, pas de npm, pas de bundler).

## 1. Concepts Fondamentaux

### Import Maps
Au lieu de concaténer tous les fichiers JS en un seul gros fichier (bundling), AssetMapper génère une "Import Map" (JSON) dans la page HTML. Ce JSON indique au navigateur où trouver chaque module JS.
```html
<script type="importmap">
{
    "imports": {
        "app": "/assets/app-123hash.js",
        "bootstrap": "/assets/vendor/bootstrap-456hash.js"
    }
}
</script>
```

### Philosophie "No-Build"
*   **Pas de compilation** : Les fichiers sont servis tels quels (ou légèrement transpilés à la volée par PHP si besoin).
*   **Pas de node_modules** : Les dépendances tierces sont téléchargées dans un dossier local `assets/vendor` et committées dans le dépôt.
*   **HTTP/2** : Le chargement de multiples petits fichiers est performant grâce au multiplexing HTTP/2.

---

## 2. Installation et Configuration

```bash
composer require symfony/asset-mapper symfony/asset
php bin/console importmap:install
```

### Fichier `importmap.php`
Ce fichier (à la racine ou dans `config/`) définit les entrées JavaScript.

```php
return [
    // Point d'entrée de votre application
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    // Dépendances externes
    '@hotwired/stimulus' => [
        'version' => '3.2.1',
    ],
];
```

### Configuration `config/packages/asset_mapper.yaml`
```yaml
framework:
    asset_mapper:
        # Dossiers contenant les fichiers sources
        paths:
            - assets/
        
        # Pour les fichiers téléchargés (vendor)
        vendor_dir: '%kernel.project_dir%/assets/vendor'
```

---

## 3. Utilisation au Quotidien

### Dans les templates Twig
```twig
{# base.html.twig #}
<head>
    {# Génère la balise <script type="importmap"> et les preloads #}
    {{ importmap('app') }}
</head>
```

### Gestion des Dépendances
Plus de `npm install`. On utilise la console Symfony.

```bash
# Ajouter une librairie (télécharge dans assets/vendor et met à jour importmap.php)
php bin/console importmap:require bootstrap

# Mettre à jour une librairie
php bin/console importmap:update bootstrap

# Vérifier les failles de sécurité (audit)
php bin/console importmap:audit
```

### Gestion du CSS
AssetMapper gère aussi le CSS. Il suffit de l'importer dans le JS ou de le lier directement.
Depuis Symfony 6.4/7.0, l'import CSS dans le JS est supporté nativement par les navigateurs ou via un shim léger.

```javascript
// assets/app.js
import './styles/app.css'; 
```

---

## 4. Fonctionnalités Avancées

### Logical Paths & Namespaces
AssetMapper associe des fichiers physiques à des chemins logiques.
*   `assets/images/logo.png` -> `images/logo.png` (si `assets/` est mappé).
*   On peut utiliser `asset('images/logo.png')` dans Twig.

### Versioning & Cache Busting
AssetMapper calcule automatiquement un hash du contenu du fichier.
L'URL générée ressemble à `/assets/app-d41d8cd98f00b204e9800998ecf8427e.js`.
Si le fichier change, le hash change, forçant le navigateur à retélécharger.

### Assets Compilés (Sass, Tailwind)
Bien que "No-Build", AssetMapper peut être combiné avec `symfony/asset-mapper-compiler` ou des binaires PHP (comme `sassphp` ou `tailwindphp`) pour compiler du SCSS ou Tailwind CSS **avant** que AssetMapper ne les serve.

---

## 5. Points de vigilance pour la Certification

*   **Différence avec Webpack Encore** :
    *   **Webpack** : Node.js requis, bundling (1 gros fichier), complexité config, tree-shaking puissant. Idéal pour les SPAs lourdes (React, Vue).
    *   **AssetMapper** : PHP Only, HTTP/2, simplicité. Idéal pour les applis Symfony classiques (avec Stimulus/Turbo).
*   **Environnement de Prod** :
    *   En dév, les fichiers sont servis par PHP via un controller interne.
    *   En prod, il faut exécuter `php bin/console asset-map:compile` pour copier les assets finaux (avec hash) dans `public/assets/` afin qu'ils soient servis directement par le serveur web (Nginx/Apache).
*   **Shim** : AssetMapper inclut souvent "ES Module Shims" pour assurer la compatibilité des Import Maps avec les navigateurs plus anciens.

---

## 6. Le Composant Asset (Classique)
AssetMapper s'appuie sur le composant **Asset** historique.
*   Fonction `asset()` dans Twig.
*   Gère les **Packages** (CDN, URLs absolues).
*   Gère le **Versioning** (manifest.json ou query string `?v=...`).

```yaml
framework:
    assets:
        base_urls: ['https://cdn.example.com']
        version: '1.0.0' # Ou strategy: json_manifest_path
```
