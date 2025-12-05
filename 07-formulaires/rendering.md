# Rendu des Formulaires (Rendering)

## Concept clé
Le rendu des formulaires est délégué à **Twig**.
Symfony fournit un ensemble de fonctions (helpers) qui génèrent le HTML en se basant sur le "Thème" actif.

## Les Fonctions de Rendu

### 1. `form_start(form)`
*   Ouvre la balise `<form>`.
*   Gère `action`, `method`, `enctype` (upload).

### 2. `form_end(form)`
*   Ferme la balise `</form>`.
*   **Crucial** : Affiche tous les champs restants (non rendus manuellement) via `form_rest()`. C'est vital pour les champs cachés (`_token`) que l'on oublie souvent.

### 3. `form_row(form.field)` (Le standard)
Affiche le "bloc" complet pour un champ. Par défaut (Bootstrap) :
*   `<div>` wrapper
*   `form_label`
*   `form_widget` (input)
*   `form_help`
*   `form_errors`
*   `</div>`

### 4. Fonctions granulaires
Pour un contrôle total sur le HTML :
*   `form_label(form.field, 'Label custom', {'label_attr': {'class': 'foo'}}) `
*   `form_widget(form.field, {'attr': {'class': 'form-control'}}) `
*   `form_errors(form.field)`
*   `form_help(form.field)`

### 5. `form_errors(form)` (Global)
Placé en haut du formulaire, affiche les erreurs globales (celles qui ne sont pas liées à un champ spécifique).

## Exemple Complet

```twig
{{ form_start(form, {'attr': {'class': 'my-form'}}) }}
    
    <div class="error-zone">
        {{ form_errors(form) }}
    </div>

    <div class="row">
        <div class="col-md-6">
            {{ form_row(form.firstName) }}
        </div>
        <div class="col-md-6">
            {{ form_row(form.lastName) }}
        </div>
    </div>

    {# Reste du formulaire #}
    {{ form_rest(form) }}

{{ form_end(form) }}
```

## 🧠 Concepts Clés
1.  **FormView** : L'objet passé à Twig (`createForm()->createView()`) est une `FormView`. Ce n'est pas l'objet `Form` PHP. Il est optimisé pour l'affichage.
2.  **Variables** : Chaque champ dans la vue a des variables (`required`, `disabled`, `value`, `attr`) que les helpers utilisent.

## ⚠️ Points de vigilance (Certification)
*   **Attributs** : Vous pouvez surcharger les attributs HTML directement dans Twig : `form_widget(form.name, {'attr': {'class': 'foo'}})`. Cela fusionne avec les attributs définis dans la classe PHP.
*   **Label** : `form_label(form.name, null)` utilise le label par défaut. `form_label(form.name, false)` désactive l'affichage du label.

## Ressources
*   [Symfony Docs - Rendering Forms](https://symfony.com/doc/current/form/form_customization.html)
