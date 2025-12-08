# Thématisation des Formulaires (Theming)

## Concept clé
Le "Form Theming" est le mécanisme puissant de Symfony pour personnaliser le HTML généré par les helpers de formulaire (`form_row`, `form_widget`, etc.).
Il repose sur l'héritage et la surcharge de blocs Twig.

## Architecture des Blocs (Anatomie)

Chaque partie d'un formulaire correspond à un bloc Twig.
Pour afficher une ligne complète (`form_row`), Symfony combine 4 sous-blocs :

1.  `form_label` : Le `<label>`
2.  `form_widget` : L'élément de saisie (`<input>`, `<select>`, `<textarea>`)
3.  `form_errors` : Les erreurs de validation (`<ul><li>...`)
4.  `form_help` : Le texte d'aide

## Surcharge et Conventions de Nommage

### 1. Hiérarchie des Blocs
Un formulaire est un arbre. Pour afficher un champ, Symfony cherche le bloc le plus spécifique possible.
L'ordre de recherche est (pour un champ `age` de type `IntegerType` dans un formulaire nommé `user`) :

1.  `_user_age_widget` (Spécifique à ce champ précis)
2.  `integer_widget` (Spécifique au type)
3.  `number_widget` (Parent du type)
4.  `form_widget_simple` (Type générique pour les inputs textuels)
5.  `form_widget` (Base absolue)

### 2. Les parties du champ
Chaque champ (`row`) est composé de 4 sous-parties que vous pouvez surcharger individuellement :
*   `_user_age_label`
*   `_user_age_widget`
*   `_user_age_errors`
*   `_user_age_help`
*   `_user_age_row` (Le conteneur global qui appelle les 4 autres)

## Méthodes d'Application

### 1. Thème Global (`config/packages/twig.yaml`)
Appliqué à tous les formulaires du site (ex: Bootstrap).

```yaml
twig:
    form_themes: ['bootstrap_5_layout.html.twig']
```

### 2. Thème Local (Fichier externe)
Appliqué à un formulaire spécifique via le tag `form_theme`.

```twig
{# templates/registration/register.html.twig #}
{% form_theme form 'form/my_custom_theme.html.twig' %}

{{ form_widget(form) }}
```

### 3. Thème Inline (`_self`)
Le plus rapide pour une petite modification (ex: ajouter une classe ou une icône sur un champ précis). On définit le bloc directement dans le template qui affiche le formulaire.

```twig
{% extends 'base.html.twig' %}

{% form_theme form _self %}

{# Surcharge du widget pour le champ 'zipcode' du formulaire 'address' #}
{% block _address_zipcode_widget %}
    <div class="input-group">
        <span class="input-group-text">ZIP</span>
        {# parent() affiche le <input> standard généré par Symfony #}
        {{ parent() }}
    </div>
{% endblock %}

{% block body %}
    {{ form_widget(form.zipcode) }}
{% endblock %}
```

## Création d'un Thème Personnalisé

Créez un fichier `templates/form/fields.html.twig`.

```twig
{# On peut hériter d'un thème existant pour ne modifier que ce qu'on veut #}
{% use 'bootstrap_5_layout.html.twig' %}

{# Personnalisation de tous les labels #}
{% block form_label %}
    <label class="my-custom-label" {% for attrname, attrvalue in label_attr %} {{ attrname }}="{{ attrvalue }}"{% endfor %}>
        {{ label|trans }}
        {% if required %}<span class="required">*</span>{% endif %}
    </label>
{% endblock %}
```

## Accès aux Variables

Dans un bloc de thème, vous avez accès à toutes les options du champ :
*   `value` : La valeur du champ.
*   `attr` : Les attributs HTML (`class`, `placeholder`...).
*   `id`, `name`, `full_name`.
*   `required`, `disabled`, `readonly`.
*   `errors` : Liste des erreurs.
*   `form` : L'objet `FormView`.

## 🧠 Concepts Clés
1.  **Fragment** : Chaque bloc est un petit morceau de HTML indépendant.
2.  **Priorité** : Les thèmes définis dans `form_theme` (template) surchargent les thèmes globaux (config).
3.  **Block Name** : Vous pouvez forcer le nom du bloc utilisé via l'option `block_name` dans le FormType PHP, pour partager une personnalisation entre plusieurs champs qui n'ont pas le même type.

## ⚠️ Points de vigilance (Certification)
*   **Position** : Le tag `{% form_theme %}` doit être placé **avant** le premier rendu de champ.
*   **Parent()** : Dans un thème inline (`_self`), `{{ parent() }}` fonctionne car on est dans le même contexte d'héritage. Dans un fichier externe importé via `use`, c'est plus subtil (mécanisme de traits Twig).

## Ressources
*   [Symfony Docs - Form Theming](https://symfony.com/doc/current/form/form_themes.html)
*   [Form Fragment Naming](https://symfony.com/doc/current/form/form_themes.html#form-fragment-naming)
