# Syntaxe Twig (v3.8)

## Concept clé
Twig est le moteur de template par défaut de Symfony. Il est concis, sécurisé et extensible.
Il utilise trois délimiteurs principaux :
1.  `{{ ... }}` : Pour afficher quelque chose (output).
2.  `{% ... %}` : Pour la logique (boucles, conditions, blocs).
3.  `{# ... #}` : Pour les commentaires (non rendus en HTML).

## Application dans Symfony 7.0
Symfony 7.0 utilise Twig 3.8.

### Affichage
```twig
<h1>{{ page_title }}</h1>
<p>{{ user.name }}</p> {# user.getName() ou $user['name'] #}
<p>{{ user.isActive ? 'Actif' : 'Inactif' }}</p>
```

### Logique
```twig
{% set foo = 'bar' %}
{% if foo == 'bar' %}
    <p>C'est bar</p>
{% endif %}
```

## Points de vigilance (Certification)
*   **Accès aux attributs** : Le point (`.`) est magique. `foo.bar` essaie (dans l'ordre) :
    1.  `$foo['bar']` (si array)
    2.  `$foo->bar` (propriété)
    3.  `$foo->bar()` (méthode)
    4.  `$foo->getBar()` (getter)
    5.  `$foo->isBar()` (issser)
    6.  `$foo->hasBar()` (hasser)
*   **Strict variables** : En dev, Twig lance une erreur si vous accédez à une variable inexistante. En prod, il retourne `null` (silencieusement).
*   **Whitespace control** : `{{- value -}}` supprime les espaces blancs avant et après.

## Ressources
*   [Twig Documentation](https://twig.symfony.com/doc/3.x/)

