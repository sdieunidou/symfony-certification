# Traductions et Pluralisation (Twig)

## Concept clé
Afficher du texte dans la langue de l'utilisateur.

## Application dans Symfony 7.0
Le filtre `|trans` est l'outil principal.

```twig
{# Simple #}
<h1>{{ 'Hello World'|trans }}</h1>

{# Avec paramètres #}
<p>{{ 'Hello %name%'|trans({ '%name%': user.name }) }}</p>

{# Domaine spécifique (fichier messages.fr.yaml par défaut) #}
<button>{{ 'Delete'|trans({}, 'admin') }}</button>
```

### Tag {% trans %}
Pour les blocs de texte plus longs.

```twig
{% trans %}
    Hello %name%!
    Welcome to our site.
{% endtrans %}
```

### Pluralisation
Symfony gère la pluralisation via le format ICU MessageFormat (recommandé depuis Symfony 6+).

```yaml
# translations/messages.fr.yaml
apples_count: '{count, plural, =0 {Aucune pomme} one {Une pomme} other {# pommes}}'
```

```twig
{{ 'apples_count'|trans({ 'count': nb_apples }) }}
```

## Points de vigilance (Certification)
*   **Domaines** : Par défaut, Symfony cherche dans `messages.LOCALE.yaml`. Si vous spécifiez un domaine (ex: `validators`), il cherchera dans `validators.LOCALE.yaml`.
*   **Extraction** : La commande `php bin/console translation:extract` scanne vos templates Twig à la recherche des tags `trans` pour générer les fichiers de traduction.

## Ressources
*   [Symfony Docs - Translations](https://symfony.com/doc/current/translation.html)

