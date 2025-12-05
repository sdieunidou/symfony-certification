# Interpolation de Chaînes

## Concept clé
L'interpolation permet d'insérer le résultat d'une expression Twig directement à l'intérieur d'une chaîne de caractères (string).
C'est l'équivalent du `"$var"` en PHP.

## Syntaxe
Utilisez `#{ expression }` à l'intérieur des guillemets doubles `"` ou simples `'`.

```twig
{{ "Bonjour #{user.firstName} #{user.lastName}" }}

{# Très utile pour construire des classes CSS dynamiques #}
<div class="alert alert-#{ type|default('info') }">
```

## Différence avec la Concaténation
En Twig, la concaténation se fait avec le tilde `~` (et non le point `.` comme en PHP).

```twig
{# Interpolation #}
{{ "Page #{page}" }}

{# Concaténation #}
{{ "Page " ~ page }}
```
Les deux méthodes sont valides et équivalentes en termes de résultat. L'interpolation est souvent plus lisible pour les chaînes complexes.

## 🧠 Concepts Clés
1.  **Conversion en String** : L'expression interpolée est convertie en chaîne. Si c'est un objet, sa méthode `__toString()` est appelée.
2.  **Contexte** : L'interpolation fonctionne partout où une chaîne est attendue (arguments de fonction, variables).

## ⚠️ Points de vigilance (Certification)
*   **Pas dans le texte** : L'interpolation ne fonctionne PAS directement dans le corps HTML.
    *   ❌ `<h1>Titre : #{title}</h1>` (Affichera littéralement `#{title}`)
    *   ✅ `<h1>Titre : {{ title }}</h1>` (C'est le but des doubles accolades)
*   L'interpolation est utile *à l'intérieur des délimiteurs Twig* `{{ ... }}` ou `{% ... %}`.

## Ressources
*   [Twig Docs - Interpolation](https://twig.symfony.com/doc/3.x/templates.html#string-interpolation)
