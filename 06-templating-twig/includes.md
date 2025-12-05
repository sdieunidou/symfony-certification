# Inclusions et Embeds (Modularité)

## Concept clé
Twig offre plusieurs mécanismes pour réutiliser des fragments de template et éviter la duplication de code. Comprendre la différence entre `include`, `embed` et `use` est crucial pour une architecture frontend propre.

## 1. `include()` (La fonction)
C'est la méthode la plus simple et la plus courante. Elle insère le contenu d'un autre template à l'endroit courant.

```twig
{# base.html.twig #}
<body>
    {{ include('partials/_header.html.twig') }}
    
    {# Avec variables spécifiques #}
    {{ include('partials/_alert.html.twig', { 
        'type': 'success', 
        'message': 'Opération réussie' 
    }) }}
</body>
```

*   **Contexte** : Par défaut, le template inclus hérite de **toutes** les variables du template parent.
*   **Isolation** : Pour empêcher cela (performance, propreté), utilisez `with_context = false`.
    *   `{{ include('...', { ... }, with_context = false) }}`.

## 2. `{% embed %}` (Le caméléon)
Le tag `embed` est une fusion entre `include` et `extends`.
Il permet d'inclure un template tout en **surchargeant ses blocs** définis. C'est l'équivalent des "Slots" ou "Components" dans d'autres frameworks JS.

**Exemple : Une Modal générique (`_modal.html.twig`)**
```twig
<div class="modal">
    <div class="header">{% block header %}Titre{% endblock %}</div>
    <div class="body">{% block body %}{% endblock %}</div>
</div>
```

**Usage dans une page :**
```twig
{% embed '_modal.html.twig' %}
    {% block header %}Confirmation de suppression{% endblock %}
    {% block body %}
        Êtes-vous sûr de vouloir supprimer cet élément ?
        <button>Oui</button>
    {% endblock %}
{% endembed %}
```

## 3. `{% use %}` (L'héritage horizontal)
C'est l'équivalent des **Traits** en PHP.
Il permet d'importer des blocs d'un autre template sans l'étendre et sans l'inclure directement. Rarement utilisé dans les projets standards, mais utilisé par le moteur de Formulaires de Symfony (`form_div_layout.html.twig`).

## 🧠 Concepts Clés
1.  **Convention** : Préfixez les templates partiels par `_` (ex: `_form.html.twig`) pour indiquer qu'ils ne sont pas des pages complètes.
2.  **Include vs Controller** :
    *   `include()` : Purement visuel. Utilise les données déjà présentes. Rapide.
    *   `render(controller())` : Exécute une logique PHP (requête DB). Plus lourd.

## ⚠️ Points de vigilance (Certification)
*   **Tag vs Fonction** : `{% include %}` est l'ancienne syntaxe (tag). `{{ include() }}` est la fonction moderne. Préférez la fonction car elle retourne une valeur et est plus flexible.
*   **Missing** : `ignore_missing: true` permet de ne pas planter si le template est absent (utile pour des thèmes dynamiques).

## Ressources
*   [Twig Docs - Include Function](https://twig.symfony.com/doc/3.x/functions/include.html)
*   [Twig Docs - Embed Tag](https://twig.symfony.com/doc/3.x/tags/embed.html)
