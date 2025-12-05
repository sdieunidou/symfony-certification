# Argument Value Resolvers

## Concept clé
C'est la magie qui permet d'injecter n'importe quoi dans les méthodes de vos contrôleurs (`Request`, `UserInterface`, `Product $product`, `SessionInterface`).
Le `HttpKernel` utilise le `ArgumentResolver`, qui boucle sur une liste de `ValueResolverInterface`. Le premier qui supporte l'argument l'injecte.

## Application dans Symfony 7.0
Résolveurs natifs :
*   `RequestValueResolver` : Injecte `Request`.
*   `ServiceValueResolver` : Injecte des services (Logger, Router...).
*   `EntityValueResolver` (Doctrine ParamConverter) : Charge une entité via `{id}`.
*   `SessionValueResolver` : Injecte `SessionInterface`.
*   `DefaultValueResolver` : Utilise la valeur par défaut de l'argument PHP (`$id = 1`).
*   `VariadicValueResolver` : Gère les arguments `...$args`.

## Créer un Resolver personnalisé
Pour injecter automatiquement un objet "DTO" depuis le JSON de la requête, par exemple.

```php
// Depuis Symfony 6.2, l'interface a changé vers ValueResolverInterface
class UserDtoResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if ($argument->getType() !== UserDto::class) {
            return [];
        }
        
        // Désérialiser le JSON en objet
        $dto = $this->serializer->deserialize($request->getContent(), UserDto::class, 'json');
        
        return [$dto];
    }
}
```

## Points de vigilance (Certification)
*   **Ordre** : L'ordre des résolveurs compte, mais en général ils sont assez spécifiques pour ne pas se marcher dessus.
*   **Attributs** : Depuis Symfony 6.3, on peut cibler un résolveur spécifique avec des attributs : `public function show(#[MapEntity] Product $product)`.

## Ressources
*   [Symfony Docs - Controller Arguments](https://symfony.com/doc/current/controller/argument_value_resolver.html)

