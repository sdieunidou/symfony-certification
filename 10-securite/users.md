# Utilisateurs (UserInterface)

## Concept clé
L'objet `User` est la représentation de l'identité dans le système.
Il doit implémenter `Symfony\Component\Security\Core\User\UserInterface`.

## Application dans Symfony 7.0
Méthodes requises :
1.  `getRoles(): array` : Retourne les rôles (doit toujours contenir au moins `ROLE_USER`).
2.  `getPassword(): ?string` : Le hash du mot de passe (ou null si auth externe).
3.  `eraseCredentials()` : Pour nettoyer les données sensibles (plainPassword) après le login.
4.  `getUserIdentifier(): string` : L'identifiant unique (email, username). Remplace `getUsername()` (déprécié).

### PasswordAuthenticatedUserInterface
Si l'utilisateur a un mot de passe stocké, il doit aussi implémenter cette interface (marqueur pour le PasswordHasher).

```php
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // ...
}
```

## Points de vigilance (Certification)
*   **Sérialisation** : L'objet User est sérialisé en session. Il doit être léger. Ne pas stocker de grosses données ou des objets non sérialisables (ressources) dedans. Éviter les relations Doctrine Lazy-Loaded non initialisées qui pourraient poser problème au réveil.
*   **Equatable** : Par défaut, Symfony compare les ID. Si vous voulez que l'utilisateur soit déconnecté si son mot de passe change en DB, implémentez `EquatableInterface` (ou laissez Symfony faire le check par défaut sur le password si le provider le permet).

## Ressources
*   [Symfony Docs - The User Class](https://symfony.com/doc/current/security/user.html)

