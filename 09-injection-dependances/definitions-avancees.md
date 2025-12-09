# Définitions Avancées de Services

## Concept clé
Au-delà de l'enregistrement simple, Symfony permet de contrôler finement comment les services sont instanciés : appels de méthodes après construction, utilisation de factories, héritage de configuration, etc.

## 1. Appels de Méthodes (Setter Injection)
Si votre service a besoin de dépendances optionnelles ou de configuration après instanciation (ex: setters), utilisez `calls`.

```yaml
services:
    App\Service\ReportGenerator:
        calls:
            - ['setLogger', ['@logger']]
            - ['setDebugMode', ['%kernel.debug%']]
```
C'est souvent utilisé pour résoudre des **références circulaires**.

## 2. Alias et Visibilité
*   **Alias** : Permet d'utiliser un service via un nom court ou une interface.
*   **Public/Private** : Par défaut, tous les services sont privés (accessibles uniquement par injection). Pour les rendre accessibles via `$container->get()` (déconseillé), utilisez `public: true`.

```yaml
services:
    # Alias
    app.mailer: '@App\Service\Mailer'

    # Alias public (pour les tests ou legacy)
    test.client:
        alias: 'test.client'
        public: true
```

## 3. Services non partagés (Prototype)
Par défaut, les services sont des **singletons** (`shared: true`). Si vous voulez une **nouvelle instance** à chaque fois qu'on demande le service, utilisez `shared: false`.

```yaml
services:
    App\Util\StringProcessor:
        shared: false
```

## 4. Héritage (Services Parents)
Pour éviter de répéter la configuration pour des services similaires, utilisez `parent`.

```yaml
services:
    # Service abstrait (template)
    App\Service\BaseManager:
        abstract: true
        arguments: ['@logger']
        calls:
            - ['setDispatcher', ['@event_dispatcher']]

    # Hérite des arguments et calls du parent
    App\Service\UserManager:
        parent: App\Service\BaseManager
        # On peut ajouter/surcharger des arguments
```

## 5. Configurateurs
Un configurateur est un callable qui s'exécute juste après l'instanciation du service pour le configurer (similaire à `calls` mais plus puissant car externalisé).

```yaml
services:
    App\Service\MyService:
        configurator: ['@App\Configurator\MyServiceConfigurator', 'configure']
```

## 6. Arguments Abstraits
Si une classe a besoin d'un argument qui *doit* être défini par les services enfants ou un Compiler Pass, marquez-le comme abstrait.

```yaml
services:
    App\Abstract\BaseWorker:
        abstract: true
        arguments:
            $token: !abstract "doit être défini par l'enfant"
```

## 7. Imports de Configuration
Pour organiser `services.yaml`, vous pouvez importer d'autres fichiers.

```yaml
imports:
    - { resource: 'parameters.yaml' }
    - { resource: 'admin_services.yaml', ignore_errors: true }
    # Import de répertoire (Symfony 5.1+)
    - { resource: '../src/Resources/config/' }
```

## 🧠 Concepts Clés
1.  **Wither Injection** : Symfony supporte les méthodes immutables `with...()` via l'attribut `returns_clone: true` dans les `calls`.
2.  **Service Deprecation** : Vous pouvez déprécier un service via l'option `deprecated`.
    ```yaml
    App\OldService:
        deprecated: { package: 'app/old', version: '1.2', message: 'Use NewService instead' }
    ```

## ⚠️ Points de vigilance (Certification)
*   **Setter vs Constructor** : Préférez toujours l'injection par constructeur (services immuables, dépendances claires). Utilisez les Setters uniquement pour les dépendances optionnelles ou circulaires.
*   **Priorité** : La définition spécifique écrase la définition héritée (`parent`).
*   **Imports** : L'ordre des imports compte. Les fichiers importés en dernier écrasent les configurations précédentes.

## Ressources
*   [Symfony Docs - Service Configuration](https://symfony.com/doc/current/service_container.html)
*   [Symfony Docs - Parent Services](https://symfony.com/doc/current/service_container/parent_services.html)
