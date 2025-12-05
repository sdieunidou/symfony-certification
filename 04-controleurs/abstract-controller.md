# La classe AbstractController

## Concept clé
Dans Symfony, un contrôleur peut être n'importe quel "Callable" PHP (fonction, classe invokable, closure).
Cependant, la pratique standard est de créer une classe qui étend `Symfony\Bundle\FrameworkBundle\Controller\AbstractController`.
Cette classe de base fournit des méthodes utilitaires (helpers) pour accéder aux fonctionnalités courantes du framework sans avoir à injecter 50 services dans le constructeur.

## Injection de Dépendances : Le Container Bag
`AbstractController` implémente `ServiceSubscriberInterface`.
Cela signifie qu'il utilise un **Service Locator** (mini-conteneur) contenant uniquement les services dont il a besoin.
*   Avantage : Le contrôleur est léger à instancier (Lazy loading des helpers).
*   Inconvénient : On ne peut pas accéder à *tous* les services via `$this->container->get()`, seulement ceux "abonnés".

## Liste Exhaustive des Helpers (Symfony 7)

### Rendu & Réponse
*   `render(string $view, array $parameters = [], Response $response = null): Response` : Rend un template Twig.
*   `renderBlock(string $view, string $block, array $parameters = []): Response` : Rend un bloc spécifique d'un template.
*   `renderView(string $view, array $parameters = []): string` : Retourne le HTML sous forme de string (sans créer de Response).
*   `json($data, int $status = 200, array $headers = [], array $context = []): JsonResponse` : Sérialise et retourne du JSON.
*   `file($file, $fileName = null, ...): BinaryFileResponse` : Sert un fichier en téléchargement.
*   `stream(callable $callback, ...): StreamedResponse` : Sert une réponse streamée.

### Routing & Redirection
*   `redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse`
*   `redirect(string $url, int $status = 302): RedirectResponse`
*   `generateUrl(string $route, array $parameters = [], int $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH): string`

### Exceptions HTTP
*   `createNotFoundException(string $message = 'Not Found', Throwable $previous = null): NotFoundHttpException`
*   `createAccessDeniedException(...)` : 403.

### Sécurité & User
*   `getUser(): ?UserInterface` : L'utilisateur connecté (ou null).
*   `isGranted(mixed $attribute, mixed $subject = null): bool` : Vérifie une permission.
*   `denyAccessUnlessGranted(...)` : Lance une exception si pas autorisé.

### Session & Flash
*   `addFlash(string $type, mixed $message): void`

### Autres
*   `getParameter(string $name): mixed` : Récupère un paramètre de `services.yaml`.
*   `createForm(string $type, $data = null, array $options = []): FormInterface`

## Bonnes Pratiques d'Architecture

### 1. Injection Constructeur vs Helpers
Pour vos propres services, préférez toujours l'**Injection Constructeur**.
Pour les services "Framework" (Router, Twig, AuthorizationChecker), utilisez les méthodes de l'`AbstractController` pour alléger le code.

```php
class BlogController extends AbstractController
{
    public function __construct(
        private BlogManager $manager // Mon service métier -> Constructeur
    ) {}

    public function index(): Response
    {
        // Service Framework -> Helper
        if (!$this->isGranted('ROLE_USER')) { ... }
        
        return $this->render(...);
    }
}
```

### 2. Controller déprécié
Avant Symfony 4, on utilisait la classe `Controller`. Elle est dépréciée et retirée. Elle injectait tout le conteneur public. `AbstractController` est plus strict et performant.

## 🧠 Concepts Clés
1.  **this->container** : La propriété existe mais c'est un `ContainerBag`, pas le conteneur global. Faire `$this->container->get('my.service')` échouera si le service n'est pas listé dans `getSubscribedServices()`.
2.  **Traits** : `AbstractController` utilise le `ControllerTrait`. Vous pouvez théoriquement utiliser ce trait dans vos propres classes sans hériter de AbstractController, mais l'héritage est plus simple.

## ⚠️ Points de vigilance (Certification)
*   **getParameter** : Permet de lire les paramètres (`%app.admin_email%`). Ne permet PAS de lire les variables d'environnement brutes (`$_ENV`). Les variables d'env doivent être mappées vers des paramètres dans `services.yaml` pour être lues ici.
*   **getUser** : Pensez à vérifier `if (!$this->getUser())` ou typer la variable dans une docblock si vous êtes sûr qu'il est connecté (via firewall access control).

## Ressources
*   [Symfony Docs - AbstractController](https://symfony.com/doc/current/controller.html#the-base-controller-class-abstractcontroller)
*   [API AbstractController](https://github.com/symfony/framework-bundle/blob/7.0/Controller/AbstractController.php)
