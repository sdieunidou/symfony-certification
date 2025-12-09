# Tags de Service

## Concept clé
Les tags permettent de marquer des services pour les regrouper et les injecter dans une collection (pattern Strategy/Chain of Responsibility) ou pour leur donner un comportement spécial via des Compiler Passes.

## 1. Définir un Tag (Marquer le service)

### Via Attributs PHP (Moderne)
C'est la méthode recommandée. On distingue deux attributs :

*   `#[AutoconfigureTag('app.handler')]` : **Pose l'étiquette** sur la classe. Indispensable si l'interface n'est pas autoconfigurée par défaut.
*   `#[AsTaggedItem]` : **Configure l'étiquette** (priorité, index) pour l'injection.

```php
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AutoconfigureTag('app.handler')]
#[AsTaggedItem(index: 'handler_one', priority: 10)]
class MyHandler implements HandlerInterface {}
```

### Via YAML (Configuration explicite)
Vous pouvez ajouter des attributs arbitraires à vos tags (nom, alias, etc.).

```yaml
services:
    App\Service\MyHandler:
        tags:
            - { name: 'app.handler', priority: 20, alias: 'handler_alias' }
```

## 2. Consommer les Tags (Injecter la collection)

Au lieu d'écrire une Compiler Pass complexe, Symfony permet d'injecter directement un itérable de services tagués.

### Attribut `#[TaggedIterator]` (PHP)
```php
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class HandlerCollection
{
    public function __construct(
        #[TaggedIterator('app.handler')] iterable $handlers
    ) {
        // $handlers contient tous les services avec le tag 'app.handler'
    }
}
```

### Options Avancées de l'Iterateur
L'attribut `#[TaggedIterator]` (et son équivalent YAML `!tagged_iterator`) supporte des options puissantes pour organiser la collection.

#### Indexation (`indexAttribute` / `defaultIndexMethod`)
Par défaut, l'index du tableau est l'ID du service. Pour utiliser une clé personnalisée (ex: pour faire `$handlers['my_key']`) :

```php
// Utilise l'attribut 'key' du tag comme clé du tableau
#[TaggedIterator('app.handler', indexAttribute: 'key')] iterable $handlers
```
Dans le YAML du service, il faut alors : `tags: [{ name: 'app.handler', key: 'my_key' }]`.

On peut aussi appeler une méthode statique sur le service pour obtenir la clé :
```php
#[TaggedIterator('app.handler', defaultIndexMethod: 'getDefaultIndexName')]
```

#### Priorité (`defaultPriorityMethod`)
Pour définir la priorité via une méthode statique sur le service (plutôt que dans le YAML ou `AsTaggedItem`) :

```php
#[TaggedIterator('app.handler', defaultPriorityMethod: 'getPriority')]
```
Le service doit alors avoir une méthode `public static function getPriority(): int`.

#### Exclusion (`exclude`)
Pour exclure des services spécifiques de la collection :

```php
#[TaggedIterator('app.handler', exclude: [BrokenHandler::class])]
```

### Syntaxe YAML
Si vous n'utilisez pas l'autowiring dans le constructeur :

```yaml
App\HandlerCollection:
    arguments:
        $handlers: !tagged_iterator { tag: 'app.handler', index_by: 'key', default_priority_method: 'getPriority' }
```

## 3. Utilisation Manuelle (Compiler Pass)
Si vous avez besoin de lire des **attributs personnalisés** du tag (ex: `alias`, `method`...) qui ne sont pas gérés par `TaggedIterator`, vous devez utiliser une Compiler Pass.

```php
// Dans un CompilerPass
$definition = $container->findDefinition(TransportChain::class);
$taggedServices = $container->findTaggedServiceIds('app.transport');

foreach ($taggedServices as $id => $tags) {
    // Un service peut avoir plusieurs fois le même tag, donc $tags est un tableau de tableaux d'attributs
    foreach ($tags as $attributes) {
        $alias = $attributes['alias'] ?? 'default';
        
        $definition->addMethodCall('addTransport', [
            new Reference($id),
            $alias
        ]);
    }
}
```

## 🧠 Concepts Clés
1.  **Lazy Loading** : L'injection via `iterable` est lazy. Les services ne sont instanciés que lorsque vous itérez dessus.
2.  **Collection** : `TaggedIterator` retourne un `RewindableGenerator` (ou un `ArrayIterator` si converti).
3.  **Héritage** : `AutoconfigureTag` se transmet aux classes enfants.

## ⚠️ Points de vigilance (Certification)
*   **AsTaggedItem** : Cet attribut ne remplace pas le tag lui-même (sauf si autoconfigure est actif). Il sert à paramétrer l'injection (index, priorité).
*   **Ordre de priorité** : Plus la priorité est élevée (entier), plus le service arrive tôt dans l'itération.
*   **Doublons** : Un service peut avoir le même tag plusieurs fois (avec des attributs différents). `findTaggedServiceIds` le gère, mais `TaggedIterator` peut avoir des comportements spécifiques selon l'indexation.

## Ressources
*   [Symfony Docs - How to Work with Service Tags](https://symfony.com/doc/current/service_container/tags.html)
