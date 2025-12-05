# Thématisation des Formulaires (Theming)

## Concept clé
Modifier le HTML généré par les fonctions `form_row`, `form_widget`, etc.
Symfony utilise des "Themes" Twig qui contiennent des blocs définissant le HTML de chaque type de champ.

## Application dans Symfony 7.0
On peut appliquer un thème :
1.  **Globalement** (config/packages/twig.yaml) : `form_themes: ['bootstrap_5_layout.html.twig']`.
2.  **Par template** : `{% form_theme form 'my_theme.html.twig' %}`.
3.  **Inline** : `{% form_theme form _self %}` et définir les blocs dans le même fichier.

### Exemple de surcharge (Override)
```twig
{% form_theme form _self %}

{% block text_widget %}
    <div class="input-group">
        <span class="icon">A</span>
        {# parent() appelle le rendu par défaut #}
        {{ parent() }}
    </div>
{% endblock %}

{{ form_row(form.username) }}
```

## Points de vigilance (Certification)
*   **Hiérarchie** : Pour surcharger un widget spécifique, le nom du bloc suit la hiérarchie du type. `_product_name_widget` (champ 'name' du formulaire 'product') > `text_widget` (type 'text') > `form_widget` (base).
*   **Bootstrap** : Symfony intègre nativement les thèmes Bootstrap 5.

## Ressources
*   [Symfony Docs - Form Theming](https://symfony.com/doc/current/form/form_themes.html)

