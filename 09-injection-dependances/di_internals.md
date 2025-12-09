# Injection de Dépendances : Fonctionnement Interne

## Concept clé
Le conteneur de services (Service Container) est compilé. Il transforme une configuration complexe (YAML, XML, PHP) en une seule classe PHP optimisée contenant tous les services instanciés.

## Le Cycle de Vie du Container

### 1. Build (Construction)
L'objet `ContainerBuilder` est créé.
*   **Kernel** : Le Kernel charge les bundles.
*   **Extensions** : Chaque bundle charge sa configuration (via `DependencyInjection\MyBundleExtension`). C'est là que les fichiers `services.yaml` sont lus.
*   À ce stade, on a des **Definitions** (recettes pour créer les services), pas encore les services eux-mêmes.

### 2. Compile (Compilation)
Avant d'être utilisable, le container doit être compilé (`$container->compile()`).
C'est ici que les **Compiler Passes** entrent en jeu.
*   **Resolution** : Les références aux services (`@service_id`) sont résolues.
*   **Optimisation** : Les services privés non utilisés sont supprimés (Garbage collection).
*   **Autowiring** : Symfony devine les arguments manquants.
*   **Tags** : Les services tagués sont collectés et injectés là où ils sont attendus (ex: tous les `twig.extension` sont injectés dans Twig).

### 3. Dump (Génération)
Pour ne pas refaire ce travail lourd à chaque requête, le container compilé est **dumpé** (écrit sur le disque) sous forme de classe PHP.
*   Fichier : `var/cache/prod/App_KernelProdContainer.php`.
*   Cette classe contient des méthodes `getServiceId()` optimisées.

### 4. Runtime (Exécution)
À l'exécution, le Kernel instancie cette classe générée.
Le container est alors **Frozen** (gelé) : on ne peut plus ajouter ou modifier de définitions de services.

## Classes Clés

### 1. Definition
Représente la "recette" d'un service : sa classe, ses arguments, ses appels de méthode (`addMethodCall`), ses tags, sa visibilité (public/private).

### 2. Reference
Représente un lien vers un autre service (l'arobase `@` dans le YAML).

### 3. CompilerPassInterface
Interface pour modifier le container pendant la phase de compilation.
*   Permet de manipuler les `Definitions`.
*   Indispensable pour créer des systèmes de basés sur les **Tags**.

### 4. ServiceLocator
Un "mini-conteneur" léger qui ne donne accès qu'à une liste restreinte de services. Utilisé pour l'injection lazy et pour éviter de passer tout le container.

## 🧠 Concepts Clés
1.  **Lazy Loading** : Par défaut, un service n'est instancié que lorsqu'on l'appelle (`get()`) ou qu'il est injecté dans un autre service en cours d'instanciation.
2.  **Shared** : Par défaut, tous les services sont des singletons (partagés). On reçoit toujours la même instance.
3.  **Synthetic** : Un service synthétique est un service qui ne peut pas être créé par le container (pas de classe/factory) mais qui est injecté manuellement au runtime (ex: `request_stack`, `kernel`).

## ⚠️ Points de vigilance (Certification)
*   **Private Services** : Depuis Symfony 4, les services sont privés par défaut. On ne peut pas faire `$container->get('mon_service_prive')` dans un contrôleur. Il faut passer par l'injection de dépendances.
*   **Circular References** : Si A dépend de B qui dépend de A, le container lance une `ServiceCircularReferenceException`. Solution : Setter Injection ou ServiceSubscriber/ServiceLocator.

## Ressources
*   [Symfony Docs - Service Container](https://symfony.com/doc/current/service_container.html)
*   [Compiler Passes](https://symfony.com/doc/current/service_container/compiler_passes.html)
