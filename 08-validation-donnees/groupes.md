# Groupes de Validation

## Concept clé
Par défaut, toutes les contraintes appartiennent au groupe `Default`.
Parfois, on veut valider seulement une partie de l'objet selon le contexte (ex: "Registration" vs "ProfileUpdate").

## Application dans Symfony 7.0

```php
class User
{
    #[Assert\NotBlank(groups: ['registration', 'profile'])]
    private string $email;

    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Length(min: 8, groups: ['registration'])]
    private string $plainPassword;
}
```

### Validation
```php
// Valide uniquement les règles du groupe 'registration'
$validator->validate($user, null, ['registration']);

// Dans un formulaire
$builder->add(..., [
    'validation_groups' => ['registration'],
]);
```

## Points de vigilance (Certification)
*   **Default** : Si vous validez un groupe spécifique (`registration`), les contraintes sans groupe (donc `Default`) **ne sont pas** validées. Pour valider les deux, il faut passer `['Default', 'registration']`.
*   **Classe** : Le nom de la classe (`User`) est un alias pour le groupe `Default` de cette classe.

## Ressources
*   [Symfony Docs - Validation Groups](https://symfony.com/doc/current/validation/groups.html)

