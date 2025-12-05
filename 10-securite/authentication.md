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

## Types d'Authentification
*   **Stateful** (Session) : Classique pour le web (`form_login`). Le token est stocké en session.
*   **Stateless** (API) : Pas de session. Le token (JWT, Bearer) est envoyé à chaque requête.

## Le TokenStorage
Une fois authentifié, le token est stocké dans le service `TokenStorageInterface`.
C'est là que `getUser()` va chercher l'info.

```php
// Accès manuel
$token = $tokenStorage->getToken();
$user = $token?->getUser();
```

## 🧠 Concepts Clés
1.  **Lazy Firewall** : Par défaut, le firewall est "lazy". Il ne démarre la session et ne charge l'utilisateur que si votre code le demande (`is_granted`, `getUser`) ou si une règle `access_control` l'exige.
2.  **Entry Point** : Si un utilisateur anonyme essaie d'accéder à une page protégée, le `AuthenticationEntryPoint` (configuré dans le firewall) décide quoi faire (rediriger vers `/login` ou renvoyer 401).

## ⚠️ Points de vigilance (Certification)
*   **Events** : L'événement `SecurityEvents::INTERACTIVE_LOGIN` est déclenché lorsqu'un utilisateur se connecte "activement" (pas via "Remember Me" ou token pré-existant).
*   **Token** : Ne confondez pas le Token CSRF (Formulaire) et le Token de Sécurité (User session).

## Ressources
*   [Symfony Docs - Authentication](https://symfony.com/doc/current/security.html#authentication-identifying-the-user)
