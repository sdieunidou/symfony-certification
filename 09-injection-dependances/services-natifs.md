# Services Natifs (Built-in Services)

## Concept clé
Symfony expose de nombreux services utilitaires. Pour écrire du code découplé et testable, il faut typer les arguments avec les **Interfaces** plutôt que les classes concrètes ou les IDs de services.

## Liste des Indispensables

| Rôle | Interface (Type Hint) | ID Service (Legacy/Alias) |
| :--- | :--- | :--- |
| **Logger** | `Psr\Log\LoggerInterface` | `logger` |
| **Router** | `Symfony\Component\Routing\RouterInterface` | `router` |
| **Event Dispatcher**| `Symfony\Contracts\EventDispatcher\EventDispatcherInterface` | `event_dispatcher` |
| **Request/Session** | `Symfony\Component\HttpFoundation\RequestStack` | `request_stack` |
| **Security Auth** | `Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface` | `security.authorization_checker` |
| **Current User** | `Symfony\Bundle\SecurityBundle\Security` (Helper) | `security.helper` |
| **Password Hash** | `Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface` | `security.user_password_hasher` |
| **Serializer** | `Symfony\Component\Serializer\SerializerInterface` | `serializer` |
| **Validator** | `Symfony\Component\Validator\Validator\ValidatorInterface` | `validator` |
| **Translator** | `Symfony\Contracts\Translation\TranslatorInterface` | `translator` |
| **Mailer** | `Symfony\Component\Mailer\MailerInterface` | `mailer` |
| **Twig** | `Twig\Environment` | `twig` |
| **Filesystem** | `Symfony\Component\Filesystem\Filesystem` | `filesystem` |
| **Kernel** | `Symfony\Component\HttpKernel\KernelInterface` | `kernel` |
| **Parameters** | `Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface` | `parameter_bag` |

## 🧠 Concepts Clés
1.  **Interface Segregation** : En typant sur l'interface, vous permettez de remplacer l'implémentation (ex: remplacer le Router par un Mock dans les tests).
2.  **RequestStack** : Ne jamais injecter `Request` ou `Session` directement (impossible car ce sont des objets de données, pas des services). Injectez `RequestStack` et faites `$stack->getCurrentRequest()`.

## ⚠️ Points de vigilance (Certification)
*   **Security** : Depuis Symfony 6.2, le service `Security` (classe helper) regroupe les fonctionnalités de `User` et `isGranted` de manière plus simple que l'ancien `SecurityContext`.
*   **EntityManager** : Pour Doctrine, on injecte `Doctrine\ORM\EntityManagerInterface`.

## Ressources
*   [Symfony Docs - Service Container Debug](https://symfony.com/doc/current/service_container/debug.html) (Commande `php bin/console debug:autowiring` pour voir la liste complète disponible).
