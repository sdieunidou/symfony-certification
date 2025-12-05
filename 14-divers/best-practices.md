# Bonnes Pratiques Officielles (Best Practices)

## Concept clé
Le guide "Best Practices" définit la manière standard de développer avec Symfony pour garantir maintenabilité, concision et efficacité. C'est un équilibre pragmatique, loin du dogmatisme "pureté académique".

## 1. Création du Projet
*   **Symfony CLI** : Utilisez `symfony new my_project` (et non Composer directement).
*   **Structure** : Gardez la structure par défaut (`src/Controller`, `src/Entity`, etc.). N'essayez pas de la réinventer sauf contrainte majeure (DDD avancé).

## 2. Configuration
*   **Infrastructure** (DB, Redis) : Variables d'environnement (`.env`).
*   **Secrets** (Clés API) : Utilisez le **Secrets Vault** (`bin/console secrets:set`).
*   **Application** (Features, Email sender) : Paramètres dans `services.yaml` (`parameters:`).
    *   Nommage : Utilisez `app.` en préfixe, snake_case (`app.admin_email`).
*   **Constantes** : Pour les valeurs qui changent rarement (ex: nombre d'items par page), utilisez des constantes de classe (`public const`) plutôt que des paramètres globaux.

## 3. Logique Métier (Business Logic)
*   **Pas de Bundles** : Ne créez pas de bundles pour organiser votre code applicatif (`UserBundle`, `AppBundle`). Utilisez les Namespaces PHP standards dans `src/`.
*   **Services** :
    *   Utilisez l'**Autowiring** et l'**Autoconfiguration**.
    *   Les services doivent être **Privés** (par défaut).
    *   Format de config : **YAML** pour les services (concis), **PHP** pour les cas complexes.
    *   **Mapping Doctrine** : Utilisez les **Attributs PHP**.

## 4. Contrôleurs
*   **AbstractController** : Étendez la classe de base `AbstractController`. Bien que cela couple votre code au framework, c'est acceptable pour la couche contrôleur (qui est de toute façon couplée à HTTP).
*   **Attributs** : Utilisez les attributs pour le Routing, le Cache, et la Sécurité (`#[Route]`, `#[IsGranted]`).
*   **Injection** : Utilisez l'injection de dépendances (Constructeur ou Action) pour récupérer les services. N'utilisez pas `$this->container->get()`.
*   **EntityValueResolver** : Utilisez le ParamConverter automatique pour récupérer les entités depuis l'ID dans l'URL.

## 5. Templates (Twig)
*   **Nommage** : snake_case pour les fichiers et variables (`user_profile.html.twig`, `user_profile`).
*   **Partials** : Préfixez les fragments de template par un underscore (`_form.html.twig`, `_menu.html.twig`).

## 6. Formulaires
*   **Classes PHP** : Définissez vos formulaires dans des classes (`App\Form\RegistrationType`), ne les construisez pas dans le contrôleur.
*   **Boutons** : Ajoutez les boutons (`submit`) dans le **Template Twig**, pas dans la classe PHP (car le label/style du bouton dépend du contexte d'affichage, pas des données). *Exception : Formulaires avec plusieurs boutons d'action différents.*
*   **Validation** : Définissez les contraintes sur l'objet sous-jacent (Entité/DTO) via des Attributs, pas dans le FormType.
*   **Action Unique** : Utilisez la même route/action pour afficher et traiter le formulaire.

## 7. Internationalisation (i18n)
*   **Format** : Utilisez **XLIFF** (`.xlf`) pour les traductions (standard industriel, validation XML).
*   **Clés** : Utilisez des clés sémantiques (`label.username`) plutôt que des chaînes de contenu (`Username`).

## 8. Sécurité
*   **Firewall Unique** : Essayez d'avoir un seul firewall (`main`) sauf si vous avez une API complètement distincte (stateless) et un Front (stateful).
*   **Hasher** : Utilisez `auto` comme algorithme de hashage (Symfony choisira le meilleur, ex: Argon2id ou Bcrypt).
*   **Voters** : Utilisez des Voters pour les règles de permission fines et complexes.

## 9. Web Assets
*   **AssetMapper** : Utilisez le composant AssetMapper (nouveauté Symfony 6.3+) pour gérer le JS/CSS moderne sans complexité Node.js/Webpack, si possible.

## 10. Tests
*   **Smoke Testing** : Commencez par un test fonctionnel simple qui vérifie que toutes les pages publiques répondent 200 OK (via un DataProvider).
*   **URLs en dur** : Dans les tests fonctionnels, utilisez les URLs en dur (`/login`) plutôt que la génération de route. Si l'URL change, le test doit casser pour vous alerter (car les liens externes/bookmarks des utilisateurs casseront aussi).

## Ressources
*   [Symfony Best Practices (Doc Officielle)](https://symfony.com/doc/current/best_practices.html)
