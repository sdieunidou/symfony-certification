# Contrôleurs Internes Natifs

## Concept clé
Symfony fournit quelques contrôleurs "built-in" pour des fonctionnalités standard.

## Application dans Symfony 7.0
*   `RedirectController` : Permet de définir des redirections directement dans `routes.yaml` sans créer de classe.
*   `TemplateController` : Permet de rendre un template Twig directement depuis `routes.yaml`.

## Exemple de code (routes.yaml)

```yaml
# Redirection simple (Legacy -> New)
doc_shortcut:
    path: /doc
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction
    defaults:
        path: https://symfony.com/doc
        permanent: true

# Page statique (sans contrôleur PHP dédié)
about_us:
    path: /about
    controller: Symfony\Bundle\FrameworkBundle\Controller\TemplateController::templateAction
    defaults:
        template: 'pages/about.html.twig'
        # On peut passer des variables
        context:
            team_name: 'Symfony Team'
```

## Points de vigilance (Certification)
*   **Utilité** : Très utile pour les pages statiques (Mentions légales, About) ou les redirections de migration, pour éviter de polluer vos contrôleurs PHP avec des méthodes vides.

## Ressources
*   [Symfony Docs - Built-in Controllers](https://symfony.com/doc/current/controller/service.html#invoking-controllers-as-services) (Note: Documentation éparse, souvent trouvée dans la section Routing).

