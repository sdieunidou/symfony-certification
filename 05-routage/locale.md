# Devinette de la Locale (Locale Guessing)

## Concept clé
Le routeur ne "devine" pas la locale tout seul, mais il permet de la gérer proprement.
Le pattern recommandé est de préfixer les URLs par `/{_locale}`.

## Application dans Symfony 7.0
Si vous configurez une route avec `path: /{_locale}/...`, le paramètre spécial `_locale` sera automatiquement utilisé pour définir la locale de la requête (`$request->setLocale()`) très tôt dans le processus.

```yaml
# config/routes.yaml
controllers:
    resource: ../src/Controller/
    type: attribute
    prefix: /{_locale}
    requirements:
        _locale: en|fr|de
```

Pour la racine `/`, on crée souvent une route sans locale qui redirige vers la locale par défaut ou devinée (voir section HTTP - Détection de langue).

## Points de vigilance (Certification)
*   **Sticky Locale** : Le routeur met à jour la locale de la requête, qui est ensuite utilisée par le service Translator.

## Ressources
*   [Symfony Docs - Localized Routes](https://symfony.com/doc/current/routing.html#localized-routes-i18n)

