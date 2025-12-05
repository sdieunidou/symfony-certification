# Interpolation de Chaînes

## Concept clé
Insérer des expressions dans une chaîne de caractères, comme les guillemets doubles `""` en PHP.

## Application dans Symfony 7.0
Twig utilise la syntaxe `#{ expression }` à l'intérieur des chaînes délimitées par `"` ou `'`.

```twig
{{ "Bonjour #{user.name}, il est #{'now'|date('H:i')}" }}

{# Utile pour les noms de classes dynamiques #}
<div class="alert alert-{{ level }}">...</div>
{# est équivalent à #}
<div class="alert alert-#{level}">...</div> 
{# Note: en Twig pur, la concaténation se fait avec ~ #}
<div class="alert alert-{{ level ~ (isActive ? ' active' : '') }}">
```

## Points de vigilance (Certification)
*   **Syntaxe** : Fonctionne uniquement à l'intérieur d'une chaîne. `{{ #{variable} }}` est invalide.
*   **Concaténation** : L'opérateur de concaténation en Twig est le tilde `~` (comme en Perl), pas le point `.` (accès attribut) ni le plus `+` (addition mathématique).

## Ressources
*   [Twig - Interpolation](https://twig.symfony.com/doc/3.x/templates.html#string-interpolation)

