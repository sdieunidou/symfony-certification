# Paramètres d'URL et Prérequis (Requirements)

## Concept clé
Les paramètres de route (`{slug}`, `{id}`) capturent des segments d'URL variables.
Pour éviter les conflits et valider les données, on définit des **Requirements** (Prérequis) sous forme d'Expressions Régulières (Regex).

## Syntaxe (Attributs)

### 1. Inline (Le plus concis - Recommandé)
On définit la regex directement dans le placeholder `<...>`.

```php
// id doit être composé de chiffres uniquement
#[Route('/blog/{id<\d+>}', name: 'blog_show')]

// slug doit être une chaine alphanumérique + tirets
#[Route('/article/{slug<[a-z0-9-]+>}')]
```

### 2. Option `requirements`
Plus lisible pour les regex complexes ou réutilisées.

```php
#[Route(
    '/blog/{year}/{slug}', 
    requirements: [
        'year' => '\d{4}',
        'slug' => '[a-z0-9-]+'
    ]
)]
```

## Regex Courantes
*   `\d+` : Entier (1, 99, 1000).
*   `\w+` : Mot (lettres, chiffres, underscore).
*   `[a-z0-9-]+` : Slug URL classique.
*   `.+` : Tout (y compris les slashs `/`, si configuré, sinon s'arrête au prochain slash).

## Catch-All (Wildcard)
Pour capturer "tout le reste de l'URL", y compris les slashs.
Exemple : un gestionnaire de fichiers `/files/path/to/my/image.jpg`.

```php
// 'path' capturera "path/to/my/image.jpg"
#[Route('/files/{path}', requirements: ['path' => '.+'])]
```
*Sans le `.`, le paramètre s'arrêterait au premier `/`.*

## 🧠 Concepts Clés
1.  **Matching Strict** : Si l'URL ne correspond pas à la regex, la route est ignorée. Symfony essaie la suivante.
2.  **Validation Précoce** : C'est une première couche de validation. Si `{id}` force `\d+`, vous êtes sûr de recevoir un string numérique dans le contrôleur (ou rien du tout, 404).

## ⚠️ Points de vigilance (Certification)
*   **Ancrage** : Symfony ancre automatiquement la regex (ajoute `^` et `$`). Inutile de les mettre (`\d+` suffit, pas besoin de `^\d+$`).
*   **Priorité** :
    *   Route A : `/blog/{id<\d+>}`
    *   Route B : `/blog/{slug}`
    *   URL `/blog/123` matche A.
    *   URL `/blog/abc` ne matche pas A (regex fail), donc matche B.
    *   C'est un excellent moyen de gérer des URLs polymorphes.

## Ressources
*   [Symfony Docs - Requirements](https://symfony.com/doc/current/routing.html#parameter-validation)
