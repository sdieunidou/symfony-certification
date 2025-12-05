# Rôles et Hiérarchie

## Concept clé
Les rôles sont des chaînes de caractères simples (`ROLE_USER`, `ROLE_ADMIN`) assignées aux utilisateurs.
Une hiérarchie permet de dire "L'Admin a aussi tous les droits du User".

## Application dans Symfony 7.0

### Hiérarchie (security.yaml)
```yaml
role_hierarchy:
    ROLE_ADMIN:       [ROLE_USER, ROLE_EDITOR]
    ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
```
Si j'ai `ROLE_SUPER_ADMIN`, j'ai automatiquement `ROLE_ADMIN`, `ROLE_USER`, etc.

### Méthodes
*   `$user->getRoles()` : Retourne les rôles explicites stockés en base.
*   `is_granted('ROLE_XX')` : Vérifie les rôles (en prenant en compte la hiérarchie).

## Points de vigilance (Certification)
*   **Convention** : Un rôle DOIT commencer par `ROLE_`. Sinon, il n'est pas géré par le système de vote par défaut (`RoleVoter`).
*   **Reachable Roles** : `RoleHierarchyInterface::getReachableRoleNames($roles)` calcule tous les rôles effectifs.
*   **Switch User** : Le rôle spécial `ROLE_ALLOWED_TO_SWITCH` permet l'impersonnation (`?_switch_user=bob`).

## Ressources
*   [Symfony Docs - Role Hierarchy](https://symfony.com/doc/current/security.html#hierarchical-roles)

