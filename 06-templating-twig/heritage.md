# Héritage de Template

## Concept clé
L'héritage permet de définir un **Layout** (squelette HTML commun) et de laisser les templates enfants remplir les trous (Blocs).
C'est l'équivalent de l'héritage de classe en PHP.

## Structure

### 1. Le Parent (`base.html.twig`)
Définit la structure et les blocs par défaut.

```twig
<!DOCTYPE html>
<html>
    <head>
        <title>{% block title %}Mon Site{% endblock %}</title>
        {% block stylesheets %}
            <link href="/app.css" rel="stylesheet"/>
        {% endblock %}
    </head>
    <body>
        <header>...</header>
        
        <div class="container">
            {% block body %}{% endblock %}
        </div>
        
        {% block javascripts %}{% endblock %}
    </body>
</html>
```

### 2. L'Enfant (`page.html.twig`)
Étend le parent et surcharge les blocs.

```twig
{% extends 'base.html.twig' %}

{% block title %}Ma Page - {{ parent() }}{% endblock %}

{% block body %}
    <h1>Contenu de la page</h1>
{% endblock %}
```

## La fonction `parent()`
Permet de récupérer le contenu du bloc parent au lieu de l'écraser complètement.
Utile pour ajouter du CSS/JS spécifique à une page tout en gardant les styles globaux.

```twig
{% block stylesheets %}
    {{ parent() }} {# Garde app.css #}
    <link href="/page-specifique.css" rel="stylesheet"/>
{% endblock %}
```

## 🧠 Concepts Clés
1.  **Unique** : Un template ne peut étendre qu'**un seul** template parent.
2.  **Racine** : Le tag `{% extends %}` doit être la **première** ligne du fichier (sauf commentaires).
3.  **Hors Bloc** : Dans un template enfant, il est **interdit** d'écrire du HTML en dehors d'un bloc `{% block %}`. Twig lancera une erreur de compilation.

## ⚠️ Points de vigilance (Certification)
*   **Héritage dynamique** : On peut choisir le parent dynamiquement (ex: layout normal vs layout AJAX).
    ```twig
    {% extends request.isXmlHttpRequest ? 'ajax.html.twig' : 'base.html.twig' %}
    ```
*   **Niveaux** : L'héritage peut être profond (A étend B qui étend C).
*   **Block Naming** : Les noms de blocs doivent être uniques dans un template.

## Ressources
*   [Twig Docs - Template Inheritance](https://twig.symfony.com/doc/3.x/tags/extends.html)
