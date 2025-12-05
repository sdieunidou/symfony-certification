# Espaces de Noms (Namespaces)

## Concept clé
Par défaut, Twig cherche les templates dans `templates/`.
Les **Namespaces** permettent de grouper des templates sous un alias logique (ex: `@Email`, `@Admin`), indépendamment de leur dossier physique. C'est aussi comme ça que les Bundles exposent leurs templates.

## Configuration (`twig.paths`)
Dans `config/packages/twig.yaml` :

```yaml
twig:
    paths:
        # Clé (Dossier) : Valeur (Namespace)
        'assets/templates': 'theme' 
        'src/Domain/Invoice/Templates': 'Invoice'
```

## Utilisation
Utilisez le préfixe `@` suivi du nom du namespace.

```twig
{{ include('@theme/header.html.twig') }}
{{ include('@Invoice/pdf/layout.html.twig') }}
```

## Templates de Bundles
Symfony enregistre automatiquement un namespace pour chaque Bundle installé.
*   Bundle : `AcmeBlogBundle`
*   Namespace : `@AcmeBlog`
*   Chemin par défaut : `vendor/acme/blog-bundle/templates/`

Pour surcharger un template de bundle, créez le fichier dans `templates/bundles/AcmeBlogBundle/`.

## 🧠 Concepts Clés
1.  **Priorité** : Symfony regarde d'abord dans `templates/` (sans namespace), puis dans les namespaces configurés.
2.  **Surcharge** : Si plusieurs chemins sont mappés au même namespace, Twig cherche dans l'ordre. C'est ce qui permet de surcharger les templates de bundles.

## ⚠️ Points de vigilance (Certification)
*   **Notation** : `@Namespace/fichier.html.twig`. Pas de slash au début.
*   **Debug** : Utilisez `php bin/console debug:twig` pour voir tous les namespaces enregistrés et leurs chemins physiques.

## Ressources
*   [Symfony Docs - Template Namespaces](https://symfony.com/doc/current/templates.html#template-namespaces)
