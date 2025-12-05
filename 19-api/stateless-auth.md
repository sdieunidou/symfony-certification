# Authentification Stateless & Tokens

## Le concept Stateless
Dans une API REST pure, le serveur ne garde aucun état client (`Session`).
Chaque requête doit s'authentifier elle-même, généralement via un **Token** envoyé dans les en-têtes HTTP.

```http
GET /api/profile HTTP/1.1
Authorization: Bearer mF_9.B5f-4.1JqM
```

## Authenticator System (Symfony Security)

Pour gérer cela, on utilise le système d'Authenticator de Symfony.

### 1. Access Token Authenticator (Symfony 6.2+)
Symfony fournit désormais un extracteur de token natif simplifiant grandement la tâche.

```php
// config/packages/security.yaml
security:
    firewalls:
        api:
            pattern: ^/api
            stateless: true # Désactive la session PHP
            access_token:
                token_handler: App\Security\ApiTokenHandler
```

Il suffit ensuite de créer le Handler qui vérifie le token :

```php
// src/Security/ApiTokenHandler.php
namespace App\Security;

use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

class ApiTokenHandler implements AccessTokenHandlerInterface
{
    public function getUserBadgeFrom(string $accessToken): UserBadge
    {
        // 1. Vérifier le token (Base de données, Redis, décodage JWT...)
        $tokenData = $this->tokenRepository->find($accessToken);

        if (!$tokenData) {
            throw new BadCredentialsException();
        }

        // 2. Retourner le UserBadge (identifiant utilisateur)
        return new UserBadge($tokenData->getUserIdentifier());
    }
}
```

### 2. JSON Web Tokens (JWT)
Symfony ne gère pas les JWT nativement dans le Core (il a besoin d'une lib crypto).
Le standard de facto est le bundle **LexikJWTAuthenticationBundle**.

*   **Principe** : Le token contient les infos (payload) signées crypto. Le serveur n'a pas besoin de vérifier en BDD à chaque requête, il vérifie juste la signature.
*   **Avantage** : Performance (Stateless réel, pas d'appel DB).
*   **Inconvénient** : Révocation difficile (nécessite liste noire ou expiration courte).

### 3. API Keys
Pour les communications Machine-à-Machine (M2M).
Souvent passée via un header personnalisé `X-API-KEY`.
Symfony supporte cela via le système `Custom Authenticator` ou le `access_token` configuré pour lire un header spécifique.

## User Provider
Même en API, le `UserProvider` reste nécessaire pour recharger l'utilisateur complet (Rôles, Données) à partir de l'identifiant extrait du token.

## 🧠 Concepts Clés
1.  **Stateless: true** : Directive firewall cruciale. Elle dit à Symfony de ne jamais essayer de lire ou écrire un cookie de session `PHPSESSID`.
2.  **CORS** : Si votre API est appelée par un navigateur (JS), l'authentification ne marchera pas si les headers CORS ne sont pas configurés pour autoriser le header `Authorization`.

## Ressources
*   [Symfony Docs - Access Token Authentication](https://symfony.com/doc/current/security/access_token.html)
*   [Symfony Docs - Custom Authenticators](https://symfony.com/doc/current/security/custom_authenticator.html)
