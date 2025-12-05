# WebLink & Performance Frontend

La performance frontend est cruciale. Symfony intègre le composant **WebLink** pour exploiter les fonctionnalités modernes de HTTP et du navigateur comme le "Resource Hinting" et le "Preloading".

## 1. Composant WebLink

Le composant `symfony/web-link` gère les en-têtes HTTP `Link` définis dans la spécification **PSR-13**.

### Installation
```bash
composer require symfony/web-link
```

### Resource Hints (Indices de Ressources)

Ces indices indiquent au navigateur de charger des ressources avant même qu'il n'en ait besoin dans le parsing HTML.

*   **Preload** (`rel="preload"`) : "J'ai besoin de cette ressource *tout de suite* pour la page courante." (ex: police critique, CSS principal, script de démarrage).
*   **Prefetch** (`rel="prefetch"`) : "J'aurai probablement besoin de cette ressource pour la *page suivante*." (Le navigateur la charge en tâche de fond).
*   **Prerender** : Charge et rend une page entière en arrière-plan.

### Utilisation dans Symfony

Symfony peut ajouter ces en-têtes automatiquement via Twig ou PHP.

**Dans Twig (recommandé) :**
```twig
{# Indique au navigateur de précharger ce fichier CSS #}
<link rel="stylesheet" href="{{ preload(asset('build/app.css')) }}">

{# Indique de précharger une police #}
<link rel="preload" href="{{ asset('fonts/main.woff2') }}" as="font" crossorigin>
```

Le helper `preload()` ajoute automatiquement l'en-tête HTTP `Link` à la réponse.

**En PHP (Controller) :**
```php
use Symfony\Component\WebLink\Link;

public function index(Request $request)
{
    // Ajout manuel de l'en-tête Link
    $request->attributes->get('_links')->add(new Link('preload', '/assets/app.css'));
    
    return $this->render('index.html.twig');
}
```

## 2. HTTP/2 et Server Push

L'en-tête `Link` est historiquement utilisé pour déclencher le **HTTP/2 Server Push**. Si le serveur web/proxy le supporte, il envoie la ressource au client *avant* même que le client ne reçoive le HTML et ne la demande.

*Note : Le Server Push est déprécié dans Chrome et de moins en moins utilisé au profit du "103 Early Hints".*

## 3. 103 Early Hints

Symfony 6.3+ supporte les réponses **103 Early Hints**.
C'est une réponse HTTP intermédiaire envoyée *pendant* que le serveur (PHP) calcule encore la réponse finale (HTML). Elle contient les en-têtes `Link` pour que le navigateur commence à télécharger les assets (CSS/JS) immédiatement, sans attendre que le HTML soit prêt.

Pour l'activer, il faut un serveur compatible (FrankenPHP, ou configuration spécifique Nginx/Apache).

## 4. Optimisations avec AssetMapper

AssetMapper utilise nativement le preloading pour les modules JavaScript.
Lorsque vous faites `{{ importmap('app') }}` :

1.  Il génère la balise `<script type="importmap">`.
2.  Il génère automatiquement des balises `<link rel="modulepreload">` pour tous les fichiers JS nécessaires au démarrage immédiat de l'application 'app'.

Cela évite l'effet de "waterfall" (cascade) où le navigateur doit télécharger un fichier JS pour découvrir qu'il en importe un autre, etc.

## 5. Bonnes Pratiques de Performance

1.  **Minification** : Toujours activer en production (natif Webpack Encore, nécessite `symfony/asset-mapper` pour AssetMapper).
2.  **Compression** : Configurer le serveur web (Nginx/Apache) pour servir les assets en Gzip ou Brotli.
3.  **Cache Long** : Les fichiers versionnés (hash dans le nom) doivent avoir des en-têtes de cache longs (`Cache-Control: max-age=31536000, immutable`).
4.  **Images** : Utiliser des formats modernes (WebP, AVIF) et le Lazy Loading (`<img loading="lazy">`).

