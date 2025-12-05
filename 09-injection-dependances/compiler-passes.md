# Passes de Compilateur (Compiler Passes)

## Concept clé
Le conteneur de services passe par une phase de **Compilation** avant d'être utilisé.
Un `CompilerPass` est un morceau de code qui s'exécute **pendant** cette compilation pour modifier dynamiquement la définition des services.
C'est le seul moment où l'on peut modifier un service déjà enregistré (changer sa classe, ses arguments, appeler des méthodes).

## Cas d'usage typique : Tagged Services
Vous créez un système de plugins (ex: `TransportInterface`). Vous voulez que votre `TransportManager` reçoive automatiquement tous les services tagués `app.transport`.

```php
namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TransportPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // 1. Toujours vérifier si le service cible existe
        if (!$container->has('app.transport_manager')) {
            return;
        }

        $definition = $container->findDefinition('app.transport_manager');

        // 2. Trouver tous les services avec le tag
        $taggedServices = $container->findTaggedServiceIds('app.transport');

        foreach ($taggedServices as $id => $tags) {
            // 3. Injecter via un setter (addTransport)
            $definition->addMethodCall('addTransport', [new Reference($id)]);
        }
    }
}
```

## Enregistrement
Dans le `Kernel.php` :

```php
protected function build(ContainerBuilder $container): void
{
    $container->addCompilerPass(new TransportPass());
}
```

## Alternative Moderne : `#[TaggedIterator]`
Depuis Symfony 6, l'utilisation explicite de CompilerPass pour l'injection de tags est souvent remplacée par l'attribut `#[TaggedIterator]` dans le constructeur, qui gère cela automatiquement.

```php
public function __construct(
    #[TaggedIterator('app.transport')] iterable $transports
) { ... }
```

## 🧠 Concepts Clés
1.  **Frozen** : Après la compilation, le conteneur est "gelé". On ne peut plus rien modifier.
2.  **Optimisation** : Symfony utilise des passes internes pour retirer les services privés non utilisés, résoudre les alias, et inliner les services pour la performance.

## ⚠️ Points de vigilance (Certification)
*   **Ordre** : Les passes ont des priorités (Optimization, BeforeOptimization, AfterRemoving, etc.). Par défaut `TYPE_BEFORE_OPTIMIZATION`.
*   **Manipulation** : On manipule des objets `Definition`, pas les services eux-mêmes (qui n'existent pas encore).

## Ressources
*   [Symfony Docs - Compiler Passes](https://symfony.com/doc/current/service_container/compiler_passes.html)
