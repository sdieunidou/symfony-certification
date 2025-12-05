# Configuration de la Sécurité (`security.yaml`)

## Concept clé
La sécurité est le composant le plus complexe à configurer. Tout se passe dans `config/packages/security.yaml`.
L'ordre des sections n'importe pas, mais l'ordre des éléments dans les listes (`firewalls`, `access_control`) est CRITIQUE.

## Structure Complète

```yaml
security:
    # 1. Configuration des Hashers (Mots de passe)
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    # 2. User Providers (Sources de données)
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    # 3. Firewalls (Zones sécurisées)
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false # Désactive la sécurité (Perf)
        
        main:
            lazy: true
            provider: app_user_provider
            
            # Authenticators
            form_login:
                login_path: app_login
                check_path: app_login
                enable_csrf: true
            
            json_login:
                check_path: api_login
            
            # Custom Authenticator
            custom_authenticator: App\Security\ApiKeyAuthenticator

            # Logout
            logout:
                path: app_logout
                target: app_home

            # Features
            remember_me:
                secret: '%kernel.secret%'
                lifetime: 604800
            
            # Limite les sessions concurrentes
            concurrent_sessions: 1

    # 4. Contrôle d'accès (URL Rules)
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }

    # 5. Hiérarchie des rôles
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER]
```

## 🧠 Concepts Clés
1.  **Authenticator Manager** : Depuis Symfony 6, c'est le nouveau système par défaut (`enable_authenticator_manager: true` est implicite).
2.  **Provider** : Un firewall a besoin d'un provider pour charger l'utilisateur après l'authentification (Refresh User).

## ⚠️ Points de vigilance (Certification)
*   **Pattern** : Si une URL matche plusieurs firewalls, le **premier** gagne. C'est pourquoi `dev` est toujours en premier.
*   **Context** : Pour partager l'authentification entre deux firewalls (ex: `main` et `admin` s'ils sont séparés), il faut leur donner le même `context`. Sinon, se connecter sur l'un ne connecte pas sur l'autre.

## Ressources
*   [Symfony Docs - Security Config](https://symfony.com/doc/current/reference/configuration/security.html)
