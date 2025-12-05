# Rendu des Formulaires (Twig)

## Concept clé
Symfony fournit des fonctions Twig pour rendre chaque partie du formulaire.

## Application dans Symfony 7.0

### Méthode simple (Prototypage)
```twig
{{ form(form) }}
```
Affiche tout d'un coup. Pratique mais peu flexible.

### Méthode détaillée (Contrôle total)
```twig
{{ form_start(form) }}
    
    {# Affiche les erreurs globales #}
    {{ form_errors(form) }}

    {# Affiche un champ complet (label + widget + errors + help) #}
    {{ form_row(form.task) }}
    
    {# Ou partie par partie #}
    <div class="my-custom-class">
        {{ form_label(form.dueDate) }}
        {{ form_widget(form.dueDate, { 'attr': {'class': 'datepicker'} }) }}
        {{ form_errors(form.dueDate) }}
    </div>

    {{ form_rest(form) }} {# Affiche les champs oubliés (token CSRF, boutons) #}

{{ form_end(form) }}
```

## Points de vigilance (Certification)
*   **form_start** : Génère la balise `<form>` avec la bonne méthode (POST), l'action, et l'enctype (multipart si upload).
*   **form_end** : Ferme `</form>` ET affiche tous les champs qui n'ont pas encore été rendus manuellement (`form_rest`). C'est important pour afficher le token CSRF caché.
*   **form_row** : C'est le "standard" à utiliser.

## Ressources
*   [Symfony Docs - Rendering Forms](https://symfony.com/doc/current/form/form_customization.html)

