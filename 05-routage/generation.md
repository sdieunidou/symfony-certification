# Génération d'URLs

## Concept clé
Ne jamais coder d'URLs en dur (`/blog/my-slug`). Utiliser le générateur de routes (Router) avec le nom de la route.
Cela permet de changer les URLs dans la config sans casser l'application.

## Application dans Symfony 7.0

### Dans le Contrôleur
```php
// URL relative : /blog/my-slug
$url = $this->generateUrl('blog_show', ['slug' => 'my-slug']);

// URL absolue : https://example.com/blog/my-slug
$url = $this->generateUrl('blog_show', ['slug' => 'my-slug'], UrlGeneratorInterface::ABSOLUTE_URL);
```

### Dans Twig
```twig
<a href="{{ path('blog_show', {slug: 'my-slug'}) }}">Lien</a>
<a href="{{ url('blog_show', {slug: 'my-slug'}) }}">Lien Absolu</a>
```

## Points de vigilance (Certification)
*   **Query String** : Si vous passez des paramètres qui ne sont pas dans le pattern de la route (ex: `cat` pour la route `/blog/{slug}`), ils sont automatiquement ajoutés en Query String (`/blog/my-slug?cat=tech`).
*   **Préfixe** : Si votre routeur a un préfixe (ex: `/api`), la génération l'inclut automatiquement.

## Ressources
*   [Symfony Docs - Generating URLs](https://symfony.com/doc/current/routing.html#generating-urls)

