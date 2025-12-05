# Valeurs par Défaut

## Concept clé
Rendre un paramètre optionnel en lui donnant une valeur par défaut.

## Application dans Symfony 7.0

### Attributs
Si le paramètre PHP a une valeur par défaut, le paramètre de route devient optionnel automatiquement.

```php
// /blog matche (page = 1)
// /blog/2 matche (page = 2)
#[Route('/blog/{page<\d+>}', name: 'blog_list')]
public function list(int $page = 1): Response { ... }
```

Ou explicitement :
```php
#[Route('/blog/{page}', defaults: ['page' => 1])]
```

### YAML
```yaml
blog_list:
    path: /blog/{page}
    controller: ...
    defaults:
        page: 1
```

## Points de vigilance (Certification)
*   **Position** : Seuls les paramètres à la **fin** du pattern peuvent être optionnels. On ne peut pas avoir `/blog/{page}/sort` avec `page` optionnel (car `/blog//sort` n'est pas valide).
*   **Null** : Si vous autorisez null (`?int $page = null`), la route matchera sans le paramètre.

## Ressources
*   [Symfony Docs - Optional Parameters](https://symfony.com/doc/current/routing.html#optional-parameters)

