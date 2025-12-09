# Décoration de Service

## Concept clé
Le pattern Décorateur permet de modifier ou d'étendre le comportement d'un service existant sans modifier sa classe et sans utiliser l'héritage (qui est souvent bloqué par `final` ou complexe).
Le décorateur "enveloppe" le service original. Symfony remplace l'instance originale par la vôtre dans tout le conteneur.

## 1. Utilisation Standard (Attributs PHP)
L'attribut `#[AsDecorator]` est la méthode recommandée.

```php
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;
use Symfony\Component\DependencyInjection\Attribute\AutowireDecorated;

#[AsDecorator(decorates: MailerInterface::class)]
class LoggableMailer implements MailerInterface
{
    public function __construct(
        // Injecte le service original.
        // AutowireDecorated permet d'utiliser n'importe quel nom de variable, pas juste $inner
        #[AutowireDecorated] private MailerInterface $inner,
        private LoggerInterface $logger
    ) {}

    public function send($message): void
    {
        $this->logger->info('Sending...');
        $this->inner->send($message);
    }
}
```

### Options de l'attribut
*   `decorates`: L'ID ou la classe du service cible.
*   `priority`: (int) Plus elle est haute, plus ce décorateur est "extérieur" (exécuté en premier).
*   `onInvalid`: Comportement si le service cible n'existe pas (`ignore`, `exception`, `null`).

## 2. Configuration YAML
En YAML, le service original est renommé automatiquement (souvent avec suffixe `.inner`), mais vous pouvez le contrôler.

```yaml
services:
    App\DecoratingMailer:
        decorates: App\Mailer
        # Optionnel : renommer le service interne (défaut: App\DecoratingMailer.inner)
        decoration_inner_name: 'app.mailer.original'
        decoration_priority: 5
        # Comportement si App\Mailer n'existe pas
        decoration_on_invalid: ignore
        
        # Injection explicite de l'ancien service
        arguments: ['@.inner'] 
```

## 3. Empiler les Décorateurs (Stacks)
Au lieu de jouer avec les priorités, vous pouvez définir une pile explicite de décorateurs via l'option `stack`. C'est très lisible pour les middlewares.

```yaml
services:
    # Ce service sera la composition de Baz(Bar(Foo))
    my_stacked_service:
        stack:
            - class: App\Baz
              arguments: ['@.inner']
            - class: App\Bar
              arguments: ['@.inner']
            - class: App\Foo # Le service original (coeur)
```

En PHP :
```php
$services->stack('my_stacked_service', [
    inline_service(Baz::class),
    inline_service(Bar::class),
    inline_service(Foo::class),
]);
```

## 4. Gestion des services inexistants (`onInvalid`)
Parfois on veut décorer un service qui n'existe peut-être pas (ex: un service optionnel d'un bundle tiers).

*   `exception` (Défaut) : Plante si le service manque.
*   `ignore` : Le décorateur est simplement supprimé du conteneur.
*   `null` : Le décorateur est créé, mais `$inner` sera `null`.

```php
#[AsDecorator(decorates: 'optional_service', onInvalid: ContainerInterface::IGNORE_ON_INVALID_REFERENCE)]
class MyDecorator { ... }
```

## 🧠 Concepts Clés
1.  **Transparence** : L'ID du service reste le même pour le reste de l'application.
2.  **Inner ID** : Le service décoré est toujours présent dans le conteneur mais sous un autre nom (souvent masqué).
3.  **Héritage** : Le décorateur *devrait* implémenter la même interface que le décoré, mais ce n'est pas techniquement forcé par Symfony (PHP le demandera si vous type-hintez).

## ⚠️ Points de vigilance (Certification)
*   **Tags** : Le service décoré **perd ses tags** ! Le nouveau service (décorateur) ne récupère PAS les tags de l'original (sauf certains tags système comme `kernel.event_subscriber`). Si le service original était une extension Twig, votre décorateur doit aussi être tagué `twig.extension`.
*   **Visibilité** : La visibilité (public/private) du service décoré est conservée par le nouveau service.
*   **Arguments** : En YAML sans autowiring, l'argument spécial pour injecter le service décoré est `@.inner`.

## Ressources
*   [Symfony Docs - Decorating Services](https://symfony.com/doc/current/service_container/service_decoration.html)
