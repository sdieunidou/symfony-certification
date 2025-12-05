# Transformateurs de Données (Data Transformers)

## Concept clé
Le formulaire manipule 3 formats de données :
1.  **Model Data** : Format dans votre objet (ex: objet `Tag`).
2.  **Norm Data** : Format normalisé (ex: string "Tag1").
3.  **View Data** : Format affiché dans le champ (ex: string "Tag1").

Un Data Transformer convertit entre Model et Norm (ou Norm et View).
C'est utile quand le format de stockage diffère du format de saisie.
Exemple : Saisir un numéro de dossier "AB-123" (string) qui correspond à l'entité Dossier ID 123.

## Application dans Symfony 7.0
Implémenter `DataTransformerInterface` : `transform()` (Model -> View) et `reverseTransform()` (View -> Model).

```php
$builder->get('tags')
    ->addModelTransformer(new CallbackTransformer(
        // Transform : Array tags -> String "tag1, tag2"
        fn ($tagsAsArray) => implode(', ', $tagsAsArray),
        
        // Reverse Transform : String "tag1, tag2" -> Array tags
        fn ($tagsAsString) => explode(', ', $tagsAsString)
    ));
```

## Points de vigilance (Certification)
*   **Validation** : Si le reverse transform échoue (ex: ID introuvable), il doit lancer une `TransformationFailedException`. Cette exception est capturée par le formulaire et transformée en erreur de validation sur le champ.
*   **Model vs View** : `addViewTransformer` est plus rare, utilisé pour formater l'affichage (ex: afficher 10000 comme 10 000).

## Ressources
*   [Symfony Docs - Data Transformers](https://symfony.com/doc/current/form/data_transformers.html)

