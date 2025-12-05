# Traitement de la Requête (Request Handling - HttpKernel)

## Concept clé
Comprendre comment `HttpKernel::handle()` transforme une `Request` en `Response` est la compétence la plus fondamentale pour un architecte Symfony.

## Le Pipeline (Events) en Détail

Chaque étape correspond à un événement dispatché par `HttpKernel`.

### 1. `kernel.request` (Early Stage)
*   **Event** : `RequestEvent`.
*   **Rôle** : Pré-traitement, Sécurité, Routing.
*   **Acteurs** :
    *   `RouterListener` : Parse l'URL et remplit `$request->attributes` (`_route`, `_controller`).
    *   `LocaleListener` : Définit la locale.
    *   `Firewall` (Security) : Authentifie l'utilisateur ou lance `AccessDeniedException`.
*   **Sortie possible** : Si un listener set une `Response` (ex: redirection), on saute directement à l'étape 6 (`kernel.response`).

### 2. `kernel.controller` (Resolution)
*   **Event** : `ControllerEvent`.
*   **Rôle** : Le `ControllerResolver` a trouvé le callable (Classe::Méthode) à appeler. C'est le moment de modifier ce choix ou d'initialiser le contrôleur.
*   **Acteurs** : `ParamConverter` (préparation), `@IsGranted` checks.

### 3. `kernel.controller_arguments` (Arguments)
*   **Event** : `ControllerArgumentsEvent`.
*   **Rôle** : Le `ArgumentResolver` calcule les valeurs à passer à la méthode (Autowiring services, Entity via ID, Request, UserInterface).
*   **Acteurs** : `EntityValueResolver`, `ServiceValueResolver`.

### 4. Exécution du Contrôleur (The Core)
*   Le Kernel appelle `$controller(...$arguments)`.
*   C'est **votre** code.

### 5. `kernel.view` (Post-Processing - Optionnel)
*   **Event** : `ViewEvent`.
*   **Quand** : UNIQUEMENT si le contrôleur ne retourne PAS une `Response`.
*   **Rôle** : Transformer le résultat brut en Response.
*   **Acteurs** : API Platform (s'active ici pour sérialiser l'objet retourné en JSON/LD). Si non géré, le Kernel lance une erreur "Controller must return a Response".

### 6. `kernel.response` (Late Stage)
*   **Event** : `ResponseEvent`.
*   **Rôle** : Modification globale de la réponse.
*   **Acteurs** :
    *   `WebDebugToolbarListener` : Injecte la barre de debug (en dev).
    *   `ContextListener` (Security) : Sauvegarde l'utilisateur en session.
    *   `ResponseListener` : Fixe le charset et le Content-Type.
    *   Ajout de cookies, compression Gzip, Headers CORS.

### 7. `kernel.finish_request` (Cleanup)
*   **Event** : `FinishRequestEvent`.
*   **Rôle** : Reset de l'état global (ex: Translator locale) pour ne pas polluer la requête suivante (ou la requête parente dans le cas d'une sous-requête).

### 8. `kernel.terminate` (Post-Send)
*   **Event** : `TerminateEvent`.
*   **Quand** : APRES `$response->send()`. L'utilisateur a déjà sa page.
*   **Rôle** : Tâches lourdes non-bloquantes pour l'user.
*   **Acteurs** : Envoi d'emails (si spool mémoire), Logs.

## 🧠 Concepts Clés
1.  **Resolver** :
    *   `ControllerResolverInterface` : `Request` -> `callable`.
    *   `ArgumentResolverInterface` : `Request` + `callable` -> `array arguments`.
2.  **Sub-Requests** : Les événements sont dispatchés pour la requête principale (`MAIN_REQUEST`) ET les sous-requêtes (`SUB_REQUEST`, ex: `{{ render() }}`). La plupart des listeners doivent vérifier `$event->isMainRequest()` pour ne pas s'exécuter inutilement sur les fragments.

## ⚠️ Points de vigilance (Certification)
*   **Exception** : Si une exception survient n'importe quand, on saute à `kernel.exception`.
*   **Ordre** : Request -> Controller -> Arguments -> View -> Response -> Terminate.
*   **Type-Hinting** : Pour créer un ArgumentResolver personnalisé (ex: injecter `UserDTO $user` automatiquement), il faut implémenter `ValueResolverInterface` (depuis Symfony 6.2, remplace `ArgumentValueResolverInterface`).

## Ressources
*   [Symfony HttpKernel Component](https://symfony.com/doc/current/components/http_kernel.html)
*   [The HttpKernel Events](https://symfony.com/doc/current/reference/events.html#http-kernel-events)
