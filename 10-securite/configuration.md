# Configuration de la Sécurité

## Concept clé
Toute la sécurité est centralisée dans `config/packages/security.yaml`.

## Application dans Symfony 7.0

Structure typique :
```yaml
security:
    # 1. Hachage des mots de passe
    password_hashers:
        Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface: 'auto'

    # 2. Fournisseurs d'utilisateurs (Où sont-ils ?)
    providers:
        app_user_provider:
            entity:
                class: App\Entity\User
                property: email

    # 3. Firewalls (Comment on se connecte ?)
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false # Pas de sécu pour les assets
        main:
            lazy: true
            provider: app_user_provider
            form_login:
                login_path: app_login
                check_path: app_login
            logout:
                path: app_logout

    # 4. Contrôle d'accès (URL matching)
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

## Points de vigilance (Certification)
*   **Ordre des Firewalls** : Le premier qui matche l'URL gagne. Toujours mettre les règles spécifiques (`dev`, `api`) avant le `main` (qui matche souvent `/`).
*   **Lazy** : `lazy: true` signifie que la session n'est démarrée que si on accède réellement à l'utilisateur. C'est la performance par défaut.

## Ressources
*   [Symfony Docs - Security Configuration](https://symfony.com/doc/current/security.html#configuration)

