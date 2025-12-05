# Configuration Doctrine (Symfony)

## Le fichier `doctrine.yaml`
La configuration principale se trouve dans `config/packages/doctrine.yaml`.
Elle est divisée en deux sections principales : **DBAL** (Connexion) et **ORM** (Mapping).

```yaml
doctrine:
    dbal:
        url: '%env(resolve:DATABASE_URL)%'
        # driver: 'pdo_mysql' (déduit automatiquement de l'URL)
        
        # Options avancées
        # server_version: '8.0'
        # charset: utf8mb4

    orm:
        auto_generate_proxy_classes: true
        enable_lazy_ghost_objects: true # Nouveauté Symfony 6.2+ (remplace les Proxies classiques)
        naming_strategy: doctrine.orm.naming_strategy.underscore_number_aware
        auto_mapping: true # Découvre automatiquement les entités dans App\Entity
        
        mappings:
            App:
                is_bundle: false
                dir: '%kernel.project_dir%/src/Entity'
                prefix: 'App\Entity'
                alias: App
```

## Connexions Multiples
Vous pouvez configurer plusieurs bases de données (ex: une pour les données client `default`, une pour les logs `logger`).

### Configuration
```yaml
doctrine:
    dbal:
        default_connection: default
        connections:
            default:
                url: '%env(resolve:DATABASE_URL)%'
            logger:
                url: '%env(resolve:LOGGER_DATABASE_URL)%'

    orm:
        default_entity_manager: default
        entity_managers:
            default:
                connection: default
                mappings:
                    Main:
                        is_bundle: false
                        dir: '%kernel.project_dir%/src/Entity/Main'
                        prefix: 'App\Entity\Main'
            logger:
                connection: logger
                mappings:
                    Logger:
                        is_bundle: false
                        dir: '%kernel.project_dir%/src/Entity/Logger'
                        prefix: 'App\Entity\Logger'
```

### Utilisation (Autowiring)
Symfony permet d'injecter le bon EntityManager grâce au nommage des arguments (`$loggerEntityManager`) ou via le `ManagerRegistry`.

```php
// Injection ciblée (nécessite config services.yaml ou attributs)
public function __construct(
    #[Target('logger')] 
    private EntityManagerInterface $loggerEm
) {}

// Via Registry
public function index(ManagerRegistry $registry)
{
    $em = $registry->getManager('logger');
}
```

## Types Personnalisés (DBAL Types)
Pour ajouter un type non supporté par défaut (ex: `geometry`).

1. Créer la classe PHP étendant `Doctrine\DBAL\Types\Type`.
2. L'enregistrer dans `doctrine.yaml` :

```yaml
doctrine:
    dbal:
        types:
            geometry: 'App\Doctrine\Type\GeometryType'
```

## Filtres SQL (SQL Filters)
Permet d'appliquer une clause `WHERE` globale à toutes les requêtes (ex: Soft Delete `deleted_at IS NULL`, Multi-tenant `tenant_id = 1`).

```yaml
doctrine:
    orm:
        filters:
            soft_delete:
                class: App\Doctrine\Filter\SoftDeleteFilter
                enabled: true
```

## 🧠 Concepts Clés
1.  **Auto Mapping** : Simplifie la vie en dev, mais pour des bundles ou structures complexes, on définit manuellement les zones (`mappings`).
2.  **Lazy Ghost Objects** : Optimisation majeure récente. L'objet n'est plus un Proxy étendant l'entité, mais l'entité elle-même avec un initialiseur interne.
3.  **Resolve env** : Toujours utiliser `resolve:` pour que Symfony décode les caractères spéciaux dans l'URL de la BDD.

## Ressources
*   [Symfony Docs - Doctrine Configuration](https://symfony.com/doc/current/doctrine.html#configuration)
*   [Symfony Docs - Multiple Connections](https://symfony.com/doc/current/doctrine/multiple_entity_managers.html)
