# Échappement Automatique (Auto Escaping)

## Concept clé
Pour protéger contre les failles XSS (Cross-Site Scripting), Twig échappe automatiquement toutes les variables affichées via `{{ }}`.
Si `var` contient `<script>alert(1)</script>`, Twig affichera `&lt;script&gt;...`.

## Application dans Symfony 7.0
L'échappement est activé par défaut (stratégie `html`).

### Désactiver l'échappement (Raw)
Si vous êtes sûr que la variable contient du HTML sûr (ex: généré par un éditeur WYSIWYG nettoyé).

```twig
{{ content|raw }}
```

### Échappement manuel
```twig
{{ user_input|escape }} {# Par défaut html #}
{{ user_input|e('js') }} {# Pour insertion dans du JS #}
{{ user_input|e('css') }}
{{ user_input|e('url') }}
```

## Points de vigilance (Certification)
*   **Contexte** : L'échappement dépend du contexte (HTML par défaut). Si vous générez du Javascript, utilisez `|e('js')`.
*   **Performance** : L'échappement a un coût minime.
*   **Désactivation globale** : On peut désactiver l'auto-échappement dans un bloc `{% autoescape false %}` (Déconseillé).

## Ressources
*   [Twig - Autoescaping](https://twig.symfony.com/doc/3.x/tags/autoescape.html)

