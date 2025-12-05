# Gestion des Erreurs

## Concept clé
Gérer les exceptions fatales et les erreurs PHP de manière élégante.

## Application dans Symfony 7.0
Le composant **ErrorHandler** enregistre les gestionnaires globaux (`set_error_handler`, `set_exception_handler`).
En mode `dev`, il affiche la page d'erreur riche ("Ghost page" ou "Exception page").
En mode `prod`, il affiche une page d'erreur générique (Error 500).

### Personnalisation
Pour personnaliser les pages d'erreur (404, 500), surchargez les templates Twig dans `templates/bundles/TwigBundle/Exception/`.
*   `error404.html.twig`
*   `error500.html.twig`
*   `error.html.twig` (Fallback)

## Points de vigilance (Certification)
*   **Preview** : On peut prévisualiser les pages d'erreur en dev via les routes `/_error/404`, `/_error/500`.
*   **JSON** : Si le format de la requête est JSON, Symfony retourne automatiquement une réponse JSON d'erreur (via le `Serializer` si installé).

## Ressources
*   [Symfony Docs - Error Handling](https://symfony.com/doc/current/controller/error_pages.html)

