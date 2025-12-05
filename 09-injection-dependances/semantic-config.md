# Configuration Sémantique (Bundles)

## Concept clé
La Configuration Sémantique est le mécanisme qui permet aux Bundles d'exposer une configuration claire et validée aux utilisateurs (`config/packages/acme_demo.yaml`).
Elle transforme cette config utilisateur (YAML) en définitions de services valides.

## Les 2 Composants

### 1. La classe `Configuration`
Implements `ConfigurationInterface`. Définit la structure (Schema) via le `TreeBuilder`.

```php
public function getConfigTreeBuilder(): TreeBuilder
{
    $treeBuilder = new TreeBuilder('acme_demo');
    $rootNode = $treeBuilder->getRootNode();

    $rootNode
        ->children()
            ->booleanNode('enabled')->defaultTrue()->end()
            ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
            ->arrayNode('servers')
                ->scalarPrototype()->end()
            ->end()
        ->end();

    return $treeBuilder;
}
```

### 2. L'Extension DI (`DependencyInjection\AcmeDemoExtension`)
Charge la config, la valide avec la classe `Configuration`, et manipule le conteneur.

```php
public function load(array $configs, ContainerBuilder $container): void
{
    $configuration = new Configuration();
    $config = $this->processConfiguration($configuration, $configs);

    // $config contient le tableau validé (ex: ['enabled' => true, 'api_key' => '...'])
    
    // On peut définir des paramètres ou charger des services
    $container->setParameter('acme_demo.api_key', $config['api_key']);
    
    $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    $loader->load('services.yaml');
}
```

## 🧠 Concepts Clés
1.  **Validation** : Le `Config` component valide les types, les champs requis, les valeurs par défaut. Si l'utilisateur fait une erreur dans son YAML, il a un message d'erreur précis ("The child node 'api_key' at path 'acme_demo' must be configured").
2.  **Extension** : C'est le pont entre la config utilisateur et le conteneur. Elle est chargée automatiquement par le Kernel si elle suit les conventions de nommage.

## ⚠️ Points de vigilance (Certification)
*   **PrependExtensionInterface** : Permet à un bundle de configurer un *autre* bundle avant le chargement. (Ex: DoctrineBundle configure TwigBundle pour ajouter des variables globales).

## Ressources
*   [Symfony Docs - Bundle Configuration](https://symfony.com/doc/current/components/config/definition.html)
