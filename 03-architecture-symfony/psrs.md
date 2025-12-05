# Interopérabilité et PSRs

## Concept clé
Le PHP-FIG (Framework Interop Group) publie des recommandations (PSR - PHP Standards Recommendations) pour faciliter l'interopérabilité entre frameworks et librairies. Symfony adopte et implémente la plupart des PSRs.

## Principales PSRs dans Symfony 7
*   **PSR-1 & PSR-12** : Standards de codage (Coding Style).
*   **PSR-3 (Logger Interface)** : Utilisé par `monolog-bundle`. Tous les loggers Symfony implémentent `Psr\Log\LoggerInterface`.
*   **PSR-4 (Autoloading)** : Standard de chargement des classes (Composer).
*   **PSR-6 & PSR-16 (Cache)** : Le composant Cache implémente ces interfaces (Cache Item Interface & Simple Cache).
*   **PSR-11 (Container)** : `Psr\Container\ContainerInterface`. Le Service Container de Symfony l'implémente (permet d'injecter le conteneur de manière standard).
*   **PSR-14 (Event Dispatcher)** : Le composant EventDispatcher est compatible.
*   **PSR-18 (HTTP Client)** : Le composant HttpClient est compatible.
*   **PSR-7, PSR-15, PSR-17 (HTTP Message)** : Symfony utilise `HttpFoundation` (qui n'est PAS PSR-7 par défaut car mutable et plus ancien), mais fournit un pont `symfony/psr-http-message-bridge` pour convertir `Request` <-> `ServerRequestInterface` (PSR-7).

## Points de vigilance (Certification)
*   **HttpFoundation vs PSR-7** : C'est un piège classique. Symfony **n'utilise pas** PSR-7 nativement pour ses contrôleurs (il utilise ses propres objets Request/Response). Mais il est compatible via un bridge si vous voulez utiliser des librairies PSR-7.
*   **Pourquoi pas PSR-7 ?** : PSR-7 est immuable (chaque modification crée un nouvel objet), ce qui a un coût de performance et d'ergonomie que Symfony a choisi d'éviter pour son cœur, tout en supportant l'interopérabilité.

## Ressources
*   [PHP-FIG PSRs](https://www.php-fig.org/psr/)

