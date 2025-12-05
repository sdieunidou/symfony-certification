# Component Config

## Concept Clé
Le composant **Config** permet de charger, valider et traiter des fichiers de configuration de différents formats (YAML, XML, PHP). Il est massivement utilisé par le conteneur de services et les bundles.

## Fonctionnalités

### Chargement (Loading)
Il délègue le chargement à des "Loaders" spécifiques selon le type de ressource.
```php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;

$locator = new FileLocator([__DIR__]);
$loaderResolver = new LoaderResolver([
    new YamlFileLoader($locator),
    new XmlFileLoader($locator),
]);

$delegatingLoader = new DelegatingLoader($loaderResolver);
$config = $delegatingLoader->load('config.yaml');
```

### Définition & Validation
Il permet de définir la structure attendue de la configuration (TreeBuilder) et de la valider.
```php
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

$treeBuilder = new TreeBuilder('mon_bundle');
$treeBuilder->getRootNode()
    ->children()
        ->booleanNode('enabled')->defaultTrue()->end()
        ->scalarNode('api_key')->isRequired()->end()
    ->end();
```

### Caching
Il gère la mise en cache des configurations compilées pour les performances (`ConfigCache`).

## Ressources
*   [Symfony Docs - Config](https://symfony.com/doc/current/components/config.html)
