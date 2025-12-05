# Tags de Service

## Concept clé
Les tags sont un mécanisme pour marquer des services afin qu'ils soient récupérés par une "collecting pass" (Compiler Pass) et utilisés par un autre service.
C'est la base de l'extensibilité de Symfony (Plugins).

## Application dans Symfony 7.0

### 1. Autoconfiguration (Magique)
Si `autoconfigure: true` est activé, Symfony ajoute des tags automatiquement selon l'interface implémentée.
*   `EventSubscriberInterface` -> Tag `kernel.event_subscriber`
*   `ConstraintValidatorInterface` -> Tag `validator.constraint_validator`
*   `Command` -> Tag `console.command`

### 2. Tag Manuel (Attributs PHP)
Si vous créez votre propre système de plugin ou si l'autoconfiguration ne suffit pas.

```php
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem(index: 'my_handler_key', priority: 10)]
class MyHandler implements HandlerInterface {}
```

### 3. Consommer les services tagués (`TaggedIterator`)
Pour injecter tous les services ayant un certain tag dans votre Manager.

```php
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

class HandlerManager
{
    /**
     * @param iterable<HandlerInterface> $handlers
     */
    public function __construct(
        #[TaggedIterator('app.handler')] private iterable $handlers
    ) {}

    public function run()
    {
        foreach ($this->handlers as $handler) {
            $handler->handle();
        }
    }
}
```

## 🧠 Concepts Clés
1.  **Priorité** : Les tags supportent souvent une priorité (`priority`). Plus elle est haute, plus le service est traité tôt dans la liste.
2.  **Itérable** : L'injection via `iterable` est "Lazy". Les services ne sont instanciés que lors de l'itération `foreach`.

## ⚠️ Points de vigilance (Certification)
*   **Compiler Pass** : Avant l'attribut `#[TaggedIterator]`, il fallait écrire une `CompilerPass` manuelle pour trouver les services tagués et les injecter (voir fichier `compiler-passes.md`). L'attribut est maintenant la méthode standard recommandée.

## Ressources
*   [Symfony Docs - Service Tags](https://symfony.com/doc/current/service_container/tags.html)
*   [Built-in Tags](https://symfony.com/doc/current/reference/dic_tags.html)
