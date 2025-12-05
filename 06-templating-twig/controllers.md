# Rendu de Contrôleur (Embedding Controllers)

## Concept clé
Parfois, une partie de la page nécessite une logique complexe (ex: un panier d'achat dans le header, une liste de "Derniers articles" dans une sidebar).
Au lieu de dupliquer cette logique dans tous les contrôleurs, on crée un contrôleur dédié qu'on appelle depuis le template.

## Application dans Symfony 7.0
On utilise la fonction `render()` combinée à `controller()`. (Note: Le tag `{% render %}` est obsolète/déprécié).

```twig
{# base.html.twig #}
<div id="sidebar">
    {{ render(controller('App\\Controller\\BlogController::recentArticles', { 'max': 3 })) }}
</div>
```

Cela déclenche une "sous-requête" interne. Symfony exécute le contrôleur `recentArticles`, récupère la `Response` HTML, et l'injecte à cet endroit.

## Points de vigilance (Certification)
*   **Performance** : Chaque appel à `render(controller())` instancie le Kernel complet (sous-requête). Abuser de cette fonctionnalité peut tuer les performances.
*   **Cache** : C'est là que le cache par fragment (ESI - Edge Side Includes) devient puissant. On peut cacher le fragment indépendamment de la page principale.
*   **Variables** : Le contrôleur appelé n'a pas accès aux variables du template parent (sauf celles passées explicitement en argument).

## Ressources
*   [Symfony Docs - Embedding Controllers](https://symfony.com/doc/current/templates.html#embedding-controllers)

