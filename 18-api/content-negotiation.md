# Content Negotiation & Versioning

## Négociation de Contenu (Content Negotiation)
C'est le mécanisme par lequel le client et le serveur s'accordent sur le format de la ressource.

### Le Header `Accept`
Le client envoie ce qu'il souhaite recevoir :
`Accept: application/json, application/xml;q=0.9`

Le serveur (Symfony) lit ce header et choisit le meilleur format via le Serializer.

### Le Header `Content-Type`
Indique le format des données envoyées dans le corps de la requête (Body).
`Content-Type: application/json`

### Dans Symfony
Les méthodes `json()` ou le `MapRequestPayload` gèrent cela en partie.
Pour une gestion avancée (ex: une même route qui rend du HTML ou du JSON selon l'appel), on peut utiliser le **Format Listener** (disponible via FOSRestBundle ou API Platform, moins natif dans le core).
Nativement, on peut inspecter la requête :

```php
$format = $request->getPreferredFormat(); // json, xml, html...
```

## Versioning d'API
Les APIs évoluent. Pour ne pas casser les clients existants (Breaking Changes), on versionne.

### Stratégies
1.  **URI Versioning** (Le plus courant)
    *   `/api/v1/users`
    *   `/api/v2/users`
    *   *Avantage* : Simple, explicite, facile à tester/cacher.
    *   *Inconvénient* : "Pollution" des URLs, pas sémantiquement REST pur.
2.  **Header Custom**
    *   `X-API-VERSION: 1`
    *   *Avantage* : URLs propres.
    *   *Inconvénient* : Plus dur à tester dans un navigateur, Cache Vary nécessaire.
3.  **Media Type Versioning** (Le plus REST)
    *   `Accept: application/vnd.mycompany.v1+json`
    *   *Avantage* : Très granulaire.
    *   *Inconvénient* : Complexe à gérer pour les clients.

### Implémentation Symfony
L'URI Versioning est le plus simple à mettre en place via le Routing.

```yaml
# config/routes/api_v1.yaml
api_v1:
    resource: '../../src/Controller/Api/V1/'
    type: annotation
    prefix: /api/v1
```

## 🧠 Concepts Clés
1.  **Backward Compatibility (BC)** : La règle d'or est de ne jamais casser un client existant. Si vous changez un nom de champ, créez une v2 ou supportez les deux noms temporairement.
2.  **Deprecation** : Utilisez le header `Warning` ou un champ custom pour prévenir les clients qu'une route va disparaître.

## Ressources
*   [Symfony Routing Prefixes](https://symfony.com/doc/current/routing.html#route-groups-prefixes)
