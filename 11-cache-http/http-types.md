# Types de Cache HTTP (Privé vs Partagé)

## Concept clé
Il est crucial de distinguer **qui** a le droit de cacher une réponse.
Mélanger cache privé et public est une faille de sécurité majeure (Information Leakage).

## 1. Cache Privé (`private`)
*   **Cible** : Navigateur de l'utilisateur final uniquement.
*   **Contenu** : Données personnalisées (Mon Compte, Panier, Page avec "Bonjour Pierre").
*   **Comportement** : Les proxys intermédiaires (Varnish, CDN, Proxy d'entreprise) **NE DOIVENT PAS** stocker cette réponse.

## 2. Cache Partagé (`public`)
*   **Cible** : Tout le monde (Proxies, CDNs, et Navigateurs).
*   **Contenu** : Données génériques identiques pour tous (Homepage, Liste produits, Assets, Articles de blog publics).
*   **Comportement** : Une seule copie stockée sur le CDN peut servir 1 million d'utilisateurs.

## Application dans Symfony 7.0

```php
// Explicitement public
$response->setPublic();

// Explicitement privé
$response->setPrivate();
```

## Règles de Sécurité Symfony
Par défaut, Symfony est paranoïaque (Secure by default).
Si la réponse dépend de données utilisateur (Session active, Cookie), Symfony ajoute automatiquement `Cache-Control: private, must-revalidate`.
Pour rendre une page publique alors qu'une session est active (ex: un blog public même si je suis loggué), vous devez appeler `$response->setPublic()` explicitement. Mais attention : assurez-vous que la page ne contient **aucune** donnée utilisateur (pas de "Bonjour Pierre" dans le header). Si vous avez besoin des deux, utilisez **ESI** ou le chargement AJAX pour la partie utilisateur.

## 🧠 Concepts Clés
1.  **Shared Max Age** : `s-maxage` ne s'applique qu'aux caches **publics** (partagés).
2.  **Authentification** : Une réponse avec un header `Authorization` est automatiquement considérée comme `private` par les standards HTTP, sauf si `public` est forcé (et `s-maxage` défini).

## ⚠️ Points de vigilance (Certification)
*   **Vary: Cookie** : Si vous rendez une page publique qui varie selon les cookies (session), vous tuez le cache (chaque ID de session crée une entrée de cache différente). C'est une attaque DoS facile. Ne jamais mettre `Vary: Cookie` sur une réponse publique.

## Ressources
*   [Symfony Docs - Cache Types](https://symfony.com/doc/current/http_cache.html#types-of-caches)
*   [RFC 7234 - Cache-Control](https://tools.ietf.org/html/rfc7234#section-5.2.2)
