# Authenticators, Passports et Badges

## Concept clé
Le système d'authentification de Symfony (Security 6+) repose sur un flux clair :
1.  Un **Authenticator** intercepte la requête.
2.  Il crée un **Passport** contenant des **Badges**.
3.  L'**AuthenticationManager** vérifie ("résout") chaque badge du Passport.
4.  Si tout est valide, un **Token** est créé.

## 1. Le Passport : Le Conteneur
Le `Passport` est un objet DTO (Data Transfer Object) qui transporte toutes les informations nécessaires à l'authentification. Il ne contient pas de logique métier, juste des données.

Il existe deux types principaux :
*   `Symfony\Component\Security\Http\Authenticator\Passport\Passport` : Nécessite obligatoirement un badge de type `Credentials` (ex: mot de passe).
*   `Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport` : Ne nécessite pas de credentials (ex: API Token, lien magique, Login via tiers).

### Passport Attributes
En plus des badges, vous pouvez stocker des attributs arbitraires dans le Passport pour les transmettre à la méthode `createToken()`.
```php
$passport->setAttribute('scope', ['read', 'write']);
```

## 2. Les Badges : Les briques de sécurité
Un **Badge** est une unité d'information de sécurité qui doit être validée.

### Badges Indispensables
*   **`UserBadge`** (Obligatoire) : Transporte l'identifiant (email, username). Il est responsable de charger l'objet `User`.
    *   *Nouveauté Symfony 7.3* : On peut passer un normalizer en 3ème argument pour nettoyer l'identifiant (ex: `strtolower`).
*   **`PasswordCredentials`** : Transporte le mot de passe en clair.
*   **`CustomCredentials`** : Permet de définir une logique de validation personnalisée via une Closure.

### Badges Optionnels (Features)
*   **`CsrfTokenBadge`** : Vérifie automatiquement un token CSRF.
*   **`RememberMeBadge`** : Active la fonctionnalité "Se souvenir de moi" (création du cookie).
*   **`PasswordUpgradeBadge`** : Met à jour le hash du mot de passe en base si nécessaire (automatique avec `PasswordCredentials`).

## 3. Fonctionnement Interne (La Résolution)
C'est ici que la magie opère. L'authentification est un pipeline d'événements.

1.  **Collecte** : L'`AuthenticatorManager` appelle `authenticate()` qui retourne un `Passport`.
2.  **Vérification (`CheckPassportEvent`)** : Le Manager dispatch cet événement.
    *   Des **Listeners** internes écoutent cet événement.
    *   Chaque listener cherche un badge spécifique dans le Passport.
    *   S'il le trouve, il le "résout" (le valide).

**Exemple de résolution :**
*   Le `UserProviderListener` voit un `UserBadge`. Il appelle la méthode `getUser()` du badge.
*   Le `CheckCredentialsListener` voit un `PasswordCredentials`. Il hash le mot de passe et le compare.
*   Le `CsrfProtectionListener` voit un `CsrfTokenBadge`. Il valide le token via le `CsrfTokenManager`.

Si un seul badge échoue, toute l'authentification échoue.

## 4. Exemples Complets

### A. Login Form (Standard)

```php
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->isMethod('POST') && $request->getPathInfo() === '/login';
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $csrfToken = $request->request->get('_csrf_token');

        // 1. UserBadge : Charge l'utilisateur
        $userBadge = new UserBadge($email, function($userIdentifier) {
            return $this->userRepository->findOneBy(['email' => $userIdentifier]);
        });

        // 2. Création du Passport avec Credentials
        return new Passport(
            $userBadge,
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }
    
    // ... onSuccess, onFailure
}
```

### B. API Token / JWT (SelfValidating)

Ici, pas de mot de passe à vérifier. La validité du token suffit.

```php
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class JwtAuthenticator extends AbstractAuthenticator
{
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization');
    }

    public function authenticate(Request $request): Passport
    {
        $token = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        
        if (!$token) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // Décodage du token
        $payload = $this->jwtManager->decode($token); 

        return new SelfValidatingPassport(
            new UserBadge($payload['username'])
        );
    }
    
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Invalid Token'], Response::HTTP_UNAUTHORIZED);
    }
}
```

## 5. Entry Point
Si votre authenticator doit aussi gérer le démarrage de l'authentification (ex: rediriger un utilisateur anonyme qui tente d'accéder à une page protégée), il doit implémenter `AuthenticationEntryPointInterface`.
La méthode `start()` retourne la réponse à envoyer (Redirect ou 401).

## 🧠 Concepts Clés
1.  **Séparation des responsabilités** : L'Authenticator *extrait* les données. Les Listeners *vérifient* les données.
2.  **Extensibilité** : Vous pouvez créer vos propres badges (ex: `IpAddressBadge`) et créer un Listener associé sur `CheckPassportEvent`.

## ⚠️ Points de vigilance (Certification)
*   **Ordre** : Le `UserBadge` est toujours résolu en premier.
*   **Exceptions** : Utilisez `CustomUserMessageAuthenticationException` pour les erreurs utilisateur.
*   **Maker** : Utilisez `php bin/console make:security:custom` pour générer le squelette.

## Ressources
*   [Symfony Docs - Custom Authenticator](https://symfony.com/doc/current/security/custom_authenticator.html)
