# Règles de Contrôle d'Accès (Access Control)

## Concept clé
La section `access_control` dans `security.yaml` est la **première ligne de défense**.
Elle permet de sécuriser des modèles d'URL (Regex) sans toucher au code (Contrôleurs).
Le principe fondamental est : **"First Match Wins"** (Le premier qui correspond gagne).

## Configuration (`security.yaml`)

```yaml
security:
    access_control:
        # 1. Exclusion (Assets, Profiler) : PUBLIC_ACCESS
        - { path: ^/(_(profiler|wdt)|css|images|js)/, roles: PUBLIC_ACCESS }

        # 2. Login : PUBLIC_ACCESS
        - { path: ^/login, roles: PUBLIC_ACCESS }

        # 3. Admin : Rôle requis
        # 'ips' restreint l'accès à une liste d'adresses IP (ex: VPN entreprise)
        - { path: ^/admin, roles: ROLE_ADMIN, ips: [127.0.0.1, 192.168.0.1/24] }

        # 4. API : Méthode HTTP spécifique
        - { path: ^/api/secure, roles: IS_AUTHENTICATED_FULLY, methods: [POST, PUT] }

        # 5. Expression complexe (allow_if)
        # Autorise l'accès si l'utilisateur a le rôle OU si c'est une IP de confiance
        - { path: ^/internal, allow_if: "is_granted('ROLE_ADMIN') or request.getClientIp() == '10.0.0.1'" }
```

## Attributs Spéciaux
En plus des rôles (`ROLE_XXX`), Symfony fournit des attributs virtuels :
*   `PUBLIC_ACCESS` : Autorise tout le monde (même anonyme).
*   `IS_AUTHENTICATED_FULLY` : Connecté (Session ou Token). Exclut "Remember Me".
*   `IS_AUTHENTICATED_REMEMBERED` : Connecté (Session ou Cookie "Se souvenir de moi"). Inclut "Fully".
*   `IS_IMPERSONATOR` : L'utilisateur actuel est en train d'imiter quelqu'un d'autre (`switch_user`).

## 🧠 Concepts Clés
1.  **Ordre** : C'est le piège classique. Si vous mettez `- { path: ^/, roles: ROLE_USER }` en premier, **toutes** les pages (y compris `/login`) nécessiteront d'être connecté -> Boucle de redirection infinie.
2.  **Canaux** : `requires_channel: https` force la redirection vers HTTPS pour ce pattern. (Moins utile aujourd'hui si tout le site est HTTPS via le serveur web).

## ⚠️ Points de vigilance (Certification)
*   **Access Control vs Firewall** :
    *   **Firewall** (`pattern: ^/admin`) : Active le système de sécurité (Session, User).
    *   **Access Control** (`path: ^/admin`) : Décide si on a le droit d'entrer.
    *   Vous pouvez avoir un firewall public (`security: true`, `anonymous: true` en vieux Symfony) et restreindre l'accès via `access_control`.

## Ressources
*   [Symfony Docs - Access Control](https://symfony.com/doc/current/security/access_control.html)
