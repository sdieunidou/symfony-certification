# Portée de Validation (Validation Scopes - Cascade)

## Concept clé
Par défaut, la validation ne traverse **pas** les objets.
Si vous avez un objet `Order` qui contient un objet `Address`, valider `Order` ne validera pas les contraintes à l'intérieur de `Address` (comme le code postal), sauf si vous le demandez explicitement.

## La Contrainte `Valid`
Pour activer la "Cascade de validation", il faut utiliser la contrainte spéciale `#[Assert\Valid]`.

```php
class Order
{
    #[Assert\NotBlank]
    public string $reference;

    // Dit au validateur : "Rentre dans cet objet et valide-le aussi"
    #[Assert\Valid]
    public Address $shippingAddress;
    
    // Fonctionne aussi sur les collections (array/ArrayCollection)
    // Valide chaque item Product de la liste
    #[Assert\Valid]
    public array $products = [];
}

class Address
{
    #[Assert\NotBlank]
    public string $city;
}
```

## Gestion des Groupes en Cascade
Si vous validez `Order` avec le groupe `registration`, Symfony essaiera de valider `Address` avec le groupe `registration` aussi.
Si `Address` n'a pas de contraintes dans ce groupe, rien ne sera validé.

Si vous voulez mapper les groupes (ex: valider `Order` en `Default` doit déclencher `Address` en `Strict`), utilisez l'option `traverse` (complexe et rare).

## 🧠 Concepts Clés
1.  **Profondeur** : La validation descend récursivement dans l'arbre d'objets tant qu'elle rencontre `#[Valid]`.
2.  **Circularité** : Le composant Validator détecte et gère les références circulaires (A -> B -> A) pour éviter les boucles infinies.

## ⚠️ Points de vigilance (Certification)
*   **Oubli** : C'est la source d'erreur #1. "J'ai mis `@NotBlank` dans `Address` mais ça ne marche pas !". Réponse : Avez-vous mis `@Valid` sur la propriété `$address` dans la classe parente ?
*   **Formulaires** : Le composant Form ajoute automatiquement `#[Valid]` si vous imbriquez des formulaires (`options['cascade_validation']` qui est true par défaut sur les enfants). Mais pour la validation d'objets purs (API/DTO), il faut l'ajouter manuellement.

## Ressources
*   [Symfony Docs - Valid Constraint](https://symfony.com/doc/current/reference/constraints/Valid.html)
