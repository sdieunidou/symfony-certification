# Création de Bundle

## Concept Clé
Un **Bundle** est un moyen d'organiser et de partager du code Symfony (Services, Configuration, Contrôleurs, Templates).
Bien que dans une application moderne (Symfony 4+), on code principalement dans `src/` (sans bundle), la création de Bundle reste indispensable pour :
1.  **Partager** une fonctionnalité entre plusieurs projets (entreprise).
2.  **Distribuer** une librairie Open Source à la communauté (via Composer).

## Structure d'un Bundle
Un bundle est structuré comme une mini-application Symfony.

```text
MyBundle/
├── config/             # Configuration par défaut (services.yaml)
├── src/
│   ├── Controller/
│   ├── DependencyInjection/
│   │   └── MyExtension.php  <-- Le point d'entrée
│   ├── MyBundle.php         <-- La classe principale
│   └── Service/
├── templates/
└── composer.json
```

## La classe Bundle
Elle doit étendre `Symfony\Component\HttpKernel\Bundle\Bundle`.
Depuis Symfony 6.1, elle peut souvent rester vide ou implémenter `getPath()` pour définir la racine.

```php
namespace Acme\MyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MyBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
```

## Dependency Injection Extension
C'est le cœur du Bundle. C'est cette classe qui va charger vos services et traiter la configuration.
Par convention, elle doit se trouver dans le sous-namespace `DependencyInjection` et s'appeler `NomDuBundleExtension` (sans le suffixe Bundle).

Exemple pour `MyBundle` -> `MyExtension`.

```php
namespace Acme\MyBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Config\FileLocator;

class MyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // 1. Charger les services du bundle
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        $loader->load('services.yaml');

        // 2. Gérer la configuration (Configuration.php)
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        
        // 3. Passer la config aux services (via paramètres)
        $container->setParameter('my_bundle.api_key', $config['api_key']);
    }
}
```

## Configuration (TreeBuilder)
Pour permettre aux utilisateurs de configurer votre bundle via `config/packages/my_bundle.yaml`, vous devez définir la structure attendue.

```php
namespace Acme\MyBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('my_bundle');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('api_key')->isRequired()->end()
                ->booleanNode('enable_logger')->defaultTrue()->end()
            ->end();

        return $treeBuilder;
    }
}
```

## AbstractBundle (Symfony 6.1+)
Pour simplifier, Symfony propose maintenant `AbstractBundle` qui combine la classe Bundle et l'Extension. C'est la méthode recommandée pour les bundles modernes simples.

```php
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class MyBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');
        
        $builder->setParameter('my_bundle.api_key', $config['api_key']);
    }

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('api_key')->end()
            ->end();
    }
}
```

## Recettes (Recipes)
Pour distribuer votre bundle efficacement, créez une **Recette Flex** (dans `symfony/recipes-contrib`). Elle permettra de configurer automatiquement le bundle lors du `composer require` (création du fichier de config, ajout au bundles.php).

## 🧠 Concepts Clés
1.  **Préfixe de Service** : Tous vos services doivent être préfixés (ex: `acme.my_bundle.service`) pour éviter les collisions avec l'application hôte.
2.  **Compiler Passes** : Si votre bundle doit modifier d'autres services (ex: ajouter des tags, modifier Twig), utilisez un `CompilerPass` dans la méthode `build()` de la classe Bundle.

## Ressources
*   [Symfony Docs - Create a Bundle](https://symfony.com/doc/current/bundles.html)
*   [Symfony Docs - AbstractBundle](https://symfony.com/doc/current/bundles/abstract_bundle.html)
