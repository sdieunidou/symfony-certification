# Le Composant Config

Le composant **Config** fournit une infrastructure pour charger, valider et traiter des fichiers de configuration (YAML, XML, PHP, etc.). Il est le pilier du système d'Extension de Bundle et du Dependency Injection Container.

## 1. Concepts Fondamentaux

Le processus se déroule en 3 étapes :
1.  **Load** : Charger la configuration depuis un fichier vers un tableau PHP.
2.  **Define & Validate** : Définir la structure attendue (Schéma) et valider les données (TreeBuilder).
3.  **Cache** : Mettre en cache le résultat pour éviter de re-analyser à chaque requête.

---

## 2. Chargement (Loading)

Le composant utilise un système de `LoaderResolver` pour choisir le bon chargeur selon le type de ressource.

```php
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// 1. Localiser les fichiers
$locator = new FileLocator([__DIR__.'/config']);

// 2. Définir les loaders disponibles
$loaderResolver = new LoaderResolver([
    new YamlFileLoader($container, $locator),
    // new XmlFileLoader(...),
]);

// 3. Déléguer le chargement
$loader = new DelegatingLoader($loaderResolver);
$loader->load('services.yaml');
```

---

## 3. Définition et Validation (TreeBuilder)

C'est la partie la plus importante pour la **Certification** et la création de Bundles. La classe `TreeBuilder` permet de définir la structure de configuration autorisée.

### Structure Typique (ConfigurationInterface)

```php
namespace App\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('acme_demo');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                // Types scalaires
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('api_key')->isRequired()->cannotBeEmpty()->end()
                ->integerNode('timeout')->defaultValue(30)->min(0)->end()
                
                // Enumérations
                ->enumNode('mode')
                    ->values(['strict', 'loose'])
                    ->defaultValue('strict')
                ->end()

                // Tableaux (ArrayNode)
                ->arrayNode('servers')
                    ->info('Liste des serveurs disponibles')
                    ->scalarPrototype()->end() // Attends une liste de strings
                ->end()
                
                // Tableaux associatifs complexes (Map)
                ->arrayNode('database')
                    ->children()
                        ->scalarNode('host')->end()
                        ->integerNode('port')->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
```

### Règles de Validation Avancées
On peut ajouter des règles de validation custom via `validate()` et `then()`.

```php
->scalarNode('token')
    ->validate()
    ->ifTrue(fn($v) => strlen($v) < 10)
    ->thenInvalid('Le token doit faire au moins 10 caractères')
    ->end()
->end()
```

### Normalisation
Permet de corriger les données avant validation (ex: transformer une string en array).

```php
->arrayNode('tags')
    ->beforeNormalization()
        ->ifString()
        ->then(fn($v) => [$v]) // "tag1" devient ["tag1"]
    ->end()
    ->scalarPrototype()->end()
->end()
```

---

## 4. Traitement (Processor)

Le `Processor` prend l'arbre de définition (TreeBuilder) et les configurations brutes (souvent multiples, ex: `config.yaml`, `config_dev.yaml`) pour les fusionner et les valider.

```php
use Symfony\Component\Config\Definition\Processor;

$processor = new Processor();
$configuration = new Configuration(); // Votre classe définie plus haut

try {
    // Fusionne les tableaux de config et valide
    $processedConfig = $processor->processConfiguration(
        $configuration,
        [$config1, $config2] // L'ordre compte : le dernier écrase les précédents
    );
} catch (InvalidConfigurationException $e) {
    // Erreur explicite : "Invalid configuration for path 'acme_demo.api_key': value is required."
}
```

---

## 5. Caching (ConfigCache)

Pour la performance, Symfony ne parse pas les fichiers YAML à chaque requête en production. Il utilise `ConfigCache`.

```php
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

$cachePath = __DIR__.'/var/cache/config.php';
$configCache = new ConfigCache($cachePath, true); // true = debug mode (check modif time)

if (!$configCache->isFresh()) {
    // ... Charger la config ...
    // ... Sauvegarder le résultat ...
    $resources = [new FileResource('config.yaml')];
    $configCache->write(serialize($config), $resources);
}
```
Si une `FileResource` change, le cache est invalidé (en mode debug).

---

## 6. Points de vigilance pour la Certification

*   **PrependExtensionInterface** : Dans un Bundle, permet de modifier la configuration d'un *autre* bundle avant qu'elle ne soit traitée. Très utilisé (ex: DoctrineBundle configure Twig pour ajouter ses globales).
*   **Prototype** : Utilisé dans `ArrayNode` pour définir un nombre indéfini d'éléments ayant la même structure (ex: liste de connexions).
*   **AddDefaultsIfNotSet** : Important pour les `ArrayNode`. Si non présent, le tableau n'est créé que si l'utilisateur le définit explicitement.
*   **Fusion** : Comprendre que les configurations sont écrasées clé par clé. Si `config_prod.yaml` redéfinit une clé de `config.yaml`, c'est la valeur de prod qui gagne.
