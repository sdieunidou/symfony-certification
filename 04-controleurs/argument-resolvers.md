# Argument Value Resolvers (Injection dans les méthodes)

## Concept clé
Le composant `HttpKernel` analyse la signature de vos méthodes de contrôleur (Type Hinting) et utilise des **Value Resolvers** pour injecter la bonne valeur pour chaque argument.
C'est le coeur de la "Magie" Symfony : avoir accès à la Request, à l'User, à l'Entité courante, juste en le demandant.

## Nouveautés Symfony 6.3+ : Les Attributs Mappés
Symfony a introduit des attributs puissants pour contrôler explicitement comment les arguments sont résolus, rendant le code plus robuste et lisible.

### 1. `#[MapEntity]` (Remplacement ParamConverter)
Remplace les annotations `@ParamConverter` de `SensioFrameworkExtraBundle` (qui est abandonné).
Permet de charger une entité Doctrine depuis l'URL (id, slug) ou le body.

```php
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

#[Route('/post/{slug}')]
public function show(
    #[MapEntity(mapping: ['slug' => 'slug'])] Post $post
): Response
```
*   **Auto** : Souvent, `public function show(Post $post)` suffit (mapping implicite `{id}` ou `{post_id}`).
*   **404** : Lance automatiquement une 404 si non trouvé (sauf si l'argument est nullable `?Post $post`).

### 2. `#[MapRequestPayload]` (DTOs & Validation)
Désérialise le corps de la requête (JSON, XML, Form) vers un objet typé (DTO) et le **valide**.

```php
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

public function create(
    #[MapRequestPayload] CreatePostDto $dto
): Response
{
    // Ici, $dto est hydraté ET validé.
    // Si validation échoue -> 422 Unprocessable Entity automatique.
    
    $this->handler->handle($dto);
    return $this->json($dto);
}
```

### 3. `#[MapQueryParameter]`
Injecte et valide un paramètre de l'URL (Query String `?filter=...`).

```php
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

public function list(
    #[MapQueryParameter] string $filter = 'all',
    #[MapQueryParameter] int $page = 1,
    #[MapQueryParameter(filter: \FILTER_VALIDATE_EMAIL)] ?string $searchEmail = null
): Response
```

### 4. `#[CurrentUser]`
Injecte l'utilisateur connecté.

```php
use Symfony\Component\Security\Http\Attribute\CurrentUser;

public function dashboard(#[CurrentUser] User $user): Response
```

## Résolveurs Natifs (Sans Attributs)
Si aucun attribut n'est présent, Symfony essaie les résolveurs par défaut :
1.  **Request** : `Request`
2.  **Session** : `SessionInterface`
3.  **Service** : N'importe quel service (LoggerInterface, RouterInterface...)
4.  **UID** : `Uuid`, `Ulid` (depuis l'URL)
5.  **Default** : Valeur par défaut PHP (`$id = 1`).

## Création d'un Résolveur Custom
Implémenter `Symfony\Component\HttpKernel\Controller\ValueResolverInterface`.

```php
class UserIpResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== UserIp::class) {
            return [];
        }
        
        return [new UserIp($request->getClientIp())];
    }
}
```

## 🧠 Concepts Clés
1.  **Injection de Service** : C'est le `ServiceValueResolver` qui permet d'injecter des services dans les méthodes de contrôleur. C'est le seul endroit dans Symfony où l'injection de méthode est standard (ailleurs, c'est constructeur).
2.  **Variadic** : Vous pouvez utiliser `...$args` pour récupérer le reste des paramètres.

## ⚠️ Points de vigilance (Certification)
*   **ParamConverter** : Le terme "ParamConverter" réfère historiquement à la librairie `SensioFrameworkExtraBundle`. Dans Symfony 7, on parle de `ValueResolver` et d'attributs natifs (`MapEntity`). Savoir que `Sensio` est déprécié est un point bonus.
*   **Validation** : `#[MapRequestPayload]` déclenche la validation (Constraints Validator). Si l'objet DTO contient des contraintes (`#[Assert\NotBlank]`), elles sont vérifiées. En cas d'échec, une `UnprocessableEntityHttpException` (422) est lancée.

## Ressources
*   [Symfony Docs - Controller Arguments](https://symfony.com/doc/current/controller/argument_value_resolver.html)
*   [Mapping Request Data to Typed Objects](https://symfony.com/doc/current/controller/argument_value_resolver.html#mapping-request-payload)
