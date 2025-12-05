# Attributs PHP natifs de Symfony 7

Symfony 7 exploite massivement les **Attributs PHP 8** pour remplacer les anciennes annotations (SensioFrameworkExtraBundle) et les configurations YAML.
Voici un guide de référence classé par composant.

## Routing (Routage)

*   **`#[Route]`** (`Symfony\Component\Routing\Attribute\Route`)
    Déclare une route sur une classe ou une méthode.
    ```php
    #[Route('/blog', name: 'blog_list', methods: ['GET'])]
    public function list(): Response { ... }
    ```

## HttpKernel & Contrôleurs

*   **`#[AsController]`** (`Symfony\Component\HttpKernel\Attribute\AsController`)
    Marque une classe comme contrôleur (auto-configuration, injection de dépendances). Devenu optionnel si la classe a une `#[Route]`.
    
*   **`#[Cache]`** (`Symfony\Component\HttpKernel\Attribute\Cache`)
    Configure le cache HTTP (Cache-Control, ETag).
    ```php
    #[Cache(public: true, maxage: 3600, smaxage: 7200)]
    public function index(): Response { ... }
    ```

*   **`#[MapRequestPayload]`** (`Symfony\Component\HttpKernel\Attribute\MapRequestPayload`)
    Désérialise et valide le corps de la requête (JSON/XML) dans un objet typé (DTO).
    ```php
    public function create(#[MapRequestPayload] UserDto $userDto): Response { ... }
    ```

*   **`#[MapQueryString]`** (`Symfony\Component\HttpKernel\Attribute\MapQueryString`)
    Mappe les paramètres d'URL (`?sort=asc&page=1`) dans un objet typé.

*   **`#[MapUploadedFile]`** (`Symfony\Component\HttpKernel\Attribute\MapUploadedFile`)
    Injecte un fichier uploadé spécifique.

*   **`#[WithHttpStatus]`** (`Symfony\Component\HttpKernel\Attribute\WithHttpStatus`)
    Définit le code HTTP à renvoyer lorsqu'une exception spécifique est lancée.
    ```php
    #[WithHttpStatus(404)]
    class ProductNotFoundException extends \Exception { ... }
    ```

## Dependency Injection (Services)

*   **`#[AsAlias]`** (`Symfony\Component\DependencyInjection\Attribute\AsAlias`)
    Crée un alias pour le service.
    ```php
    #[AsAlias(id: 'app.mailer', public: true)]
    class MyMailer { ... }
    ```

*   **`#[Autowire]`** (`Symfony\Component\DependencyInjection\Attribute\Autowire`)
    Injecte une valeur spécifique (paramètre, variable d'env, autre service).
    ```php
    public function __construct(
        #[Autowire('%kernel.project_dir%')] string $projectDir,
        #[Autowire(env: 'API_KEY')] string $apiKey
    ) { ... }
    ```

*   **`#[Target]`** (`Symfony\Component\DependencyInjection\Attribute\Target`)
    Cible une implémentation spécifique (alias) quand il y en a plusieurs.
    ```php
    public function __construct(#[Target('filesystem.public')] Filesystem $storage) { ... }
    ```

*   **`#[When]`** (`Symfony\Component\DependencyInjection\Attribute\When`)
    Enregistre le service uniquement dans l'environnement donné.
    ```php
    #[When(env: 'dev')]
    class DebugService { ... }
    ```

*   **`#[Lazy]`** (`Symfony\Component\DependencyInjection\Attribute\Lazy`)
    Rend l'instanciation du service paresseuse (Proxy).

## Security (Sécurité)

*   **`#[IsGranted]`** (`Symfony\Component\Security\Http\Attribute\IsGranted`)
    Vérifie les droits avant d'exécuter le contrôleur.
    ```php
    #[IsGranted('ROLE_ADMIN')]
    public function admin(): Response { ... }
    ```

*   **`#[CurrentUser]`** (`Symfony\Component\Security\Http\Attribute\CurrentUser`)
    Injecte l'utilisateur connecté.
    ```php
    public function profile(#[CurrentUser] ?User $user): Response { ... }
    ```

## EventDispatcher (Événements)

*   **`#[AsEventListener]`** (`Symfony\Component\EventDispatcher\Attribute\AsEventListener`)
    Enregistre la classe ou la méthode comme écouteur d'événement.
    ```php
    #[AsEventListener(event: 'kernel.request', priority: 100)]
    public function onKernelRequest(RequestEvent $event): void { ... }
    ```

## Messenger

*   **`#[AsMessageHandler]`** (`Symfony\Component\Messenger\Attribute\AsMessageHandler`)
    Enregistre la classe comme gestionnaire de message.
    ```php
    #[AsMessageHandler]
    class SmsNotificationHandler {
        public function __invoke(SmsNotification $message) { ... }
    }
    ```

## Console

*   **`#[AsCommand]`** (`Symfony\Component\Console\Attribute\AsCommand`)
    Configure le nom et la description d'une commande.
    ```php
    #[AsCommand(name: 'app:create-user', description: 'Creates a new user')]
    class CreateUserCommand extends Command { ... }
    ```

## Serializer

*   **`#[Groups]`** : Définit les groupes de sérialisation.
*   **`#[Ignore]`** : Ignore la propriété.
*   **`#[SerializedName]`** : Change le nom de la clé JSON.
*   **`#[MaxDepth]`** : Limite la profondeur.

## Validator

Toutes les contraintes sont des attributs : `#[Assert\NotBlank]`, `#[Assert\Email]`, etc.

## Twig

*   **`#[Template]`** (`Symfony\Bundle\TwigBundle\Attribute\Template`)
    Permet de retourner un tableau depuis le contrôleur, qui sera passé au template spécifié.

## Doctrine Bridge

*   **`#[MapEntity]`** (`Symfony\Bridge\Doctrine\Attribute\MapEntity`)
    Configure explicitement le ParamConverter pour récupérer une entité depuis l'URL.
    ```php
    public function show(#[MapEntity(mapping: ['slug' => 'slug'])] Post $post) { ... }
    ```
