# PSR & PHP-FIG

## Concept clé
Le **PHP-FIG** (PHP Framework Interop Group) est un groupe composé de représentants de projets PHP majeurs (dont Symfony, Laravel, Composer, etc.) qui travaillent ensemble pour définir des standards communs : les **PSR** (PHP Standards Recommendations).

L'objectif principal est l'**interopérabilité** : permettre aux bibliothèques de différents frameworks de fonctionner ensemble facilement.

## Les PSRs incontournables pour Symfony

Symfony adopte et implémente nativement la plupart des PSRs. Voici les plus importantes à connaître pour la certification :

### 1. Autoloading
*   **PSR-4 (Autoloader)** : Standard actuel de chargement automatique des classes. Il mappe les namespaces aux répertoires du système de fichiers (ex: `App\` -> `src/`). C'est la fondation de Composer.

### 2. Style de Code (Coding Style)
*   **PSR-1 (Basic Coding Standard)** : Règles de base (balises PHP, encodage UTF-8, effets de bord, nommage des classes en PascalCase, méthodes en camelCase).
*   **PSR-12 (Extended Coding Style)** : Extension de PSR-1 et remplacement de PSR-2. Définit les règles de formatage (espaces, accolades, visibilité, types de retour).
*   **PER-CS (PHP Evolved Recommendations Coding Style)** : Évolution continue de PSR-12 pour suivre les nouvelles fonctionnalités de PHP (ex: Enums, Readonly, etc.).

### 3. Interfaces de Service (Les "Contracts")
Ces PSRs définissent des interfaces communes pour que vous puissiez changer d'implémentation sans changer votre code.

*   **PSR-3 (Logger Interface)** : Définit `Psr\Log\LoggerInterface`.
    *   Utilisé partout dans Symfony pour les logs.
    *   Méthodes : `debug()`, `info()`, `notice()`, `warning()`, `error()`, `critical()`, `alert()`, `emergency()`.
*   **PSR-6 & PSR-16 (Caching)** :
    *   **PSR-6 (Cache Interface)** : Système complexe et puissant (`CacheItemPoolInterface`, `CacheItemInterface`).
    *   **PSR-16 (Simple Cache)** : Version simplifiée pour des cas d'usage basiques (`get()`, `set()`, `delete()`).
*   **PSR-11 (Container Interface)** : Définit `Psr\Container\ContainerInterface`.
    *   Standardise l'accès au conteneur de services avec `get($id)` et `has($id)`.
    *   Symfony l'utilise pour ses Service Locators.
*   **PSR-14 (Event Dispatcher)** : Standardise la distribution d'événements (`EventDispatcherInterface`, `ListenerProviderInterface`).
*   **PSR-20 (Clock)** : Définit `Psr\Clock\ClockInterface` pour récupérer l'heure courante de manière testable (mockable).

### 4. HTTP (Requêtes et Réponses)
Bien que Symfony utilise historiquement son propre composant `HttpFoundation`, il fournit un pont (bridge) pour supporter ces PSRs.

*   **PSR-7 (HTTP Message Interface)** : Interfaces pour les requêtes et réponses HTTP (`ServerRequestInterface`, `ResponseInterface`, `UriInterface`).
    *   Point clé : Les objets PSR-7 sont **immuables** (chaque modification renvoie une nouvelle instance), contrairement aux objets `Request`/`Response` de Symfony qui sont mutables.
*   **PSR-15 (HTTP Handlers)** : Standard pour les Middlewares (`RequestHandlerInterface`, `MiddlewareInterface`).
*   **PSR-17 (HTTP Factories)** : Standard pour créer les objets HTTP PSR-7 (RequestFactory, UriFactory, etc.).
*   **PSR-18 (HTTP Client)** : Standard pour les clients HTTP (envoi de requêtes).

## Application dans Symfony
Symfony est très respectueux des standards :
1.  **Implémentation directe** : Le composant `Cache` implémente PSR-6 et PSR-16. Le composant `Monolog` (via Bridge) implémente PSR-3.
2.  **Adaptateurs** : Pour HTTP, le `psr7-bridge` convertit les objets Symfony en objets PSR-7 et vice-versa.
3.  **Autoloading** : Tout le framework est chargé via Composer selon PSR-4.

## ⚠️ Points de vigilance (Certification)
*   **Immutabilité PSR-7** : Une question piège classique concerne la différence entre `Symfony\Component\HttpFoundation\Request` (mutable) et `Psr\Http\Message\ServerRequestInterface` (immuable).
    *   Symfony : `$request->headers->set('X-Foo', 'Bar');` (Modifie l'objet)
    *   PSR-7 : `$newRequest = $request->withHeader('X-Foo', 'Bar');` (Renvoie un nouvel objet)
*   **PSR-1 vs PSR-12** : PSR-1 concerne ce qui est *requis* pour l'interopérabilité (noms de classes, balises), PSR-12 concerne le *style* (où mettre les accolades, les espaces).
*   **Dépréciations** : PSR-2 est "Abandoned" (remplacé par PSR-12). PSR-0 est "Deprecated" (remplacé par PSR-4).

## Ressources
*   [PHP-FIG Website](https://www.php-fig.org/)
*   [Liste des PSRs](https://www.php-fig.org/psr/)
