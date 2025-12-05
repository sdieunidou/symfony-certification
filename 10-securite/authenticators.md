# Authenticators, Passports et Badges

## Concept clé
Depuis Symfony 6, le système "Authenticator" remplace l'ancien système "Guard".
Il repose sur 3 concepts :
1.  **Authenticator** : Extrait les credentials et crée un Passport.
2.  **Passport** : Contient l'User et les "Badges" de sécurité.
3.  **Badge** : Une contrainte de sécurité (ex: Password valide, CSRF valide, Email confirmé, RememberMe activé).

## Application dans Symfony 7.0 (Custom Authenticator)

```php
class ApiKeyAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-TOKEN');
    }

    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-AUTH-TOKEN');
        
        return new SelfValidatingPassport(
            new UserBadge($apiToken) // Trouve l'user par ce token
        );
    }

    public function onAuthenticationSuccess(...): ?Response { return null; } // Continue la requête
    public function onAuthenticationFailure(...): ?Response { ... }
}
```

### Badges standards
*   `UserBadge` (Obligatoire) : Identifie l'utilisateur.
*   `PasswordCredentials` : Vérifie le mot de passe.
*   `CsrfTokenBadge` : Vérifie le token CSRF.
*   `RememberMeBadge` : Active le cookie "Se souvenir de moi".

## Points de vigilance (Certification)
*   **Passport** : L'avantage du Passport est que le contrôleur de sécurité (Symfony) s'occupe de vérifier tous les badges. Votre authenticator n'a plus à vérifier le mot de passe ou le CSRF manuellement, il ajoute juste le badge correspondant au Passport.
*   **SelfValidatingPassport** : Utilisé quand il n'y a pas de "credentials" à vérifier (ex: API Token, login magique).

## Ressources
*   [Symfony Docs - Custom Authenticator](https://symfony.com/doc/current/security/custom_authenticator.html)

