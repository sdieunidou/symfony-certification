# Gestion des Erreurs (ErrorHandler)

## Concept clé
Le composant `ErrorHandler` gère la capture des erreurs PHP (Exceptions et erreurs natives) pour les transformer en réponse HTTP contrôlée, plutôt qu'en page blanche ou erreur serveur brute.

## Fonctionnement
1.  **Boot** : Le `Debug::enable()` (dans `index.php`) enregistre les handlers globaux de PHP.
2.  **Capture** : Si une erreur survient, elle est convertie en `Exception`.
3.  **Rendu** :
    *   En **Dev** : Une page HTML riche avec la stack trace, les logs, les arguments (Ghost page).
    *   En **Prod** : Une page d'erreur générique ("Oops! An Error Occurred").

## Personnalisation (Twig)
Pour changer le look des pages d'erreur en production (404, 403, 500), il suffit de créer des templates Twig spécifiques.
Symfony (TwigBundle) cherche dans `templates/bundles/TwigBundle/Exception/`.

*   `error404.html.twig`
*   `error403.html.twig`
*   `error500.html.twig` (Erreur critique)
*   `error.html.twig` (Fallback pour tous les autres codes)

### Variables disponibles
Dans ces templates, vous avez accès à :
*   `status_code` : Le code HTTP (ex: 404).
*   `status_text` : Le message standard (ex: "Not Found").
*   `exception` : L'objet exception (attention à ne pas afficher de données sensibles en prod via `exception.message` ou `exception.trace` sans filtrage).

```twig
{% extends 'base.html.twig' %}

{% block body %}
    <h1>Erreur {{ status_code }}</h1>
    <p>Oups ! {{ status_text }}</p>
    <a href="{{ path('homepage') }}">Retour à l'accueil</a>
{% endblock %}
```

## Prévisualisation en Dev
Comme vous ne voyez jamais les pages d'erreur "Prod" en environnement "Dev" (vous voyez la stack trace), Symfony fournit des routes spéciales pour les tester.

Si besoin, configurez la route dans `config/routes/dev/framework.yaml` (automatique avec Flex) :
```yaml
when@dev:
    _errors:
        resource: '@FrameworkBundle/Resources/config/routing/errors.php'
        prefix: /_error
```

URLs de test :
*   `/_error/404`
*   `/_error/500`
*   `/_error/403`

## Pages d'Erreur Statiques (Symfony 7.3+)
Pour les erreurs critiques (ex: PHP ne démarre pas, Base de données down, Erreur 500 fatale), Symfony ne peut même pas rendre le template Twig.
La solution est de pré-générer des pages HTML statiques que le serveur Web (Nginx/Apache) servira directement.

**Commande** : `error:dump`
```bash
# Génère les pages pour tous les codes d'erreur
APP_ENV=prod php bin/console error:dump var/cache/prod/error_pages/

# Génère seulement certaines pages
APP_ENV=prod php bin/console error:dump var/cache/prod/error_pages/ 404 500
```
Ensuite, configurez votre serveur web (Nginx/Apache) pour utiliser ces fichiers HTML en cas d'erreur (ErrorDocument).

## 🧠 Concepts Clés
1.  **Event** : Le mécanisme repose sur l'événement `kernel.exception` (ou `ExceptionEvent`). Vous pouvez écouter cet événement pour loguer l'erreur ou rediriger l'utilisateur avant le rendu de la page d'erreur.
2.  **JSON** : Si la requête demande du JSON (Accept header), le ErrorHandler essayera de retourner du JSON (sérialisation du problème via `symfony/serializer` si présent).

## ⚠️ Points de vigilance (Certification)
*   **Erreur 500 en prod** : Si une erreur survient *pendant* le rendu de la page d'erreur 500 (ex: bug dans `error500.html.twig`), Symfony affiche une page HTML de secours minimaliste (hardcodée en PHP) pour éviter la page blanche.
*   **Logs** : Toutes les exceptions sont logguées (critical pour 500, error pour 400).

## Ressources
*   [Symfony Docs - Custom Error Pages](https://symfony.com/doc/current/controller/error_pages.html)
*   [Symfony Blog - Static Error Pages](https://symfony.com/blog/new-in-symfony-7-3-static-error-pages)
