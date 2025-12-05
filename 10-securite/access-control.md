# Règles de Contrôle d'Accès (Access Control Rules)

## Concept clé
Sécuriser des patterns d'URL directement dans la configuration `security.yaml`. C'est la première barrière de défense (globale).

## Application dans Symfony 7.0

```yaml
access_control:
    # Ordre important : First match wins !
    
    # 1. Login public
    - { path: ^/login, roles: PUBLIC_ACCESS }
    
    # 2. Admin sécurisé
    - { path: ^/admin, roles: ROLE_ADMIN }
    
    # 3. Profil utilisateur (doit être connecté)
    - { path: ^/profile, roles: ROLE_USER }
    
    # 4. API (Method restriction)
    - { path: ^/api, roles: ROLE_API, methods: [POST, PUT] }
```

## Points de vigilance (Certification)
*   **PUBLIC_ACCESS** : Rôle virtuel spécial pour dire "Tout le monde peut accéder, même non connecté".
*   **IS_AUTHENTICATED_FULLY** : L'utilisateur est connecté (pas via RememberMe).
*   **IS_AUTHENTICATED_REMEMBERED** : Connecté (via session ou cookie RememberMe).
*   **Channel** : On peut forcer HTTPS (`requires_channel: https`).
*   **IP** : On peut restreindre par IP (`ips: [127.0.0.1, ...]`).

## Ressources
*   [Symfony Docs - Access Control](https://symfony.com/doc/current/security/access_control.html)

