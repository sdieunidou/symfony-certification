# Bonnes Pratiques Officielles (Best Practices)

## Concept clé
Symfony a évolué d'un framework "Configuration over Convention" (v2/3) vers un équilibre pragmatique favorisant la productivité développeur. Le guide "Best Practices" définit la manière standard de développer pour réduire la complexité.

## Les Règles d'Or Symfony 7

### 1. Configuration & Environnement
*   **Infrastructure** (DB, Redis, API Keys) : Variables d'environnement (`.env`). Utiliser le composant **Secrets** pour la prod (`bin/console secrets:set`).
*   **Comportement** (Pagination, Features flags) : Paramètres de service (`services.yaml` -> `bind`).
*   **Constantes** : Utiliser des constantes de classe (`public const`) pour les valeurs métier stables, pas des paramètres globaux.

### 2. Logique Métier
*   **Fat Service, Skinny Controller** : Le contrôleur ne doit faire que le passe-plat (Reçoit Request -> Appelle Service -> Renvoie Response).
*   **Services** : Doivent être **Stateless** (pas d'état utilisateur stocké dans les propriétés).
*   **Repositories** : Doivent retourner des collections d'objets ou des itérateurs, pas de la logique métier complexe.

### 3. Injection de Dépendances
*   **Autowiring** : À utiliser partout (`autowire: true`).
*   **Autoconfiguration** : À utiliser partout (`autoconfigure: true`).
*   **Private Services** : Tous les services sont privés par défaut (non récupérables via `$container->get()`).
*   **Constructeur** : Seul mode d'injection recommandé (pas de Setter Injection ou Property Injection sauf cas rares comme dépendances cycliques).

### 4. Modèles & Données
*   **Doctrine** : Utiliser les **Attributs PHP** pour le mapping.
*   **ParamConverter** : Utiliser l'injection d'entité dans le contrôleur (automatique via `DoctrineParamConverter`).

### 5. Templates & UI
*   **Twig** : Moteur de template unique.
*   **Logique** : Aucune requête DB ou logique complexe dans Twig. Passer les données pré-calculées depuis le contrôleur ou utiliser des **Twig Components** / **Twig Extensions**.

### 6. Routing
*   **Attributs** : Utiliser `#[Route]` directement sur les méthodes de contrôleur. C'est plus lisible (Code + Route au même endroit).
*   **Nommage** : Utiliser snake_case pour les noms de routes (`app_blog_show`).

## Structure de Projet

| Dossier | Usage |
| :--- | :--- |
| `src/Entity` | Modèle de données (Doctrine). |
| `src/Repository` | Requêtes DB. |
| `src/Form` | Classes de formulaires (`Type`). |
| `src/Controller` | Points d'entrée HTTP. |
| `src/Security` | Voters, Authenticators, User. |
| `src/Service` | (Optionnel) Fourre-tout pour la logique métier, mais préférez des dossiers métier (`src/Invoice`, `src/Catalog`) en architecture hexagonale/DDD. |

## 🧠 Concepts Clés
1.  **Maker Bundle** : Utilisez `php bin/console make:...` pour générer du code. Il génère du code qui suit les bonnes pratiques actuelles.
2.  **Linter** : Utilisez `php-cs-fixer` avec les règles `@Symfony` pour garantir le style de code.
3.  **Performance** : N'optimisez pas prématurément. Suivez les bonnes pratiques, profilez avec Blackfire/Profiler, puis optimisez.

## ⚠️ Points de vigilance (Certification)
*   **Formats** : L'examen teste souvent "Quel format est recommandé ?". Réponse : **Attributs PHP** pour Routing/Doctrine/Validation/Serializer. (XML est pour les bundles tiers, YAML pour la config globale framework).
*   **Services Publics** : Ne rendez jamais un service public "juste pour le tester". Utilisez le conteneur de test (`client->getContainer()`).
*   **Parameters** : Ne définissez pas de paramètres dans `services.yaml` pour des classes de services. Utilisez l'injection directe d'arguments via `bind` ou les constructeurs nommés.

## Ressources
*   [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)
*   [Symfony Secrets Management](https://symfony.com/doc/current/configuration/secrets.html)
