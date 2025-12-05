# Passes de Compilateur (Compiler Passes)

## Concept clé
Un `CompilerPass` permet de modifier le conteneur de services **pendant sa compilation**, juste avant qu'il ne soit figé (frozen).
C'est le moment idéal pour trouver tous les services tagués et les injecter dans un gestionnaire.

## Application dans Symfony 7.0
Implémenter `CompilerPassInterface`.

```php
namespace App\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MailTransportPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('app.mail_chain')) {
            return;
        }

        $definition = $container->findDefinition('app.mail_chain');
        $taggedServices = $container->findTaggedServiceIds('app.mail_transport');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addTransport', [new Reference($id)]);
        }
    }
}
```

Ensuite, enregistrer le pass dans le `Kernel.php`.

## Points de vigilance (Certification)
*   **Modification** : C'est le seul endroit où vous pouvez modifier des définitions de services existants (changer des arguments, appeler des setters).
*   **Optimisation** : Symfony utilise de nombreuses passes internes pour optimiser le conteneur (retirer les services privés non utilisés, résoudre les alias).

## Ressources
*   [Symfony Docs - Compiler Passes](https://symfony.com/doc/current/service_container/compiler_passes.html)

