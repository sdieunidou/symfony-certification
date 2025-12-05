# Conventions de Nommage

## Concept clé
Suivre des conventions strictes rend le code prédictible et navigable. Symfony suit les standards PSR et ajoute ses propres conventions.

## Conventions Symfony
1.  **Classes** : UpperCamelCase (`UserProfile`).
2.  **Méthodes/Propriétés** : lowerCamelCase (`isValid`, `$firstName`).
3.  **Constantes** : UPPER_SNAKE_CASE (`DEFAULT_LIMIT`).
4.  **Fichiers** : Même nom que la classe (`UserProfile.php`).
5.  **Namespaces** : `Vendor\Project\Category\...` (ex: `App\Controller\Admin`).
6.  **Services** : snake_case pour les IDs (ex: `app.msg_sender`), mais l'utilisation du FQCN (Fully Qualified Class Name) est recommandée comme ID (`App\Service\MsgSender`).
7.  **Templates** : snake_case (`user_profile.html.twig`).
8.  **Routes** : snake_case (`app_blog_show`).
9.  **Paramètres de config** : snake_case (`app.items_per_page`).

## Suffixes
Symfony utilise des suffixes explicites pour indiquer le rôle d'une classe :
*   `...Controller`
*   `...Command`
*   `...Listener` / `...Subscriber`
*   `...Type` (Formulaires)
*   `...Voter` (Sécurité)
*   `...Repository` (Doctrine)
*   `...Interface`
*   `...Trait`
*   `...Exception`

## Points de vigilance (Certification)
*   **Services** : Depuis Symfony 4, l'ID du service est par défaut le nom de la classe. Les IDs en snake_case (`app.mailer`) sont réservés aux alias ou aux services qui n'ont pas de classe propre (configurations).
*   **Routes** : Il est recommandé de préfixer les noms de routes (ex: `app_admin_post_show`) pour éviter les collisions.

## Ressources
*   [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)

