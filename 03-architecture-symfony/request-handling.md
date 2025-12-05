# Traitement de la Requête (Request Handling)

## Concept clé
Le cycle de vie d'une requête dans Symfony suit un chemin précis géré par le `HttpKernel`. C'est le cœur du fonctionnement du framework.

## Application dans Symfony 7.0
Le flux :
1.  **Front Controller** (`public/index.php`) : Reçoit la requête, boote le Kernel.
2.  **Kernel** : Appelle `HttpKernel::handle($request)`.
3.  **Events** : Le `HttpKernel` dispatche des événements pour transformer la requête en réponse.

### Les étapes clés (Events) :
1.  `kernel.request` : Très tôt. Peut retourner une `Response` immédiatement (ex: redirection, maintenance). Sinon, le routing détermine le contrôleur.
2.  `kernel.controller` : Le contrôleur est déterminé. On peut le modifier ou faire de l'initialisation.
3.  `kernel.controller_arguments` : Résolution des arguments (Autowiring, Value Resolvers).
4.  **Exécution du Contrôleur** : Votre code est exécuté. Il retourne une `Response`.
5.  `kernel.view` : (Optionnel) Si le contrôleur ne retourne pas une `Response` (ex: un tableau ou null), cet événement doit transformer le résultat en `Response`. Utilisé par API Platform ou `@View`.
6.  `kernel.response` : La réponse est prête. On peut modifier les headers, compresser, ajouter des cookies.
7.  `kernel.finish_request` : Nettoyage (reset des locales, etc.).
8.  `kernel.terminate` : Après l'envoi au client (`$response->send()`). Pour les tâches lourdes (envoi email, logging).

## Exemple de code

```php
// index.php simplifié
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
$response = $kernel->handle($request); // Tout se passe ici
$response->send();
$kernel->terminate($request, $response);
```

## Points de vigilance (Certification)
*   **Ordre** : Connaître l'ordre exact des événements est **crucial** pour la certification.
    *   Request -> Controller -> ControllerArguments -> (Controller execution) -> View (si besoin) -> Response -> Terminate.
*   **Exceptions** : Si une exception est lancée à n'importe quel moment, l'événement `kernel.exception` est dispatché pour tenter de créer une réponse d'erreur.

## Ressources
*   [Symfony Docs - The HttpKernel Component](https://symfony.com/doc/current/components/http_kernel.html)

