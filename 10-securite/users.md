# Utilisateurs (UserInterface)

## Concept clé
L'interface `UserInterface` est le contrat minimal que tout objet "Utilisateur" doit respecter pour être manipulé par le système de sécurité Symfony.
C'est généralement une Entité Doctrine (`App\Entity\User`), mais ce n'est pas obligatoire.

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
Si votre utilisateur se connecte avec un mot de passe, il **DOIT** implémenter cette interface.
*   `getPassword(): ?string` : Retourne le hash du mot de passe.

## Sérialisation et Sécurité (`__serialize`)
L'objet User est sérialisé et stocké en session.
**Risque** : Stocker le mot de passe (même hashé) en session n'est pas recommandé.
**Solution** : Implémentez `__serialize` (ou `Serializable`) pour exclure les propriétés sensibles ou inutiles.

```php
public function __serialize(): array
{
    return [
        'id' => $this->id,
        'email' => $this->email,
        // On ne stocke PAS le password
    ];
}
```
*Note : Symfony recharge de toute façon l'utilisateur depuis la DB à chaque requête (Refresh User).*

## Interface `EquatableInterface` (Optionnel)
Par défaut, lors du "Refresh User", Symfony vérifie si l'utilisateur a changé en comparant les valeurs de retour de `getUserIdentifier`, `getPassword` et `getSalt`.
Si vous implémentez `EquatableInterface`, vous prenez le contrôle via `isEqualTo(UserInterface $user)`.
*Utile pour forcer la déconnexion si une propriété spécifique change.*

## 🧠 Concepts Clés
1.  **Objet léger** : L'objet User en session doit être léger. Évitez de sérialiser les relations Doctrine (Lazy Loading faillira au dé-sérialisage).
2.  **Découplage** : Le composant Security ne connaît pas votre classe `User`, il ne connaît que l'interface.

## ⚠️ Points de vigilance (Certification)
*   **getUsername** : Déprécié au profit de `getUserIdentifier`.
*   **Salt** : `getSalt()` n'est plus nécessaire avec les algorithmes modernes.

## Ressources
*   [Symfony Docs - The User Class](https://symfony.com/doc/current/security/user.html)
