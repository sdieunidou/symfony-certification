# Conteneur de Services (Service Container)

## Concept clé
Le Conteneur d'Injection de Dépendances (DIC) est le cœur de Symfony. C'est une usine géante qui construit et stocke tous les objets (Services) de l'application.
Il résout le problème de l'instanciation manuelle (`new ClassA(new ClassB(new ClassC...))`).

## Cycle de Vie
1.  **Build** : Le conteneur lit la configuration (YAML, PHP, Attributs), exécute les Extensions de Bundles et les Compiler Passes.
2.  **Compile** : Il résout les dépendances, optimise le graphe, et génère une classe PHP optimisée (ex: `var/cache/dev/App_KernelDevDebugContainer.php`).
3.  **Runtime** : À chaque requête, le Kernel instancie ce conteneur php compilé. Les services sont créés en **Lazy Loading** (uniquement quand on les demande).

## Services Publics vs Privés
*   **Privé** (Défaut) : Un service est privé par défaut. On ne peut pas l'obtenir via `$container->get('id')`. Il n'est accessible que par injection de dépendance. Cela permet au compilateur de l'inliner ou de le supprimer s'il est inutilisé.
*   **Public** : Accessible via `$container->get()`. Rarement nécessaire, sauf pour les services utilisés par le Kernel lui-même ou pour le débogage.

## Service Synthétique (Synthetic)
Un service synthétique est un service qui n'est pas créé par le conteneur, mais injecté dedans "de l'extérieur" au runtime (ex: `kernel`, `request_stack`).

## 🧠 Concepts Clés
1.  **Singleton** : Par défaut, tous les services sont des singletons dans le contexte d'une requête. Si vous demandez `LoggerInterface` à 3 endroits, vous recevez la même instance.
2.  **Immutabilité** : Une fois compilé, on ne peut plus ajouter de service.

## ⚠️ Points de vigilance (Certification)
*   **Injection de Conteneur** : Injecter le service `service_container` (ou `ContainerInterface`) dans vos propres services est considéré comme une mauvaise pratique (**Service Locator Pattern**). Il faut injecter uniquement les dépendances réelles.
*   **Debug** : `php bin/console debug:container` liste tous les services publics et privés.

## Linting de la Configuration
Avant de déployer en production, il est crucial de valider la configuration du conteneur.
*   `php bin/console lint:container` : Vérifie que les services sont correctement configurés (arguments, types).
*   `php bin/console lint:container --resolve-env-vars` : (Symfony 7.2+) Force la résolution des variables d'env pour vérifier qu'elles existent.

Ces vérifications s'appuient sur des *Compiler Passes* comme `CheckTypeDeclarationsPass` et `CheckAliasValidityPass` (Symfony 7.1+).

## Ressources
*   [Symfony Docs - Service Container](https://symfony.com/doc/current/service_container.html)
