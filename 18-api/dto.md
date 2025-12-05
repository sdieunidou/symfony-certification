# Data Transfer Objects (DTO)

## Pourquoi utiliser des DTOs ?
Dans une architecture Symfony simple, on utilise souvent les Entités directement dans les formulaires ou les vues.
Dans une API, exposer ou hydrater directement les entités présente des risques :

1.  **Sécurité** : Risque de "Mass Assignment". Un utilisateur malveillant pourrait injecter un champ `is_admin: true` dans le JSON si l'entité est directement désérialisée.
2.  **Couplage** : L'API doit être stable. Si vous renommez une colonne en BDD, vous ne voulez pas casser le contrat d'interface de votre API publique.
3.  **Logique** : Le format de réception (ex: création de compte avec `password` + `confirm_password`) est souvent différent du format de stockage (Entité `User` avec `passwordHash`).

Le DTO est un objet PHP simple (POPO) qui représente la structure de la donnée attendue en entrée ou sortie.

## Flux de traitement moderne (Symfony 6.3+)

Avec l'attribut `#[MapRequestPayload]`, Symfony automatise le flux : `JSON` -> `DTO` -> `Validation`.

```php
// src/Dto/CreateUserDto.php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CreateUserDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public readonly string $email,

        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public readonly string $password,
    ) {}
}
```

```php
// src/Controller/ApiUserController.php
namespace App\Controller;

use App\Dto\CreateUserDto;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiUserController extends AbstractController
{
    #[Route('/api/users', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateUserDto $dto
    ): JsonResponse
    {
        // Si on arrive ici, c'est que :
        // 1. Le JSON était valide
        // 2. Le DTO a été hydraté
        // 3. Les contraintes de validation sont respectées
        
        // Logique métier : Conversion DTO -> Entité
        $user = new User();
        $user->setEmail($dto->email);
        // ...
        
        return $this->json($user, 201);
    }
}
```

## MapRequestPayload vs MapQueryString
*   **`#[MapRequestPayload]`** : Mappe le **corps** de la requête (Body) vers l'objet (pour POST/PUT/PATCH). Gère le JSON, XML, etc. selon le Content-Type.
*   **`#[MapQueryString]`** : Mappe les **paramètres d'URL** (Query String) vers l'objet (pour GET). Utile pour les filtres de recherche (`?page=1&sort=asc&search=foo`).

## Gestion des erreurs (422)
Si la validation échoue lors du mapping, Symfony lance une `UnprocessableEntityHttpException`.
Le gestionnaire d'erreur la transforme automatiquement en réponse 422 contenant la liste des violations au format JSON (si le format demandé est JSON).

## 🧠 Concepts Clés
1.  **Immuabilité** : Les DTOs gagnent à être immuables (`readonly` properties en PHP 8.1+).
2.  **Découplage** : Le DTO agit comme un tampon entre le monde extérieur (API) et votre domaine interne (Entités).

## Ressources
*   [Symfony Docs - Mapping Request Payload](https://symfony.com/doc/current/controller/argument_value_resolver.html#mapping-request-payload)
