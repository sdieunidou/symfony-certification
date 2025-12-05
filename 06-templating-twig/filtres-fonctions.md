# Filtres et Fonctions

## Concept clé
*   **Filtres** (`|`) : Modifient une variable. `{{ name|upper }}`.
*   **Fonctions** : Génèrent du contenu ou effectuent une action. `{{ date() }}`.

## Application dans Symfony 7.0
Symfony ajoute de nombreux filtres/fonctions à Twig natif via `TwigBridge`.

### Filtres courants
*   `length`, `upper`, `lower`, `capitalize`, `trim` (Natif Twig)
*   `date` : Formattage de date. `{{ post.publishedAt|date('d/m/Y') }}`
*   `json_encode`
*   `trans` : Traduction (Symfony).
*   `yaml_encode`, `yaml_dump` (Symfony).

### Fonctions courantes
*   `path()`, `url()` : Génération d'URLs.
*   `asset()` : Gestion des assets.
*   `dump()` : Debug.
*   `form()`, `form_row()` : Rendu de formulaire.
*   `include()`, `source()`

## Points de vigilance (Certification)
*   **Syntaxe** : Les filtres s'enchaînent. `{{ name|trim|upper }}`.
*   **Arguments** : `{{ price|number_format(2, ',', ' ') }}`.
*   **Création** : Vous pouvez créer vos propres extensions Twig (classe PHP implémentant `AbstractExtension`) pour ajouter des filtres/fonctions spécifiques. C'est une bonne pratique recommandée plutôt que de mettre de la logique complexe dans le template.

## Ressources
*   [Twig - Filters](https://twig.symfony.com/doc/3.x/filters/index.html)
*   [Twig - Functions](https://twig.symfony.com/doc/3.x/functions/index.html)

