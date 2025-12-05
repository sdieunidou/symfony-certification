# Inclusion de Templates

## Concept clé
Inclure un fragment de template dans un autre pour éviter la duplication (ex: un widget sidebar, un footer).

## Application dans Symfony 7.0
Deux méthodes principales : le tag `{% include %}` et la fonction `include()`. La fonction est plus flexible.

```twig
{# Tag (syntaxe historique) #}
{% include 'partials/_sidebar.html.twig' %}

{# Fonction (recommandé) #}
{{ include('partials/_sidebar.html.twig') }}

{# Passer des variables spécifiques #}
{{ include('partials/_card.html.twig', { 'title': 'My Title', 'content': '...' }) }}

{# Avec only pour désactiver l'accès aux variables du parent #}
{{ include('partials/_card.html.twig', { 'item': item }, with_context = false) }}
```

## Points de vigilance (Certification)
*   **Contexte** : Par défaut, le template inclus a accès à toutes les variables du template parent.
*   **Missing** : Si le template n'existe pas, Twig lance une erreur. On peut utiliser `ignore_missing = true`.
*   **Embed** : Le tag `{% embed %}` est une combinaison de `include` et `extends`. Il permet d'inclure un template tout en surchargeant ses blocs à la volée. Puissant mais plus complexe.

## Ressources
*   [Twig - Include function](https://twig.symfony.com/doc/3.x/functions/include.html)

