# User Providers (Fournisseurs d'utilisateurs)

## Concept clé
Le User Provider est le composant "Lecture Seule" qui permet à Symfony de récupérer un utilisateur UserInterface à partir d'un identifiant (email, username, api_key).
Il ne gère ni le mot de passe, ni l'authentification. Juste le chargement.

## Interface `UserProviderInterface`
Trois méthodes obligatoires :
1.  `loadUserByIdentifier(string $identifier): UserInterface` : Chargement initial (Login).
2.  `refreshUser(UserInterface $user): UserInterface` : Rechargement à chaque requête (depuis la session).
3.  `supportsClass(string $class): bool`.

## Types de Providers

### 1. Entity (Doctrine) - Le plus courant
Charge l'utilisateur depuis la base de données.

```yaml
providers:
    app_user_provider:
        entity:
            class: App\Entity\User
            property: email # La colonne à chercher
```

### 2. Memory (Static)
Utile pour les tests ou un backend admin simple.

```yaml
providers:
    admin_users:
        memory:
            users:
                admin: { password: '...', roles: ['ROLE_ADMIN'] }
```

### 3. Chain (Chaîne)
Combine plusieurs providers. Cherche dans le premier, puis le second...

```yaml
providers:
    all_users:
        chain:
            providers: [admin_users, app_user_provider]
```

### 4. Custom (Service)
Si vous chargez vos utilisateurs depuis une API externe.
Créez une classe qui implémente `UserProviderInterface` et configurez-la :
```yaml
providers:
    my_api_provider:
        id: App\Security\ApiUserProvider
```

## 🧠 Concepts Clés
1.  **Refresh User** : C'est une sécurité. À chaque requête, Symfony prend l'ID de l'utilisateur stocké en session, et demande au Provider de le recharger (`refreshUser`). Si l'utilisateur a été supprimé ou si ses données critiques ont changé (mot de passe), il est déconnecté.
2.  **Identifier** : Depuis Symfony 5.3, `loadUserByUsername` est remplacé par `loadUserByIdentifier`.

## ⚠️ Points de vigilance (Certification)
*   **Pourquoi Refresh ?** : Pour garantir que l'utilisateur en session est toujours à jour (rôles, état bloqué) par rapport à la DB.
*   **Stateless** : Si votre firewall est `stateless: true`, `refreshUser` n'est jamais appelé (car pas de session).

## Ressources
*   [Symfony Docs - User Providers](https://symfony.com/doc/current/security/user_provider.html)
