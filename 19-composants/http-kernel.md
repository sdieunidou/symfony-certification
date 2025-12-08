# Le Composant HttpKernel

Le composant **HttpKernel** est le cœur du framework Symfony. Il orchestre le traitement d'une `Request` pour la transformer en `Response` via une architecture événementielle stricte.

C'est ce composant qui définit le "Cycle de vie" d'une requête Symfony.

---

## 1. Le Workflow `handle()`

La méthode principale est `HttpKernel::handle()`. Voici les étapes chronologiques traversées par chaque requête :

1.  **`kernel.request`** :
    *   C'est le tout début. La requête est reçue mais pas encore routée.
    *   *Usage typique* : Ajouter des informations globales, gérer la sécurité (Firewall), déterminer la Locale, Redirection anticipée (Maintenance mode).
    *   *Si un listener renvoie une Response ici, le traitement s'arrête et on saute directement à `kernel.response`.*

2.  **Résolution du Contrôleur** (`ControllerResolver`) :
    *   Le Kernel utilise le résultat du Routing (stocké dans `$request->attributes->get('_controller')`) pour déterminer quel callable PHP (classe + méthode) doit être exécuté.

3.  **`kernel.controller`** :
    *   Le contrôleur a été trouvé mais pas encore exécuté.
    *   *Usage typique* : Modifier le contrôleur à la volée, initialiser des données lourdes avant l'exécution, attributs `#[IsGranted]`.

4.  **Résolution des Arguments** (`ArgumentResolver`) :
    *   Le Kernel analyse la signature de la méthode du contrôleur pour injecter les bonnes valeurs (`Request $request`, `User $user`, `$id`).

5.  **`kernel.controller_arguments`** :
    *   Les arguments sont résolus mais pas encore passés à la méthode.
    *   *Usage typique* : Modifier un argument juste avant l'appel (rare).

6.  **Exécution du Contrôleur** :
    *   Le code utilisateur est exécuté. Il doit retourner une `Response` ou autre chose (vue, tableau, null).

7.  **`kernel.view`** (Optionnel) :
    *   N'est déclenché **QUE** si le contrôleur ne retourne **PAS** une `Response`.
    *   *Usage typique* : Convertir la valeur de retour en Response. C'est la base d'**API Platform** ou de `FOSRestBundle` (convertir un Array/Object en JSON).

8.  **`kernel.response`** :
    *   Une `Response` valide a été créée. C'est la dernière chance de la modifier avant l'envoi au client.
    *   *Usage typique* : Ajouter des headers HTTP globaux (CORS), compresser la réponse (Gzip), ajouter des cookies, modifier le contenu HTML (WebProfilerToolbar).

9.  **`kernel.finish_request`** :
    *   Déclenché après le traitement d'une requête (principale ou sous-requête).
    *   *Usage typique* : Reset de la Locale ou de l'état global de l'application.

10. **`kernel.terminate`** :
    *   Déclenché **APRÈS** que la réponse ait été envoyée au client (via `fastcgi_finish_request()`).
    *   *Usage typique* : Tâches lourdes qui ne nécessitent pas de faire attendre l'utilisateur (envoi de mails sans Messenger, génération de logs).

11. **`kernel.exception`** :
    *   Déclenché si une exception est levée n'importe où dans le processus.
    *   *Usage typique* : Transformer l'Exception en une `Response` d'erreur (Page 404, 500 personnalisée).

---

## 2. Sous-Requêtes (Sub-Requests)

Le Kernel peut traiter des requêtes imbriquées (ex: `{{ render(controller('...')) }}` dans Twig ou `$this->forward()`).

*   **Main Request** (`HttpKernelInterface::MAIN_REQUEST`) : La requête principale provenant du navigateur.
*   **Sub Request** (`HttpKernelInterface::SUB_REQUEST`) : Une requête interne.

Les écouteurs d'événements doivent souvent vérifier le type de requête pour ne pas s'exécuter deux fois.

```php
public function onKernelRequest(RequestEvent $event): void
{
    if (!$event->isMainRequest()) {
        return;
    }
    // ...
}
```

---

## 3. Les Resolvers

### ControllerResolver
Il transforme le paramètre `_controller` de la Request (ex: `App\Controller\HomeController::index`) en un `callable` PHP exécutable.

### ArgumentResolver
Il utilise la Reflection pour mapper les arguments de la méthode. Il supporte nativement :
*   `Request`
*   `SessionInterface`
*   `UserInterface` (si SecurityBundle)
*   Paramètres de route (`$id`, `$slug`)
*   Service (si typé et autowiré)
*   `DefaultValue` (si argument optionnel)
*   `Variadic` (`...$args`)

On peut créer ses propres `ValueResolverInterface` pour injecter des objets custom (ex: transformer un ID en Entité Doctrine - c'est ce que fait le `EntityValueResolver`).

---

## Fonctionnement Interne

### Architecture
*   **HttpKernel** : Le chef d'orchestre.
*   **EventDispatcher** : Le moteur qui propulse le kernel.
*   **ControllerResolver** : Trouve le callable PHP.
*   **ArgumentResolver** : Trouve les arguments.

### Le Flux
1.  **handle()** : Point d'entrée.
2.  **Dispatch Loop** : Le kernel dispatch successivement les événements `kernel.request`, `kernel.controller`, `kernel.view`, `kernel.response`.
3.  **Exception Handling** : Le tout est wrappé dans un `try/catch` géant qui dispatch `kernel.exception` en cas de pépin.

## 4. Points de vigilance pour la Certification

*   **Ordre des événements** : Il est CRUCIAL de connaître l'ordre par cœur.
    *   Rappel : `Request` -> `Controller` -> `Arguments` -> (`View` si pas de Response) -> `Response` -> `Terminate`.
*   **Exception Handling** : Le Kernel attrape toutes les exceptions. S'il n'y a pas de listener sur `kernel.exception` qui retourne une Response, il affiche la page d'erreur par défaut de PHP/Symfony.
*   **TraceableHttpKernel** : En mode dev, c'est une sous-classe qui wrap le Kernel pour le profiler (Stopwatch).
