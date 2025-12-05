# Paramètres d'URL (Requirements)

## Concept clé
Les paramètres d'URL sont dynamiques (ex: `/blog/{slug}`). Par défaut, ils acceptent n'importe quoi (sauf `/`).
On doit souvent les restreindre (ex: `id` doit être un entier, `locale` doit être `en` ou `fr`).

## Application dans Symfony 7.0
On utilise des Expressions Régulières (Regex).

### Attributs
```php
#[Route('/blog/{page}', name: 'blog_list', requirements: ['page' => '\d+'])]
public function list(int $page): Response { ... }
```

### Inline (Raccourci pratique)
```php
#[Route('/blog/{page<\d+>}', name: 'blog_list')]
```

### YAML
```yaml
blog_show:
    path: /blog/{slug}
    controller: ...
    requirements:
        slug: '[a-z0-9-]+'
```

## Points de vigilance (Certification)
*   **Greedy** : Si deux routes ont le même pattern mais des requirements différents, l'ordre compte.
*   **Validation** : Si le paramètre ne matche pas la regex, la route ne matche pas (Symfony passe à la suivante). Si aucune route ne matche, c'est une 404.

## Ressources
*   [Symfony Docs - Route Requirements](https://symfony.com/doc/current/routing.html#parameter-validation)

