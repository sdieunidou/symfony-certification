# Sécurité : Fonctionnement Interne

## Concept clé
Le composant Security de Symfony sépare l'**Authentification** (Qui êtes-vous ?) de l'**Autorisation** (Avez-vous le droit ?). Tout repose sur le **Token** stocké dans le **TokenStorage**.

## Architecture et Classes Clés

### 1. TokenStorage (`TokenStorageInterface`)
Le cœur du système. C'est un service qui stocke le `TokenInterface` de l'utilisateur courant.
*   Si le token est `null`, l'utilisateur est anonyme (ou non authentifié).
*   C'est ici que `getUser()` va chercher l'info.

### 2. Firewall (Listener)
Le Firewall n'est pas une seule classe, mais un **Event Listener** sur `kernel.request`.
*   Il vérifie si l'URL courante correspond à une `firewall` configurée (`security.yaml`).
*   Si oui, il active les **Authenticators** configurés pour ce firewall.

### 3. Authenticator (`AuthenticatorInterface`)
Remplace les anciens Guard/Listeners (depuis Symfony 5.3+).
*   **supports()** : Est-ce que cet authenticator peut gérer la requête ? (ex: présence d'un header `Authorization`).
*   **authenticate()** : Crée un `Passport` contenant les credentials (Badge) et l'utilisateur (UserBadge).
*   **onAuthenticationSuccess()** : Création de la réponse (ex: redirection, JSON).

### 4. UserProvider (`UserProviderInterface`)
Responsable de charger l'objet `User` depuis une source de données (BDD, API, Fichier) à partir d'un identifiant (email, username).
*   Méthode clé : `loadUserByIdentifier()`.

### 5. Voters (`VoterInterface`)
Le cœur de l'Autorisation.
*   Appelés quand on vérifie une permission : `is_granted('EDIT', $post)`.
*   Chaque Voter vote : `ACCESS_GRANTED`, `ACCESS_DENIED`, ou `ACCESS_ABSTAIN`.
*   **AccessDecisionManager** agrège les votes (stratégie: affirmative, consensus, unanimous).

## Le Flux d'Authentification

1.  **Requête** : L'utilisateur envoie une requête.
2.  **Firewall** : Détecte la config active.
3.  **Authenticator** :
    *   Extrait les credentials.
    *   Appelle le `UserProvider` pour récupérer le User.
    *   Vérifie le mot de passe (via `PasswordHasher`).
    *   Retourne un `Token` authentifié.
4.  **Session** : Le Token est sérialisé en session (pour ne pas se ré-authentifier à chaque page).
5.  **TokenStorage** : Le Token est placé dans le stockage pour la requête courante.

## 🧠 Concepts Clés
1.  **Passport** : Nouveau concept (Symfony 6/7) qui encapsule les données d'authentification (User, Password, CSRF token, RememberMe badge).
2.  **Stateless** : Pour une API, on configure le firewall en `stateless: true`. Le token n'est pas stocké en session, il doit être renvoyé à chaque requête (ex: JWT).

## ⚠️ Points de vigilance (Certification)
*   **Roles** : Les rôles sont de simples chaînes de caractères commençant par `ROLE_`. La hiérarchie (`role_hierarchy`) est gérée par le `RoleHierarchyVoter`.
*   **Access Control** : La section `access_control` dans `security.yaml` est la première barrière. Elle est vérifiée AVANT d'exécuter le contrôleur.

## Ressources
*   [Symfony Docs - Security Internals](https://symfony.com/doc/current/security.html)
*   [Custom Authenticators](https://symfony.com/doc/current/security/custom_authenticator.html)
