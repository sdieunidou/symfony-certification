# Utilisateurs (UserInterface)

## Concept clé
L'interface `UserInterface` est le contrat minimal que tout objet "Utilisateur" doit respecter pour être manipulé par le système de sécurité Symfony.
C'est généralement une Entité Doctrine (`App\Entity\User`), mais ce n'est pas obligatoire (ça peut être un DTO, un modèle LDAP).

## Méthodes de `UserInterface`

### 1. `getRoles(): array`
Retourne les rôles de l'utilisateur.
**Règle** : Doit garantir que chaque utilisateur a au moins un rôle (souvent `ROLE_USER`) et que les rôles sont uniques.

### 2. `getUserIdentifier(): string`
Retourne l'identifiant unique (login) : email, username, ou API Key.
*(Remplace `getUsername` depuis Symfony 5.3).*

### 3. `eraseCredentials(): void`
Appelé après l'authentification pour nettoyer les données sensibles temporaires stockées dans l'objet (ex: le mot de passe en clair `plainPassword` soumis par le formulaire).

## Interface `PasswordAuthenticatedUserInterface`
Si votre utilisateur se connecte avec un mot de passe (Form Login, HTTP Basic), il **DOIT** implémenter cette interface supplémentaire.
*   `getPassword(): ?string` : Retourne le hash du mot de passe (ou null).

## Interface `EquatableInterface` (Optionnel)
Par défaut, lors du "Refresh User" (rechargement depuis la session), Symfony vérifie si l'utilisateur a changé en comparant certaines propriétés (password, salt, username).
Si vous implémentez `EquatableInterface`, vous prenez le contrôle de cette comparaison via la méthode `isEqualTo(UserInterface $user)`.
*Utile si vous voulez déconnecter l'utilisateur si son `email` change, mais pas si son `lastname` change.*

## 🧠 Concepts Clés
1.  **Objet léger** : L'objet User est sérialisé en session. Ne stockez pas de grosses données (Blob, Collections Doctrine chargées) dans l'objet User.
2.  **Découplage** : Le composant Security ne connaît pas votre classe `User`, il ne connaît que l'interface.

## ⚠️ Points de vigilance (Certification)
*   **getUsername** : Cette méthode est dépréciée et supprimée de `UserInterface` dans les versions récentes au profit de `getUserIdentifier`.
*   **Salt** : `getSalt()` n'est plus nécessaire avec les algorithmes modernes (Bcrypt/Sodium).

## Ressources
*   [Symfony Docs - The User Class](https://symfony.com/doc/current/security/user.html)
