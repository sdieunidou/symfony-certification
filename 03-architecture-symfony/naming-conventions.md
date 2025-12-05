# Conventions de Nommage

## Concept clé
Symfony suit les standards **PSR** (PHP-FIG) et ajoute ses propres conventions pour garantir une uniformité dans l'écosystème. Un code respectant ces conventions est immédiatement compréhensible par tout développeur Symfony.

## Code PHP (PSR-1 / PSR-12)
*   **Classes** : `UpperCamelCase` (PascalCase). Ex: `UserProfile`.
*   **Méthodes** : `lowerCamelCase`. Ex: `getFirstName`.
*   **Propriétés** : `lowerCamelCase`. Ex: `$createdAt`.
*   **Constantes** : `UPPER_SNAKE_CASE`. Ex: `MAX_ATTEMPTS`.
*   **Namespaces** : Correspondent à l'arborescence (PSR-4). `App\Controller\Admin`.

## Conventions Spécifiques Symfony

### Services (Injection de Dépendances)
*   **ID de Service** : Utilisez le **FQCN** (Fully Qualified Class Name) par défaut.
    *   Bon : `App\Service\Mailer`
    *   Obsolète/Réservé : `app.mailer` (snake_case utilisé pour les paramètres ou les alias courts).
*   **Paramètres** : `snake_case`. Ex: `app.admin_email`.

### Routing & URLs
*   **Noms de routes** : `snake_case`.
    *   Recommandé : `app_entity_action` (ex: `app_blog_show`, `api_user_list`).
    *   Le préfixe `app_` évite les conflits avec les routes des bundles tiers.
*   **URLs** : `kebab-case` (minuscules avec tirets).
    *   Bon : `/blog/my-awesome-post`
    *   Mauvais : `/blog/My_Awesome_Post`

### Templates
*   **Noms de fichiers** : `snake_case`.
    *   Ex: `user_profile.html.twig`.
*   **Emplacement** : `templates/{controller_name}/{action_name}.html.twig`.

### Configuration
*   **Clés YAML** : `snake_case`.
*   **Variables d'env** : `UPPER_SNAKE_CASE`. Ex: `DATABASE_URL`.

## Suffixes de Classes (Sémantique)
Le nom de la classe doit indiquer son type/rôle.

| Type | Suffixe | Exemple |
| :--- | :--- | :--- |
| Contrôleur | `Controller` | `BlogController` |
| Entité | (Aucun) | `User`, `Product` |
| Repository | `Repository` | `UserRepository` |
| Commande CLI | `Command` | `CreateUserCommand` |
| Écouteur | `Listener` / `Subscriber` | `ExceptionListener` |
| Formulaire | `Type` | `RegistrationType` |
| Sécurité | `Voter` | `PostVoter` |
| Extension Twig | `Extension` | `AppExtension` |
| Exception | `Exception` | `UserNotFoundException` |
| Interface | `Interface` | `UserInterface` |
| Trait | `Trait` | `TimestampableTrait` |

## 🧠 Concepts Clés
1.  **Prédictibilité** : Si je cherche le Voter pour les produits, je tape `ProductVoter` dans mon IDE (Ctrl+N) et je le trouve immédiatement.
2.  **Autoconfiguration** : Symfony se base souvent sur l'implémentation d'interface (`EventSubscriberInterface`) plutôt que sur le nom ou le dossier, mais le nommage aide les humains.

## ⚠️ Points de vigilance (Certification)
*   **Singulier vs Pluriel** :
    *   Entités : **Singulier** (`Product`, pas `Products`). Une instance représente *un* produit.
    *   Tables DB : Souvent pluriel (`products`) ou singulier (`product`) selon les conventions d'équipe, mais l'entité PHP reste singulière.
    *   URLs REST : Pluriel pour les collections (`/products`), singulier+id pour les éléments (`/products/{id}`).

## Ressources
*   [Symfony Coding Standards](https://symfony.com/doc/current/contributing/code/standards.html)
*   [PSR-12: Extended Coding Style](https://www.php-fig.org/psr/psr-12/)
