# Héritage de Template

## Concept clé
L'héritage est la fonctionnalité la plus puissante de Twig. Elle permet de définir un layout de base (squelette) et de laisser les templates enfants remplir ou surcharger des zones spécifiques appelées "Blocs".

## Application dans Symfony 7.0

### Parent (base.html.twig)
```twig
<!DOCTYPE html>
<html>
    <head>
        <title>{% block title %}Bienvenue{% endblock %}</title>
    </head>
    <body>
        <header>...</header>
        <main>
            {% block body %}{% endblock %}
        </main>
    </body>
</html>
```

### Enfant (index.html.twig)
```twig
{% extends 'base.html.twig' %}

{% block title %}Accueil{% endblock %}

{% block body %}
    <h1>Page d'accueil</h1>
    {# Rappeler le contenu du parent #}
    {{ parent() }}
{% endblock %}
```

## Points de vigilance (Certification)
*   **Règle d'or** : Dans un template enfant (qui a un tag `extends`), **tout** le contenu affiché doit être à l'intérieur d'un `block`. Tout texte en dehors des blocs provoquera une erreur (Twig ne saura pas où le mettre dans le parent).
*   **Niveaux** : L'héritage peut être multi-niveaux (Enfant extends Layout extends Base).
*   **Dynamic inheritance** : On peut hériter dynamiquement : `{% extends ajax ? 'ajax.html.twig' : 'base.html.twig' %}`.

## Ressources
*   [Twig - Template Inheritance](https://twig.symfony.com/doc/3.x/tags/extends.html)

