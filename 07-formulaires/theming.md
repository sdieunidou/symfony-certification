# Thématisation des Formulaires (Theming)

## Concept clé
Le "Form Theming" est le mécanisme puissant de Symfony pour personnaliser le HTML généré par les helpers de formulaire.
Il repose sur l'héritage de blocs Twig.

## Architecture des Blocs
Chaque partie d'un formulaire correspond à un bloc Twig spécifique.
Nommage : `_{nom_du_champ}_{partie}` ou `{type}_{partie}`.

Exemple pour un champ `age` de type `integer` :
1.  `_customer_age_widget` (Le plus spécifique : champ 'age' du form 'customer')
2.  `integer_widget` (Pour tous les entiers)
3.  `number_widget` (Parent de integer)
4.  `form_widget_simple` (Base pour les inputs)
5.  `form_widget` (Base générique)

## Application d'un Thème

### 1. Global (config/packages/twig.yaml)
Pour tout le site (ex: Bootstrap 5).

```yaml
twig:
    form_themes: ['bootstrap_5_layout.html.twig']
```

### 2. Local (Dans le template)
Pour un formulaire spécifique.

```twig
{% form_theme form 'form/my_theme.html.twig' %}
{# ou plusieurs #}
{% form_theme form 'theme1.html.twig' 'theme2.html.twig' %}
```

### 3. Inline (`_self`)
Pour une surcharge rapide dans la page même.

```twig
{% form_theme form _self %}

{% block integer_widget %}
    <div class="input-wrapper">
        {{ parent() }}
        <span>ans</span>
    </div>
{% endblock %}

{{ form_widget(form.age) }}
```

## Créer un Thème Personnalisé
Créez un fichier `templates/form/my_theme.html.twig` qui étend un thème existant (ou aucun).

```twig
{# On peut étendre un thème existant #}
{% use 'bootstrap_5_layout.html.twig' %}

{% block form_row %}
    <div class="custom-row">
        {{ form_label(form) }}
        {{ form_widget(form) }}
        {{ form_errors(form) }}
    </div>
{% endblock %}
```

## 🧠 Concepts Clés
1.  **Fragment** : Chaque bloc est un petit morceau de HTML. `form_widget(form.age)` appelle le bloc `integer_widget` avec les variables du champ `age`.
2.  **Variables** : Dans un bloc de thème, vous avez accès aux variables du champ (`value`, `attr`, `id`, `full_name`...).

## ⚠️ Points de vigilance (Certification)
*   **form_theme tag** : Il doit être placé **avant** le premier rendu de champ du formulaire.
*   **Héritage** : Pour garder le comportement par défaut tout en ajoutant quelque chose, utilisez `{{ parent() }}` à l'intérieur du bloc, mais cela ne marche que si vous utilisez `extends` (rare pour les thèmes de form qui utilisent plutôt `use`).

## Ressources
*   [Symfony Docs - Form Theming](https://symfony.com/doc/current/form/form_themes.html)
*   [Form Fragment Naming](https://symfony.com/doc/current/form/form_themes.html#form-fragment-naming)
