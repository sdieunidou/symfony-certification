# User Providers (Fournisseurs d'utilisateurs)

## Concept clé
Le User Provider est le pont entre le système de sécurité et votre stockage d'utilisateurs (Base de données, LDAP, API, Fichier config, Mémoire).
Son rôle unique : **Charger un utilisateur** (UserInterface) à partir d'un identifiant (email, username).

## Application dans Symfony 7.0
Le type le plus courant est `entity` (Doctrine).

```yaml
providers:
    # Provider Doctrine
    app_user_provider:
        entity:
            class: App\Entity\User
            property: email
            
    # Provider Chaîné (Cherche dans A, puis B)
    chain_provider:
        chain:
            providers: [in_memory_users, app_user_provider]
```

Il implémente `UserProviderInterface` :
*   `loadUserByIdentifier(string $identifier)`
*   `refreshUser(UserInterface $user)` : Recharger l'user depuis la session (vérifier s'il a changé).
*   `supportsClass(string $class)`

## Points de vigilance (Certification)
*   **User vs Provider** : Le Provider *trouve* l'utilisateur. L'Authenticator *vérifie* le mot de passe.
*   **Refresh** : À chaque requête (si stateful), l'utilisateur est désérialisé de la session, puis le Provider est appelé (`refreshUser`) pour s'assurer qu'il existe toujours et est à jour (ex: rôle changé).

## Ressources
*   [Symfony Docs - User Providers](https://symfony.com/doc/current/security/user_provider.html)

