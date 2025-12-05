# Documentation API (OpenAPI)

## Pourquoi documenter ?
Une API sans documentation est inutilisable.
Le standard mondial est **OpenAPI** (anciennement Swagger). C'est un fichier JSON ou YAML qui décrit :
*   Les routes disponibles.
*   Les paramètres attendus.
*   Les formats de réponse (schémas).
*   L'authentification nécessaire.

## NelmioApiDocBundle
Symfony n'a pas de générateur OpenAPI natif dans le core. On utilise quasi-systématiquement **NelmioApiDocBundle**.

Il permet de générer la spec OpenAPI automatiquement à partir :
1.  Des routes Symfony.
2.  Des types PHP (DTOs, Entités) et contraintes de validation.
3.  Des PhpDoc ou Attributs spécifiques.

### Exemple d'Attributs

```php
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class UserController
{
    #[Route('/api/users', methods: ['POST'])]
    #[OA\Response(
        response: 201,
        description: 'Utilisateur créé',
        content: new OA\JsonContent(ref: new Model(type: User::class, groups: ['read']))
    )]
    #[OA\RequestBody(
        content: new OA\JsonContent(ref: new Model(type: CreateUserDto::class))
    )]
    public function create(#[MapRequestPayload] CreateUserDto $dto)
    {
        // ...
    }
}
```

## Swagger UI
Nelmio fournit aussi une interface graphique (Swagger UI) accessible (ex: `/api/doc`) pour tester l'API directement depuis le navigateur. C'est un outil indispensable pour les développeurs Front ou mobiles qui consomment votre API.

## 🧠 Concepts Clés
1.  **Code-First** : On écrit le code PHP, et la doc est générée (Approche Nelmio/API Platform).
2.  **Design-First** : On écrit le fichier YAML OpenAPI d'abord, puis on code l'implémentation.
3.  **Schemas** : OpenAPI décrit la structure des objets (Schemas). En utilisant `Nelmio\ApiDocBundle\Annotation\Model`, vous liez ces schémas à vos classes PHP, évitant la duplication.

## Ressources
*   [NelmioApiDocBundle Documentation](https://symfony.com/bundles/NelmioApiDocBundle/current/index.html)
*   [OpenAPI Specification](https://swagger.io/specification/)
