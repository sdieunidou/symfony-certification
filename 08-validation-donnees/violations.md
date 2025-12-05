# Constructeur de Violations (Violations Builder)

## Concept clé
L'API utilisée pour créer des erreurs de validation personnalisées, que ce soit dans un Callback ou un Validateur personnalisé.

## Application dans Symfony 7.0
L'objet `ConstraintViolationBuilder` (retourné par `$context->buildViolation()`).

```php
$context->buildViolation('Cette valeur est invalide.')
    ->setParameter('{{ value }}', $value) // Pour la traduction
    ->atPath('property_name') // Cibler une propriété spécifique (utile pour validation de classe globale)
    ->setTranslationDomain('validators')
    ->addViolation();
```

## Créer une Contrainte Personnalisée
1.  **La Contrainte** (Annotation/Attribut) : Classe héritant de `Constraint`.
2.  **Le Validateur** : Classe héritant de `ConstraintValidator`.

```php
// 1. La Contrainte
#[\Attribute]
class ContainsAlphanumeric extends Constraint
{
    public string $message = 'La chaîne "{{ string }}" contient des caractères interdits.';
}

// 2. Le Validateur
class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
```

## Points de vigilance (Certification)
*   **Alias** : Par convention, le validateur d'une contrainte `MyConstraint` est `MyConstraintValidator`. Si vous respectez ça, Symfony le trouve tout seul.
*   **Service** : Les validateurs sont des services. Vous pouvez y injecter des dépendances (EntityManager, RequestStack...).

## Ressources
*   [Symfony Docs - Custom Constraints](https://symfony.com/doc/current/validation/custom_constraint.html)

