# Macros

## Concept clé
Les **Macros** sont l'équivalent des **fonctions** en PHP, mais pour les templates Twig.
Elles permettent de définir des morceaux de HTML réutilisables, paramétrables, pour éviter la répétition de code (DRY).

Elles sont différentes des **includes** car elles acceptent des arguments typés et sont isolées (scope).

## Définition d'une Macro

On utilise la balise `{% macro %}`.

```twig
{# templates/macros/forms.html.twig #}

{% macro input(name, value = "", type = "text", size = 20) %}
    <div class="form-group">
        <input type="{{ type }}" name="{{ name }}" value="{{ value|e }}" size="{{ size }}">
    </div>
{% endmacro %}
```

## Utilisation

### 1. Importation
Pour utiliser une macro, il faut d'abord l'importer via `{% import %}`.

```twig
{# templates/contact.html.twig #}

{% import "macros/forms.html.twig" as forms %}

<form action="#" method="post">
    {{ forms.input('username') }}
    {{ forms.input('password', type='password') }}
</form>
```

### 2. Importation sélective (`from`)
On peut importer seulement certaines macros spécifiques.

```twig
{% from "macros/forms.html.twig" import input as field %}

{{ field('username') }}
```

### 3. Macro locale (même fichier)
Si la macro est définie dans le même fichier que son utilisation, on utilise la variable spéciale `_self`.

```twig
{% macro badge(status) %}
    <span class="badge">{{ status }}</span>
{% endmacro %}

{# Utilisation immédiate #}
{{ _self.badge('Active') }}
```

## Contexte et Scope (Isolation)

Contrairement aux `include`, les macros **n'ont pas accès** aux variables du template courant (comme `user`, `app`).
C'est une isolation volontaire pour garantir que la macro est autonome.

Si vous avez besoin d'une variable globale dans la macro, vous **devez** la passer en argument.

```twig
{# Mauvais (ne marche pas) #}
{% macro welcome() %}
    Bonjour {{ app.user.name }}
{% endmacro %}

{# Bon #}
{% macro welcome(user) %}
    Bonjour {{ user.name }}
{% endmacro %}
```

> **Note** : Il existe une variable spéciale `_context` qui contient tout le contexte, mais l'utiliser dans une macro brise le principe d'isolation.

## Macros et Blocs

Les macros ne peuvent pas contenir de `{% block %}`. Si vous avez besoin d'héritage ou de redéfinition, utilisez l'héritage de template standard ou les **Twig Components** (Symfony UX) qui sont plus modernes et flexibles que les macros pour les composants UI complexes.

## 🧠 Concepts Clés
1.  **Réutilisabilité** : Parfait pour générer des éléments répétitifs (menus, pagination, champs simples).
2.  **Isolation** : Pas d'accès au contexte global (sauf si passé en argument).
3.  **Alternative** : Pour des composants UI complexes avec de la logique, préférez **Twig Components**.

## ⚠️ Points de vigilance (Certification)
*   **Import** : L'import se fait dans une variable (`as forms`). On appelle ensuite `forms.nomMacro()`.
*   **_self** : Sert à appeler une macro définie dans le fichier courant.
*   **Arguments** : Supportent les valeurs par défaut (`param = "default"`).

## Ressources
*   [Twig Docs - Macros](https://twig.symfony.com/doc/3.x/tags/macro.html)
