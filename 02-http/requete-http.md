# Requête HTTP (Request)

## Concept clé
L'objet `Request` est une représentation orientée objet de la requête HTTP entrante et de l'environnement serveur. Il encapsule les superglobales PHP (`$_GET`, `$_POST`, etc.) qui ne doivent **jamais** être utilisées directement dans Symfony.

## Anatomie de l'objet Request

L'objet contient plusieurs "Sacs" (ParameterBags) publics :
1.  `$request->query` (`$_GET`) : Paramètres d'URL.
2.  `$request->request` (`$_POST`) : Paramètres du corps de requête (Formulaire).
3.  `$request->attributes` : **Spécifique Symfony**. Stocke les résultats du Routing (`_route`, `id`), de la Sécurité, et vos données custom.
4.  `$request->cookies` (`$_COOKIE`).
5.  `$request->files` (`$_FILES`) : Objets `UploadedFile`.
6.  `$request->server` (`$_SERVER`) et `$request->headers`.

## Trusted Proxies (Indispensable en Prod)
Dans une architecture moderne, Symfony est souvent derrière un Load Balancer (AWS ELB, Cloudflare, Nginx Reverse Proxy).
La requête arrive à Symfony depuis l'IP du Proxy (ex: `10.0.0.1`), pas du client réel.
Le Proxy transmet l'IP réelle via des headers (`X-Forwarded-For`, `X-Forwarded-Proto`).

Si vous ne configurez pas les **Trusted Proxies**, `$request->getClientIp()` renverra l'IP du proxy, et `$request->isSecure()` (HTTPS) renverra false.

```php
// config/packages/framework.yaml
framework:
    # Faire confiance à tous les proxies (si dans conteneur isolé) ou liste d'IPs
    trusted_proxies: '127.0.0.1,10.0.0.0/8' 
    trusted_headers: ['x-forwarded-for', 'x-forwarded-proto', ...]
```

## InputBag et Typage (PHP 8)
Depuis Symfony 5/6, `query`, `request` et `cookies` sont des `InputBag`. Ils permettent de récupérer des valeurs typées, ce qui est plus sûr.

```php
// Récupère un entier (transtypage auto). Renvoie 1 par défaut.
$page = $request->query->getInt('page', 1);

// Récupère une string (force le type)
$name = $request->request->getString('name');

// Récupère un booléen
$isAjax = $request->query->getBoolean('ajax');

// Récupère un Enum (Symfony 6.3+ / PHP 8.1)
$status = $request->query->getEnum('status', App\Enum\Status::class);
```

## Formats et Contenu Brut
Pour les APIs JSON, les données ne sont pas dans `$_POST`. Elles sont dans le corps brut.

```php
// Lire le JSON brut
$content = $request->getContent();

// Helper Symfony (convertit JSON en Array)
// Lance une Exception si JSON invalide
$data = $request->toArray(); 
```

## 🧠 Concepts Clés
1.  **Immutabilité** : L'objet Request est mutable (on peut modifier les attributes), mais il est conceptuellement préférable de le traiter comme immuable.
2.  **Host Matching** : On peut récupérer le host (`$request->getHost()`) pour faire du routing par sous-domaine.
3.  **Request Format** : `$request->getRequestFormat()` déduit le format (json, html) de l'extension d'URL ou du header Accept (voir Négociation de Contenu).

## ⚠️ Points de vigilance (Certification)
*   **Paramètres vs Attributs** : Ne confondez pas `$request->query->get('id')` (le `?id=1` dans l'URL) et `$request->attributes->get('id')` (le `{id}` de la route `/product/{id}`). Le Routing remplit `attributes`.
*   **Override Globals** : `Request::createFromGlobals()` initialise la requête. Symfony le fait dans `public/index.php`. Mais Symfony ne *modifie pas* les globales PHP (contrairement à certaines vieilles librairies).
*   **Session** : La session n'est pas un Bag direct de la Request. On y accède via `$request->getSession()`. Attention : cela démarre la session si elle ne l'est pas. Si vous êtes en API Stateless (JWT), n'appelez jamais `getSession()`.

## Ressources
*   [Symfony Docs - Request](https://symfony.com/doc/current/components/http_foundation.html#request)
*   [Trusted Proxies Configuration](https://symfony.com/doc/current/deployment/proxies.html)
