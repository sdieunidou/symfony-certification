# Portées de Validation (Validation Scopes)

## Concept clé
La validation ne se limite pas à une seule classe. Elle peut "cascader" sur les objets liés (Relations).
Si un `Article` contient un auteur `User`, valider l'article doit-il valider l'auteur ?

## Application dans Symfony 7.0
Utilisation de la contrainte `Valid`.

```php
class Order
{
    #[Assert\Valid]
    private Address $shippingAddress;
}

class Address
{
    #[Assert\NotBlank]
    private string $city;
}
```

Quand on valide `Order`, le validateur voit `#[Assert\Valid]`, descend dans l'objet `shippingAddress`, et valide ses contraintes (`city`). Sans cela, seule la présence de l'objet Address serait validée (si `NotNull` est présent), mais pas son contenu.

## Points de vigilance (Certification)
*   **Circularité** : Le validateur gère les références circulaires (A -> B -> A) pour ne pas boucler à l'infini.
*   **Traversable** : Fonctionne aussi sur les collections (tableaux d'objets). `#[Assert\Valid]` sur `private array $items` validera chaque item.

## Ressources
*   [Symfony Docs - Valid Constraint](https://symfony.com/doc/current/reference/constraints/Valid.html)

