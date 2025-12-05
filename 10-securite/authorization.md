# Autorisation (AuthZ)

## Concept clé
L'autorisation répond à la question : **"Avez-vous le droit de faire ça ?"**.
Elle intervient *après* l'authentification (ou pendant, si l'action est publique).

## Application dans Symfony 7.0
Le service central est `AuthorizationCheckerInterface`.

La décision se base sur :
1.  **Attributs** : Ce qu'on vérifie (ex: `ROLE_ADMIN`, `ARTICLE_EDIT`).
2.  **Sujet** : L'objet sur lequel on agit (ex: `$article`).
3.  **Utilisateur** : Celui qui demande l'accès (récupéré du TokenStorage).

### Vérification dans le code
```php
// Contrôleur
$this->denyAccessUnlessGranted('ROLE_ADMIN');

// Service
if ($this->authChecker->isGranted('ARTICLE_EDIT', $article)) { ... }

// Twig
{% if is_granted('ROLE_ADMIN') %} ... {% endif %}
```

## Points de vigilance (Certification)
*   **Roles** : Un rôle est une chaîne commençant par `ROLE_`. C'est le niveau d'autorisation le plus basique.
*   **Voters** : Pour des règles complexes (ex: "Je peux éditer si je suis l'auteur"), on utilise des Voters.
*   **AccessDecisionManager** : Le service qui orchestre les voters (stratégie `affirmative` par défaut : il suffit d'un voter qui dit OUI).

## Ressources
*   [Symfony Docs - Authorization](https://symfony.com/doc/current/security.html#authorization-denying-access)

