# Rôles et Hiérarchie

## Concept clé
Les rôles sont le mécanisme d'autorisation le plus simple.
Un rôle est une chaîne de caractères commençant **obligatoirement** par `ROLE_`.

## Assignation
Les rôles sont retournés par la méthode `getRoles()` de l'objet `User`.
Tout utilisateur authentifié possède au moins `ROLE_USER` (ajouté par défaut par Symfony si non présent).

## Hiérarchie des Rôles (`role_hierarchy`)
Permet l'héritage des permissions pour éviter la duplication en base de données.

```yaml
security:
    role_hierarchy:
        ROLE_EDITOR:      [ROLE_USER]
        ROLE_ADMIN:       [ROLE_EDITOR]
        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_ALLOWED_TO_SWITCH]
```
Si j'ai `ROLE_ADMIN`, `is_granted('ROLE_USER')` renverra `true`.

## Vérification
*   **Dans le code** : `$security->isGranted('ROLE_ADMIN')`.
*   **Dans l'Access Control** : `roles: ROLE_ADMIN`.

## Rôles Spéciaux (Virtuels)
Ces rôles n'existent pas en base, mais sont gérés par le système :
*   `IS_AUTHENTICATED_FULLY` : Connecté explicitement (Login).
*   `IS_AUTHENTICATED_REMEMBERED` : Connecté via cookie.
*   `PUBLIC_ACCESS` : Tout le monde.

## 🧠 Concepts Clés
1.  **Reachable Roles** : C'est la liste de tous les rôles qu'un utilisateur possède *effectivement* (Rôles directs + Rôles hérités via la hiérarchie). Le service `RoleHierarchy` calcule cela.
2.  **Convention** : Toujours utiliser des majuscules (`ROLE_MY_FEATURE`).

## ⚠️ Points de vigilance (Certification)
*   **Voter** : Le `RoleVoter` est le voter natif qui vote sur les attributs commençant par `ROLE_`. Si vous utilisez une chaîne sans ce préfixe (ex: `EDIT_POST`), le `RoleVoter` s'abstiendra (et un autre voter devra gérer ça).
*   **Stockage** : En base de données (JSON), on stocke `['ROLE_ADMIN']`. Grâce à la hiérarchie, c'est suffisant pour avoir aussi `ROLE_USER`.

## Ressources
*   [Symfony Docs - Roles](https://symfony.com/doc/current/security.html#roles)
