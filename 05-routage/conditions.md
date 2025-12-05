# Correspondance de Requête Conditionnelle

## Concept clé
Parfois, le path, la méthode et le host ne suffisent pas. On veut matcher selon une logique arbitraire (ex: un header spécifique, user-agent).

## Application dans Symfony 7.0
L'option `condition` permet d'utiliser le composant **ExpressionLanguage**.

```php
#[Route(
    '/contact',
    condition: "context.getMethod() in ['GET', 'HEAD'] and request.headers.get('User-Agent') matches '/firefox/i'"
)]
public function contactFirefox(): Response { ... }
```

Les variables disponibles dans l'expression :
*   `context` : Instance de `RequestContext`.
*   `request` : Instance de `Request`.

## Points de vigilance (Certification)
*   **Performance** : Les expressions sont évaluées à l'exécution, c'est (légèrement) plus lent que les regex statiques.
*   **Compilation** : Les conditions ne sont pas compilées en regex Apache/Nginx lors du dump des routes, elles nécessitent PHP.

## Ressources
*   [Symfony Docs - Route Conditions](https://symfony.com/doc/current/routing.html#matching-expressions)

