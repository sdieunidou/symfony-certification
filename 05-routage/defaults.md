# Valeurs par Défaut (Defaults)

## Concept clé
Un paramètre de route (`{page}`) peut être rendu optionnel en lui fournissant une valeur par défaut.
Si l'URL ne contient pas le paramètre, Symfony utilisera cette valeur.

## Syntaxe (Attributs)

### 1. Via signature PHP (Recommandé)
Si l'argument de la méthode contrôleur a une valeur par défaut, le paramètre de route devient optionnel.

```php
// Matche /blog (page=1) ET /blog/2 (page=2)
#[Route('/blog/{page}')]
public function list(int $page = 1): Response
```

### 2. Via l'option `defaults`
```php
#[Route('/blog/{page}', defaults: ['page' => 1])]
public function list(int $page): Response
```

### 3. Nullable
Si vous autorisez `null`, le paramètre est optionnel et vaut null si absent.

```php
// Matche /search (query=null) ET /search/foo (query=foo)
#[Route('/search/{query}')]
public function search(?string $query): Response
```

## Règles de Position
Un paramètre optionnel ne peut se trouver qu'à la **fin** du pattern (ou être suivi uniquement d'autres paramètres optionnels).

*   ✅ `/blog/{page}` avec `page=1`.
*   ✅ `/blog/{page}/{sort}` avec `page=1, sort=date`. (Si je demande `/blog`, j'ai `page=1, sort=date`).
*   ❌ `/blog/{page}/details` avec `page=1`. (Impossible de matcher `/blog/details` car le séparateur `/` est ambigu).

## 🧠 Concepts Clés
1.  **Canonical URL** : Symfony génère l'URL la plus courte possible. Si la valeur du paramètre est égale à la valeur par défaut, elle est omise lors de la génération (`generateUrl`).
    *   Si `page=1` (défaut), `generateUrl` -> `/blog`.
    *   Si `page=2`, `generateUrl` -> `/blog/2`.
2.  **Priorité** : Une route avec paramètre optionnel `/blog/{page?}` est techniquement une seule route qui gère deux cas. C'est souvent mieux que de créer deux routes distinctes (`/blog` et `/blog/{page}`).

## ⚠️ Points de vigilance (Certification)
*   **Défaut global** : On peut définir des `defaults` globaux dans `routes.yaml` lors de l'import d'un dossier (ex: `_format: json` pour tout le dossier `/api`).

## Ressources
*   [Symfony Docs - Optional Parameters](https://symfony.com/doc/current/routing.html#optional-parameters)
