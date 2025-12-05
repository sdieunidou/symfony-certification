# Débogage du Routeur

## Concept clé
Comprendre pourquoi une route ne matche pas ou quelle route matche une URL donnée.

## Commandes Utiles (Certification)

*   `php bin/console debug:router` : Liste toutes les routes (Nom, Method, Path).
*   `php bin/console debug:router --show-controllers` : Affiche aussi vers quel contrôleur ça pointe.
*   `php bin/console router:match /blog/my-slug` : Simule une requête et vous dit quelle route matche (ou pourquoi ça ne matche pas). Indispensable pour le débug.

## Points de vigilance (Certification)
*   **Cache** : En prod, le routing est compilé et mis en cache. Si vous modifiez des routes et que rien ne change, videz le cache (`cache:clear`). En dev, c'est automatique.
*   **Ordre** : Si `router:match` vous donne une route inattendue, c'est probablement un problème d'ordre (une route trop générique placée avant).

## Ressources
*   [Symfony Docs - Debugging Routes](https://symfony.com/doc/current/routing.html#debugging-routes)

