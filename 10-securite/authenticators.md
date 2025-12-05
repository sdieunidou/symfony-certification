# Authenticators, Passports et Badges

## Concept clé
Pour créer un système de login personnalisé (ex: Login par lien magique, Auth via Header spécifique), on crée un **Authenticator**.
Il retourne un **Passport** qui contient :
1.  L'**UserBadge** (Qui est l'utilisateur ?).
2.  Des **Credentials** (Mot de passe ou Token).
3.  Des **Badges** optionnels (CSRF, RememberMe).

## Structure d'un Authenticator

```php
namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiTokenAuthenticator extends AbstractAuthenticator
{
    // 1. Est-ce que cet authenticator s'applique à la requête ?
    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-API-TOKEN');
    }

    // 2. Extraire les infos et créer le Passport
    public function authenticate(Request $request): Passport
    {
        $apiToken = $request->headers->get('X-API-TOKEN');

        if (null === $apiToken) {
            throw new CustomUserMessageAuthenticationException('No API token provided');
        }

        // SelfValidatingPassport = Pas de mot de passe à vérifier (le token suffit)
        // UserBadge = Va appeler le UserProvider pour charger l'user avec cet identifiant
        return new SelfValidatingPassport(new UserBadge($apiToken));
    }

    // 3. Succès
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // Laisse la requête continuer vers le contrôleur
    }

    // 4. Échec
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(['error' => 'Auth Failed'], Response::HTTP_UNAUTHORIZED);
    }
}
```

## Les Badges (Sécurité Modulaire)
*   `UserBadge($identifier, $loader)` : Charge l'utilisateur.
*   `PasswordCredentials($password)` : Vérifie le mot de passe (automatique via PasswordHasher).
*   `CsrfTokenBadge($id, $token)` : Vérifie le jeton CSRF.
*   `RememberMeBadge` : Active le cookie de persistance.

## Événements de Sécurité (Security Events)
Le processus d'authentification dispatch plusieurs événements auxquels vous pouvez vous abonner :

1.  `CheckPassportEvent` : Après la création du Passport. Pour validations custom (ex: IP ban).
2.  `AuthenticationTokenCreatedEvent` : Après validation du Passport, quand le Token est créé.
3.  `AuthenticationSuccessEvent` : Juste avant le succès final.
4.  `LoginSuccessEvent` : Après succès total. Permet de modifier la Réponse (ex: ajouter un cookie).
5.  `LoginFailureEvent` : En cas d'erreur.
6.  `LogoutEvent` : Lors de la déconnexion.

## 🧠 Concepts Clés
1.  **AbstractAuthenticator** : Classe de base pour les auth custom (API).
2.  **InteractiveAuthenticatorInterface** : Interface marqueur. Si implémentée, `INTERACTIVE_LOGIN` est dispatché (pour le login form). Souvent inutile pour les APIs stateless.

## ⚠️ Points de vigilance (Certification)
*   **Passport** : C'est la grande nouveauté de Symfony 5/6. Il sépare l'extraction des données de leur vérification. Le `UserBadge` résout l'utilisateur, le `PasswordCredentials` résout le password check.
*   **Registration** : Pour utiliser votre authenticator custom, enregistrez-le dans `security.yaml` sous `firewalls.main.custom_authenticator`.

## Ressources
*   [Symfony Docs - Custom Authenticator](https://symfony.com/doc/current/security/custom_authenticator.html)
