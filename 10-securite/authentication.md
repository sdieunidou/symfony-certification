# Authentification (AuthN)

## Concept clé
L'authentification est le processus qui vérifie l'identité d'un utilisateur (Credentials -> User).
Depuis Symfony 6, tout repose sur le système **Authenticator Manager**.

## Le Flux d'Authentification
1.  **Request** : L'utilisateur envoie des données (Formulaire login, Header API).
2.  **Authenticator** :
    *   `supports($request)` : "Est-ce que je sais gérer cette requête ?"
    *   `authenticate($request)` : "Voici les credentials (Passport)".
3.  **AuthenticatorManager** :
    *   Vérifie les Badges du Passport (CSRF, Password, User existant).
    *   Si succès -> Crée un `TokenAuthenticated`.
    *   Si échec -> Lance `AuthenticationException`.
4.  **Authenticator (Post-Auth)** :
    *   `onAuthenticationSuccess` : Redirection, Génération JWT.
    *   `onAuthenticationFailure` : Affichage erreur, 401.

## Login Programmatique (Manuel)
Parfois, vous voulez connecter un utilisateur manuellement (ex: après l'inscription, sans qu'il ressaisisse son mot de passe).

```php
use Symfony\Bundle\SecurityBundle\Security;

public function register(Security $security, User $user): Response
{
    // ... création user ...
    
    // Connecter l'utilisateur manuellement
    // login(UserInterface $user, ?string $authenticatorName = null, ?string $firewallName = null)
    $security->login($user, 'form_login'); 
    
    return $this->redirectToRoute('home');
}
```

## Logout Programmatique
```php
public function someAction(Security $security): Response
{
    // Déconnecter l'utilisateur courant
    $security->logout(false); // false = désactiver la validation CSRF pour cet appel
}
```

## Limiter les tentatives de Login (Throttling)
Pour prévenir les attaques brute-force, Symfony intègre nativement le composant `RateLimiter`.

### Configuration
```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            login_throttling:
                max_attempts: 3          # 3 essais
                interval: '15 minutes'   # Bloqué pendant 15 min
```
Par défaut, cela bloque par IP + Username (5 essais) et par IP (50 essais).
Vous pouvez personnaliser le limiteur en créant votre propre service `RateLimiter`.

## Types d'Authentification
*   **Stateful** (Session) : Classique pour le web (`form_login`). Le token est stocké en session.
*   **Stateless** (API) : Pas de session. Le token (JWT, Bearer) est envoyé à chaque requête.

## 🧠 Concepts Clés
1.  **Lazy Firewall** : Par défaut, le firewall est "lazy". Il ne démarre la session et ne charge l'utilisateur que si votre code le demande (`is_granted`, `getUser`) ou si une règle `access_control` l'exige.
2.  **Entry Point** : Si un utilisateur anonyme essaie d'accéder à une page protégée, le `AuthenticationEntryPoint` (configuré dans le firewall) décide quoi faire (rediriger vers `/login` ou renvoyer 401).

## ⚠️ Points de vigilance (Certification)
*   **Events** : L'événement `SecurityEvents::INTERACTIVE_LOGIN` est déclenché lorsqu'un utilisateur se connecte "activement" (pas via "Remember Me" ou token pré-existant).
*   **Token** : Ne confondez pas le Token CSRF (Formulaire) et le Token de Sécurité (User session).

## Ressources
*   [Symfony Docs - Authentication](https://symfony.com/doc/current/security.html#authentication-identifying-the-user)
