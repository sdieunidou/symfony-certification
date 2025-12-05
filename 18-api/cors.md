# CORS (Cross-Origin Resource Sharing)

## Le Problème
Par sécurité, les navigateurs bloquent les requêtes AJAX/Fetch vers un domaine différent de celui qui sert la page.
Exemple :
*   Front React sur `http://localhost:3000`
*   API Symfony sur `http://localhost:8000`

Si le Front appelle l'API, le navigateur bloque la réponse (Same-Origin Policy).

## La Solution : En-têtes CORS
Le serveur (API) doit dire explicitement au navigateur : "J'accepte les requêtes venant de localhost:3000".

### Headers principaux
*   `Access-Control-Allow-Origin`: `http://localhost:3000` (ou `*` pour public, mais risqué avec auth).
*   `Access-Control-Allow-Methods`: `GET, POST, PUT, DELETE, OPTIONS`.
*   `Access-Control-Allow-Headers`: `Content-Type, Authorization`.

### Requêtes Preflight (OPTIONS)
Pour les requêtes "complexes" (celles qui ont des headers custom comme `Authorization` ou un Content-Type `application/json`), le navigateur envoie d'abord une requête HTTP **OPTIONS** pour demander la permission.
Si le serveur répond 200 OK avec les bons headers CORS, alors le navigateur envoie la vraie requête (POST, GET...).

**L'API doit donc répondre aux requêtes OPTIONS sur toutes les routes, sans authentification.**

## NelmioCorsBundle
C'est le standard de fait pour gérer CORS dans Symfony. Il automatise l'ajout des headers et la gestion des requêtes OPTIONS.

```yaml
# config/packages/nelmio_cors.yaml
nelmio_cors:
    defaults:
        allow_origin: ['%env(CORS_ALLOW_ORIGIN)%']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
    paths:
        '^/api/':
            allow_origin: ['*']
            allow_headers: ['*']
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE']
            max_age: 3600
```

## 🧠 Concepts Clés
1.  **Sécurité Navigateur** : CORS est une sécurité *côté navigateur*. Un appel via Curl ou Postman n'est jamais bloqué par CORS.
2.  **Wildcard (*)** : `Access-Control-Allow-Origin: *` est incompatible avec `Access-Control-Allow-Credentials: true` (cookies/auth).

## Ressources
*   [MDN - CORS](https://developer.mozilla.org/fr/docs/Web/HTTP/CORS)
*   [NelmioCorsBundle](https://github.com/nelmio/NelmioCorsBundle)
