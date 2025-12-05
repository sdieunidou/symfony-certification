# Autorisation (AuthZ)

## Concept clé
Une fois authentifié (Qui ?), l'autorisation détermine les droits (Quoi ?).
Le service central est `AuthorizationCheckerInterface`.

## Mécanismes de Vérification

### 1. Contrôleur (`AbstractController`)
```php
$this->denyAccessUnlessGranted('ROLE_ADMIN');
$this->denyAccessUnlessGranted('POST_EDIT', $post); // Voter
```

### 2. Service (Injection)
```php
public function __construct(
    private AuthorizationCheckerInterface $authChecker
) {}

public function edit(Post $post)
{
    if (!$this->authChecker->isGranted('POST_EDIT', $post)) {
        throw new AccessDeniedException();
    }
}
```

### 3. Attributs PHP (`#[IsGranted]`) - Recommandé
Déclaratif et propre.

```php
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[IsGranted('POST_EDIT', subject: 'post')]
    public function edit(Post $post): Response { ... }
}
```

## Access Decision Manager
C'est le cerveau qui prend la décision finale en consultant tous les **Voters**.
Stratégies de vote (config `security.access_decision_manager.strategy`) :
1.  **affirmative** (Défaut) : Accès accordé dès qu'un voter dit OUI.
2.  **consensus** : La majorité l'emporte.
3.  **unanimous** : Tous les voters (qui ne s'abstiennent pas) doivent dire OUI.
4.  **priority** : Le premier voter (selon priorité service) décide.

## 🧠 Concepts Clés
1.  **RoleVoter** : Un voter natif qui vérifie les chaînes commençant par `ROLE_`.
2.  **AuthenticatedVoter** : Gère `IS_AUTHENTICATED_FULLY`, `IS_AUTHENTICATED_REMEMBERED`, `PUBLIC_ACCESS`.

## ⚠️ Points de vigilance (Certification)
*   **Subject** : L'attribut `#[IsGranted]` sur une méthode peut automatiquement résoudre le sujet (ex: l'argument `$post`) si le nom correspond.
*   **Exception** : Si l'accès est refusé :
    *   Si connecté : `AccessDeniedException` (403).
    *   Si pas connecté : `AuthenticationException` (Redirection vers Login).

## Ressources
*   [Symfony Docs - Authorization](https://symfony.com/doc/current/security.html#authorization-denying-access)
