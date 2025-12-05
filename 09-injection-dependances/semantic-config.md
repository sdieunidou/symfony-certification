# Configuration Sémantique

## Concept clé
Permet de définir une configuration DSL (Domain Specific Language) pour un Bundle, qui sera validée et transformée avant d'être injectée dans le conteneur. C'est ce qui permet d'écrire `framework: { secret: '...' }` dans `config/packages/framework.yaml`.

## Application dans Symfony 7.0
Utilisée principalement lors de la création de Bundles réutilisables.
Deux classes clés :
1.  `Configuration` (implémente `ConfigurationInterface`) : Définit l'arbre de configuration (TreeBuilder).
2.  `Extension` (étend `Extension`) : Charge la config, la processe, et définit les services.

### Exemple (TreeBuilder)
```php
public function getConfigTreeBuilder(): TreeBuilder
{
    $treeBuilder = new TreeBuilder('acme_social');
    $treeBuilder->getRootNode()
        ->children()
            ->scalarNode('twitter_api_key')->isRequired()->end()
            ->booleanNode('enable_logging')->defaultFalse()->end()
        ->end();
    return $treeBuilder;
}
```

## Points de vigilance (Certification)
*   **Extension** : La classe Extension doit se trouver dans le sous-namespace `DependencyInjection`.
*   **Prepend** : L'interface `PrependExtensionInterface` permet à un bundle de configurer d'autres bundles (ex: DoctrineBundle configure TwigBundle).

## Ressources
*   [Symfony Docs - Defining and Processing Configuration](https://symfony.com/doc/current/components/config/definition.html)

