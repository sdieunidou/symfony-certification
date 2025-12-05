# Contrôleurs Internes Natifs

## Concept clé
Symfony inclut des contrôleurs génériques dans `FrameworkBundle` pour gérer les cas simples (Redirection statique, Page statique) sans avoir à créer une classe PHP vide.
Ces contrôleurs sont utilisés directement dans la configuration de **Routing** (`routes.yaml`).

## 1. TemplateController
Rend un template Twig statique. Idéal pour les pages "À propos", "Mentions légales", "Homepage" statique.

```yaml
# config/routes.yaml
about_us:
    path: /about
    controller: Symfony\Bundle\FrameworkBundle\Controller\TemplateController::templateAction
    defaults:
        template: 'pages/about.html.twig'
        maxAge: 86400 # Cache HTTP (optionnel)
        sharedAge: 86400
        context: # Variables passées à Twig
            title: 'About Us'
```
*Depuis Symfony 5, on peut utiliser l'alias court `template` (dépend de la config).*

## 2. RedirectController
Gère les redirections (migration d'URLs, shortcuts).

```yaml
# config/routes.yaml
doc_shortcut:
    path: /doc
    controller: Symfony\Bundle\FrameworkBundle\Controller\RedirectController::urlRedirectAction
    defaults:
        path: 'https://symfony.com/doc'
        permanent: true # 301
```
Il existe deux actions :
*   `urlRedirectAction` : Redirige vers une URL absolue ou un path.
*   `redirectAction` : Redirige vers une **route** interne.

## 🧠 Concepts Clés
1.  **Performance** : Ces contrôleurs sont optimisés et évitent d'avoir des milliers de classes PHP inutiles ("Empty Controllers") qui polluent le dossier `src/Controller`.
2.  **Caching** : `TemplateController` permet de définir facilement les headers de cache HTTP (`maxAge`, `sharedAge`, `private`).

## ⚠️ Points de vigilance (Certification)
*   **Services** : On ne peut pas injecter de services personnalisés dans ces contrôleurs (ils sont pré-compilés dans le framework). Si vous avez besoin de logique dynamique (ex: charger des produits depuis la DB), créez un vrai contrôleur.
*   **Nom complet** : Il faut souvent utiliser le FQCN (`Symfony\Bundle\FrameworkBundle\Controller\...`) dans le YAML, bien que des raccourcis existent.

## Ressources
*   [Symfony Docs - Render Template from Router](https://symfony.com/doc/current/templates.html#rendering-a-template-directly-from-a-route)
*   [Symfony Docs - Redirect from Router](https://symfony.com/doc/current/routing.html#redirecting-to-urls-and-routes-directly)
