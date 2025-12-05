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

Vous avez accès aux variables `status_code` et `status_text`.

## Prévisualisation en Dev
Comme vous ne voyez jamais les pages d'erreur "Prod" en environnement "Dev" (vous voyez la stack trace), Symfony fournit des routes spéciales pour les tester :
*   `/_error/404`
*   `/_error/500`
*   `/_error/403`

## 🧠 Concepts Clés
1.  **Event** : Le mécanisme repose sur l'événement `kernel.exception` (ou `ExceptionEvent`).
2.  **JSON** : Si la requête demande du JSON (Accept header), le ErrorHandler essayera de retourner du JSON (sérialisation du problème via `symfony/serializer` si présent).

## ⚠️ Points de vigilance (Certification)
*   **Erreur 500 en prod** : Si une erreur survient *pendant* le rendu de la page d'erreur 500 (ex: bug dans `error500.html.twig`), Symfony affiche une page HTML de secours minimaliste (hardcodée en PHP) pour éviter la page blanche.
*   **Logs** : Toutes les exceptions sont logguées (critical pour 500, error pour 400).

## Ressources
*   [Symfony Docs - Custom Error Pages](https://symfony.com/doc/current/controller/error_pages.html)
