# Redirections dans le Routage

## Concept clé
Parfois, on veut rediriger une ancienne URL vers une nouvelle sans écrire de contrôleur. Le composant Routing permet de le faire directement dans la configuration YAML.

## Application dans Symfony 7.0
Utilisation du `RedirectController`.

```yaml
# config/routes.yaml

# Redirection simple 301 (Permanent)
old_page:
    path: /old-page
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction
    defaults:
        path: /new-page
        permanent: true

# Redirection vers une autre route (plus robuste)
legacy_route:
    path: /legacy/{id}
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::redirectAction
    defaults:
        route: 'new_route_name'
        permanent: true
        # Les paramètres (id) sont conservés/transmis
```

## Points de vigilance (Certification)
*   **Trailing Slash** : Symfony redirige automatiquement les URLs avec slash final vers sans slash (ou l'inverse) selon la configuration, pour éviter le duplicate content.
*   **RedirectController** : Connaître la différence entre `urlRedirectAction` (vers un path) et `redirectAction` (vers une route nommée).

## Ressources
*   [Symfony Docs - Redirects](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly-from-config)

