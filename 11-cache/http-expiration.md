# Modèle d'Expiration

## Concept clé
Le modèle d'expiration est la forme la plus simple de cache HTTP. Le serveur déclare : **"Cette ressource est fraîche pendant X secondes"**.
Tant que le délai n'est pas écoulé, le client (ou proxy) utilise sa copie locale **sans contacter le serveur**.

## En-têtes HTTP (RFC 7234)

### 1. `Cache-Control` (Le Standard)
C'est l'en-tête maître qui contient plusieurs directives.

*   **`max-age=3600`** : Durée de vie en secondes (Cache Privé et Partagé).
*   **`s-maxage=3600`** : Durée de vie pour le cache **Shared** (Partagé) uniquement. Si présent, les proxys l'utilisent et ignorent `max-age`.
*   **`public`** : Autorise le cache partagé (même avec Auth).
*   **`private`** : Interdit le cache partagé.
*   **`no-cache`** : Le cache doit **valider** (ETag) la réponse avec le serveur avant de la servir (ne veut pas dire "pas de cache").
*   **`no-store`** : Interdiction totale de stocker la réponse (Données sensibles).
*   **`must-revalidate`** : Une fois expiré, le cache ne doit JAMAIS servir une réponse périmée (stale) même si le serveur est injoignable.
*   **`immutable`** : Le contenu ne changera jamais pendant sa durée de vie (ex: assets versionnés). Évite les revalidations lors du "Refresh".

### 2. `Expires`
Date d'expiration absolue (Legacy HTTP 1.0). Ignoré si `Cache-Control: max-age` est présent.

## Application dans Symfony 7.0

### Via l'Attribut `#[Cache]` (Recommandé)
```php
use Symfony\Component\HttpKernel\Attribute\Cache;

#[Cache(public: true, maxage: 3600, mustRevalidate: true)]
public function index(): Response
{
    return $this->render('blog/index.html.twig');
}
```

### Via l'objet `Response`
```php
$response->setPublic();
$response->setMaxAge(3600);
$response->setSharedMaxAge(7200); // 2h sur le CDN, 1h sur le navigateur

// Directives avancées
$response->headers->addCacheControlDirective('must-revalidate', true);
$response->setImmutable();
```

## Safe Methods
Le cache HTTP ne fonctionne que pour les méthodes "Safe" : **GET** et **HEAD**.
POST, PUT, DELETE ne sont jamais cachés par défaut (et invalident souvent le cache).

## 🧠 Concepts Clés
1.  **Priorité** : Les headers définis dans le contrôleur écrasent ceux de l'attribut `#[Cache]`.
2.  **Calcul de l'âge** : L'âge est calculé par rapport à la date de génération (`Date` header).

## ⚠️ Points de vigilance (Certification)
*   **Invalidation** : Le modèle d'expiration ne permet PAS d'invalider le cache navigateur avant la fin du délai. C'est pourquoi on utilise souvent des URLs versionnées pour les assets.
*   **Horloge (NTP)** : L'expiration repose sur l'heure du serveur. Une désynchronisation de l'horloge peut entraîner des comportements imprévisibles (contenu expiré instantanément ou valide trop longtemps).

## Ressources
*   [Symfony Docs - Expiration](https://symfony.com/doc/current/http_cache/expiration.html)
