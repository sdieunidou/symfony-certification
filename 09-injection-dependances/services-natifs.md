# Services Natifs

## Concept clé
Symfony fournit des centaines de services prêts à l'emploi.
Connaître les principaux IDs (ou classes interfaces) est essentiel.

## Application dans Symfony 7.0

### Services principaux (Interfaces à typer)
*   `Psr\Log\LoggerInterface` (Logger)
*   `Symfony\Component\Routing\RouterInterface` (Router)
*   `Symfony\Component\EventDispatcher\EventDispatcherInterface` (Dispatcher)
*   `Symfony\Component\HttpFoundation\RequestStack` (Accès requête/session)
*   `Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface` (Sécurité)
*   `Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface` (User token)
*   `Doctrine\ORM\EntityManagerInterface` (Base de données)
*   `Symfony\Contracts\Translation\TranslatorInterface` (Traduction)
*   `Symfony\Component\Serializer\SerializerInterface` (Sérialisation)
*   `Twig\Environment` (Templating)

## Points de vigilance (Certification)
*   **Alias** : La plupart de ces interfaces sont des alias vers les services concrets.
*   **kernel** : Le service `kernel` (ID `http_kernel`) est le cœur de l'app.
*   **service_container** : Le service qui représente le conteneur lui-même. Il est injectable, mais c'est une mauvaise pratique (Service Locator pattern) sauf cas très spécifiques.

## Ressources
*   [Symfony Docs - Built-in Services](https://symfony.com/doc/current/service_container/debug.html) (utiliser `debug:container`)

