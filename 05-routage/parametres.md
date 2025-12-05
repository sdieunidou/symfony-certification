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

// Avec valeur par défaut (Optionnel)
#[Route('/blog/{page<\d+>?1}')] 
```

### 2. Option `requirements`
Plus lisible pour les regex complexes ou réutilisées.

```php
use Symfony\Component\Routing\Requirement\Requirement;

#[Route(
    '/blog/{year}/{slug}', 
    requirements: [
        'year' => Requirement::DIGITS, // '\d+'
        'slug' => Requirement::ASCII_SLUG, // '[A-Za-z0-9]+(?:-[A-Za-z0-9]+)*'
    ]
)]
```
*L'Enum `Requirement` (Symfony 6.4+) fournit des constantes regex prêtes à l'emploi.*

## Slash dans les Paramètres
Par défaut, un paramètre s'arrête au premier `/`.
Pour capturer "tout le reste de l'URL" (Wildcard), utilisez la regex `.+`.

```php
// 'path' capturera "path/to/my/image.jpg"
#[Route('/files/{path}', requirements: ['path' => '.+'])]
```
*Attention : Si vous utilisez `.+`, ce doit être le dernier paramètre de la route, sinon ambiguïté.*

## Parameter Conversion (ParamConverter)
Symfony peut convertir automatiquement un paramètre `{id}` en Entité Doctrine ou en Enum.
Si le nom du paramètre de route diffère de l'argument du contrôleur, utilisez la syntaxe `{routeParam:controllerArg}` (Symfony 7.1+) :

```php
#[Route('/blog/{slug:post}', name: 'blog_show')]
public function show(BlogPost $post): Response
```
Ici, le paramètre d'URL `slug` est utilisé pour chercher l'entité `BlogPost` injectée dans `$post`.

## 🧠 Concepts Clés
1.  **Matching Strict** : Si l'URL ne correspond pas à la regex, la route est ignorée. Symfony essaie la suivante.
2.  **Validation Précoce** : C'est une première couche de validation. Si `{id}` force `\d+`, vous êtes sûr de recevoir un string numérique dans le contrôleur (ou rien du tout, 404).
3.  **Unicode** : Les regex supportent l'unicode (`\p{Lu}` pour majuscules toutes langues).

## ⚠️ Points de vigilance (Certification)
*   **Ancrage** : Symfony ancre automatiquement la regex (ajoute `^` et `$`). Inutile de les mettre (`\d+` suffit, pas besoin de `^\d+$`).
*   **Priorité** :
    *   Route A : `/blog/{id<\d+>}`
    *   Route B : `/blog/{slug}`
    *   URL `/blog/123` matche A.
    *   URL `/blog/abc` ne matche pas A (regex fail), donc matche B.

## Ressources
*   [Symfony Docs - Requirements](https://symfony.com/doc/current/routing.html#parameter-validation)
