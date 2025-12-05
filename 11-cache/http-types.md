# Types de Cache HTTP (Gateway vs Proxy)

## Concept clé
Il existe deux types principaux de caches dans l'architecture HTTP :
1.  **Cache Navigateur (Client-side)** : Stocké localement chez l'utilisateur (Privé).
2.  **Gateway Cache (Reverse Proxy)** : Stocké sur un serveur intermédiaire (Partagé).

## 1. Cache Privé (`private`)
*   **Cible** : Navigateur de l'utilisateur final uniquement.
*   **Contenu** : Données personnalisées (Mon Compte, Panier, Page avec "Bonjour Pierre").
*   **Comportement** : Les proxys intermédiaires (Varnish, CDN, Proxy d'entreprise) **NE DOIVENT PAS** stocker cette réponse.

## 2. Cache Partagé (`public`) / Gateway Cache
*   **Cible** : Tout le monde (Proxies, CDNs, et Navigateurs).
*   **Contenu** : Données génériques identiques pour tous (Homepage, Liste produits, Assets, Articles de blog publics).
*   **Rôle** : Le "Gateway Cache" (ou Reverse Proxy) agit comme un intermédiaire. Il intercepte les requêtes entrantes. Si une réponse valide est en cache, il la renvoie sans solliciter l'application.
*   **Exemples** : Varnish, Symfony Reverse Proxy, Nginx, CDN (Cloudflare).

### Symfony Reverse Proxy
Symfony inclut un Reverse Proxy écrit en PHP. Moins performant que Varnish (écrit en C), il est idéal pour le développement ou les hébergements mutualisés.

**Activation (`framework.yaml`) :**
```yaml
when@prod:
    framework:
        http_cache: true
```

Ce proxy gère le caching, l'invalidation conditionnelle, et les ESI (Edge Side Includes).

**Debug :**
En mode debug, Symfony ajoute le header `X-Symfony-Cache` pour tracer les HIT/MISS.
On peut configurer le niveau de trace via `trace_level` (`none`, `short`, `full`).

## Application dans Symfony 7.0

```php
// Explicitement public (Cache partagé autorisé)
$response->setPublic();

// Explicitement privé (Uniquement navigateur)
$response->setPrivate();
```

## Règles de Sécurité Symfony
Par défaut, Symfony est "Secure by default".
Si la réponse dépend de données utilisateur (Session active, Cookie), Symfony ajoute automatiquement `Cache-Control: private, must-revalidate`.
Pour rendre une page publique alors qu'une session est active, appelez `$response->setPublic()`. Assurez-vous alors qu'aucune donnée personnelle n'est affichée.

## 🧠 Concepts Clés
1.  **Shared Max Age** : `s-maxage` ne s'applique qu'aux caches **publics** (partagés).
2.  **Authentification** : Une réponse avec un header `Authorization` est automatiquement considérée comme `private` par les standards HTTP, sauf si `public` est forcé.

## ⚠️ Points de vigilance (Certification)
*   **Vary: Cookie** : Si vous rendez une page publique qui varie selon les cookies, vous tuez le cache. Ne jamais mettre `Vary: Cookie` sur une réponse publique.

## Ressources
*   [Symfony Docs - HTTP Cache](https://symfony.com/doc/current/http_cache.html)
*   [RFC 7234 - Cache-Control](https://tools.ietf.org/html/rfc7234#section-5.2.2)
