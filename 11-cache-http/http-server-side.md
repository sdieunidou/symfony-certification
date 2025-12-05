# Mise en cache Server-side (Reverse Proxy / Gateway)

## Concept clé
Un **Gateway Cache** (ou Reverse Proxy) est un serveur intermédiaire situé devant votre application. Il intercepte les requêtes et sert les réponses cachées à la place de votre application PHP.
C'est la clé pour scaler à des millions de requêtes.

## Solutions Supportées

### 1. Symfony HttpCache (PHP)
Symfony fournit un Reverse Proxy écrit en PHP pur (`Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache`).
*   **Avantage** : Zéro infrastructure. Fonctionne partout (même hébergement mutualisé). Facile à débugger. Supporte ESI.
*   **Inconvénient** : Moins performant que Varnish (car boot PHP), mais bien plus rapide que l'appli complète (ne boot pas le Kernel complet).
*   **Mise en place** : Modifier `public/index.php`.

```php
// public/index.php
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

// Envelopper le kernel avec le Cache
if ('prod' === $kernel->getEnvironment()) {
    $kernel = new HttpCache($kernel);
}
```

### 2. Varnish / Nginx (Infrastructure)
Solutions logicielles dédiées (C/C++).
*   **Avantage** : Performance extrême.
*   **Inconvénient** : Configuration complexe (VCL), nécessite accès root.

### 3. CDN (Cloudflare, Fastly, AWS CloudFront)
Caches distribués géographiquement.

## Interaction avec Symfony
Symfony est "Http Cache aware". Il suffit de retourner les bons headers (`Cache-Control: public, s-maxage=...`) et le Reverse Proxy (quel qu'il soit) obéira.

## Invalidation
Le problème difficile. Le modèle d'expiration ne permet pas de purger le cache instantanément.
Pour purger un Reverse Proxy (ex: après modification d'un article), il faut envoyer une requête HTTP spéciale (`PURGE /article/1`).
*   Symfony ne gère pas ça nativement.
*   Utilisez `FOSHttpCacheBundle` pour gérer l'invalidation (Ban/Purge) de Varnish/SymfonyHttpCache.

## 🧠 Concepts Clés
1.  **X-Symfony-Cache** : Header ajouté par `HttpCache` (en debug) pour indiquer si la réponse vient du cache (`HIT`), a été générée (`MISS`) ou validée (`FRESH`).
2.  **Store** : `HttpCache` stocke ses fichiers dans `var/cache/prod/http_cache`.

## ⚠️ Points de vigilance (Certification)
*   **IP Client** : Derrière un reverse proxy, `REMOTE_ADDR` est l'IP du proxy. Il faut configurer les **Trusted Proxies** pour que Symfony lise `X-Forwarded-For` et récupère la vraie IP client.

## Ressources
*   [Symfony Docs - Reverse Proxy](https://symfony.com/doc/current/http_cache.html#symfony-reverse-proxy)
