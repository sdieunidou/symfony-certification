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
├── config/             # Configuration (services.yaml, routes.yaml)
├── src/
│   ├── Controller/
│   ├── DependencyInjection/ # Extension & Configuration
│   ├── MyBundle.php         # Classe principale
│   └── Service/
├── templates/
├── composer.json
├── README.md
└── LICENSE
```

## La classe Bundle (Modern way: AbstractBundle)
Depuis Symfony 6.1, la méthode recommandée est d'étendre `AbstractBundle`.
Cette classe unique remplace souvent la paire `Bundle` + `DependencyInjection\Extension`.

```php
namespace Acme\MyBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

class MyBundle extends AbstractBundle
{
    // 1. Charger les services (services.yaml)
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import('../config/services.yaml');

        // Utiliser la config pour définir des paramètres ou services
        $builder->setParameter('my_bundle.api_key', $config['api_key']);
    }

    // 2. Définir la configuration (Semantic Configuration)
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('api_key')->isRequired()->end()
                ->booleanNode('enable_logger')->defaultTrue()->end()
            ->end();
    }

    // 3. Pré-configurer d'autres bundles (Prepend Extension)
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        // Exemple : configurer Twig automatiquement si ce bundle est installé
        $container->extension('twig', [
            'globals' => [
                'my_bundle_version' => '1.0.0'
            ]
        ]);
    }
}
```

## La méthode Classique (Legacy / Complexe)
Si vous avez besoin de plus de contrôle, vous pouvez séparer la classe Bundle et l'Extension.

1.  **Bundle Class** : `Acme\MyBundle\MyBundle` (étend `Bundle`).
2.  **Extension Class** : `Acme\MyBundle\DependencyInjection\MyExtension` (étend `Extension`).
3.  **Configuration Class** : `Acme\MyBundle\DependencyInjection\Configuration` (implémente `ConfigurationInterface`).

C'est l'`Extension` qui charge les services via `YamlFileLoader` et traite la configuration via `processConfiguration`.

## Interaction avec d'autres Bundles (PrependExtensionInterface)
Si votre bundle doit configurer un autre bundle (ex: ajouter une config Doctrine ou Twig), votre Extension doit implémenter `PrependExtensionInterface`.
*Note : Avec `AbstractBundle`, c'est directement la méthode `prependExtension()`.*

## Bonnes Pratiques (Best Practices)
1.  **Nommage** : `AcmeBlogBundle` (pas `BlogBundle` tout court).
2.  **Services** :
    *   Préfixez TOUS vos IDs de services et paramètres (ex: `acme_blog.repository.post`).
    *   Évitez l'`autowiring` public. Définissez vos services explicitement ou utilisez un autowiring local strict pour ne pas polluer le conteneur de l'application.
3.  **Configuration** : Utilisez la **Semantic Configuration** (TreeBuilder) plutôt que de simples paramètres. Cela permet la validation et l'autocomplétion de la config.
4.  **Composer** : Définissez le `type: symfony-bundle` dans `composer.json`.

## Recettes (Recipes)
Pour distribuer votre bundle efficacement, créez une **Recette Flex** (dans `symfony/recipes-contrib`).
Elle permet de :
*   Ajouter automatiquement le bundle dans `config/bundles.php`.
*   Créer le fichier de configuration par défaut `config/packages/my_bundle.yaml`.
*   Copier des fichiers par défaut (routes, env vars).

## 🧠 Concepts Clés
*   **Compiler Passes** : Pour modifier des services existants (ex: ajouter des tags) lors de la compilation du conteneur, utilisez la méthode `build(ContainerBuilder $container)` dans votre classe Bundle.

## Ressources
*   [Symfony Docs - Create a Bundle](https://symfony.com/doc/current/bundles.html)
*   [Symfony Docs - Best Practices](https://symfony.com/doc/current/bundles/best_practices.html)
