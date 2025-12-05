# Débogage des Variables

## Concept clé
Voir le contenu d'une variable pendant le développement.

## Application dans Symfony 7.0
La fonction `dump()` (nécessite le `VarDumper` component et le `DebugBundle`).

```twig
{# Affiche le contenu dans la Web Debug Toolbar (cible) #}
{% dump user %}

{# Affiche le contenu in-line (dans le HTML) #}
{{ dump(user) }}
```

## Points de vigilance (Certification)
*   **Prod** : En environnement de production, `dump()` n'affiche rien (ou peut ne pas exister si le bundle de debug n'est pas installé).
*   **Arguments** : Sans argument, `{% dump %}` affiche toutes les variables disponibles dans le contexte courant. Utile pour découvrir ce qu'on a sous la main.

## Ressources
*   [Symfony Docs - Debugging variables](https://symfony.com/doc/current/templates.html#debugging-variables)

