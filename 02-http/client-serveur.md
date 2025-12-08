# Interaction Client / Serveur & Cycle de Vie

## Concept clé
Le modèle Client-Serveur (Request-Response) est l'architecture fondamentale du Web HTTP.
1.  **Client** : Initiateur (Navigateur, `curl`, Mobile App). Envoie une **Request**.
2.  **Serveur** : Processeur. Reçoit, traite, renvoie une **Response**.
3.  **Stateless** : Le protocole HTTP ne garde pas de mémoire entre deux requêtes. Tout état (Session, Panier) doit être reconstruit à chaque appel.

## Architecture Shared-Nothing (PHP-FPM) vs Workers
*   **Modèle Classique (PHP-FPM)** : Pour chaque requête entrante, un processus PHP vierge démarre, charge le framework, traite la requête, renvoie la réponse, et **MEURT**. La mémoire est vidée. Rien n'est partagé. C'est robuste (fuites de mémoire limitées) mais a un coût de démarrage (bootstrapping).
*   **Modèle Worker (FrankenPHP, RoadRunner, Symfony Runtime)** : L'application démarre une fois (Bootstrapping). Une boucle reçoit les requêtes et les traite avec la même instance d'application. Plus performant, mais attention aux fuites de mémoire et aux services avec état (Stateful services).

## Application dans Symfony 7.0 : Le cycle de vie
Symfony modélise ce flux objet : `Request -> Kernel -> Response`.

### Les 5 étapes du Kernel `HttpKernel::handle()`

1.  **`kernel.request`** (Early Request) :
    *   C'est ici qu'interviennent les Firewalls (Sécurité), la détection de Locale, les redirections globales.
    *   Si un Listener retourne une `Response` ici, les étapes suivantes sont sautées (ex: Redirection, Access Denied).
2.  **`kernel.controller`** (Resolution) :
    *   Le `ControllerResolver` a déterminé quel contrôleur appeler (`App\Controller\BlogController::index`).
    *   C'est le moment de modifier le contrôleur (ex: `ParamConverter` / `ArgumentResolver`).
3.  **`kernel.controller_arguments`** :
    *   Résolution des arguments à passer à la méthode (Autowiring services, paramètres de route `{id}`, Objet `Request`).
4.  **Exécution du Contrôleur** :
    *   Votre code métier s'exécute. Il **DOIT** retourner un objet `Response`.
5.  **`kernel.view`** (Optionnel) :
    *   Appelé UNIQUEMENT si le contrôleur ne renvoie PAS une `Response` (ex: un array, ou null). Utilisé par `FOSRestBundle` ou API Platform pour sérialiser automatiquement les données.
6.  **`kernel.response`** :
    *   La réponse est prête mais pas encore envoyée.
    *   Dernière chance pour modifier les headers (Cookies, CORS, Cache-Control, Web Debug Toolbar injection).
7.  **`kernel.finish_request` / `kernel.terminate`** :
    *   La réponse a été envoyée au client.
    *   Traitement lourd post-réponse (envoi d'emails non bloquants, logs).

## Front Controller

C'est le point d'entrée unique (`public/index.php`).

```php
<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Le composant Runtime (Symfony 5.3+) abstrait la création du Kernel
// Il permet de tourner aussi bien sur Apache/FPM que sur RoadRunner/FrankenPHP sans changer le code.
return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

## Simulation du cycle

```php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

// Création manuelle (pour tests ou sous-requêtes)
$request = Request::create('/api/posts', 'GET');

// handle() est la méthode clé de l'interface HttpKernelInterface
$response = $kernel->handle(
    $request,
    HttpKernelInterface::MAIN_REQUEST, // ou SUB_REQUEST
    true // $catch: Attraper les exceptions et retourner une réponse d'erreur ?
);

$response->send();

// Clean up
$kernel->terminate($request, $response);
```

## 🧠 Concepts Clés
1.  **Sous-requêtes (Sub-requests)** : Symfony peut simuler une requête interne (ex: `{{ render(controller(...)) }}` dans Twig). Elle repasse par tout le cycle (`kernel.request` -> Controller -> `kernel.response`) mais avec le type `HttpKernelInterface::SUB_REQUEST`.
2.  **Front Controller Pattern** : Tout le trafic passe par un seul fichier PHP (`index.php`). Cela centralise la configuration et la sécurité, contrairement à l'ancien style (un fichier PHP par page).
3.  **Request/Response Objects** : Ce sont des abstractions de la spécification HTTP. Ils ne contiennent pas de logique métier, juste des données HTTP.

## ⚠️ Points de vigilance (Certification)
*   **Services Stateful** : Dans le modèle classique PHP-FPM, un service qui stocke des données (`private $data = []`) est vidé à chaque requête. Dans un modèle Worker (Swoole/FrankenPHP), ce tableau persisterait entre les requêtes ! Règle d'or : Les services doivent être **Stateless**.
*   **Exit/Die** : Ne jamais utiliser `die()` ou `exit()` dans Symfony. Cela coupe le cycle du Kernel, empêche les événements `kernel.terminate` de s'exécuter et casse les tests. Toujours retourner une `Response`.
*   **`Request::createFromGlobals()`** : Utilisé uniquement dans `index.php`. Dans vos contrôleurs, **toujours** injecter l'objet `Request` via l'argument de la méthode.

## Ressources
*   [Symfony Docs - Request-Response Lifecycle](https://symfony.com/doc/current/http_kernel.html)
*   [Symfony Runtime Component](https://symfony.com/doc/current/components/runtime.html)
