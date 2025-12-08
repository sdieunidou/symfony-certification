# Violations et Contraintes Personnalisées

## Concept clé
Quand les contraintes natives ne suffisent pas, vous devez créer vos propres règles.
Cela implique trois concepts :
1.  **La Contrainte** (`Constraint`) : La définition (Attribute/Options). C'est un DTO.
2.  **Le Validateur** (`ConstraintValidator`) : La logique de validation.
3.  **La Violation** (`ConstraintViolation`) : L'erreur générée via le `ViolationBuilder`.

## 1. Créer la Contrainte (Classe)
La classe de contrainte doit étendre `Symfony\Component\Validator\Constraint`.

### Structure de base et Attributs
Depuis PHP 8, on utilise les attributs pour définir la contrainte. L'attribut `#[Attribute]` est nécessaire pour l'utiliser comme tel.

```php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Attribute\HasNamedArguments;

#[\Attribute]
class ContainsAlphanumeric extends Constraint
{
    public string $message = 'La chaîne "{{ string }}" contient des caractères interdits.';
    
    // Attribut utile pour forcer des arguments nommés dans le constructeur
    #[HasNamedArguments]
    public function __construct(
        public string $mode = 'strict', // Propriété publique accessible par le validateur
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }
}
```

**Pourquoi `HasNamedArguments` ?**
Sans cet attribut, Symfony pourrait tenter de passer les options comme un tableau unique `$options` au constructeur. `HasNamedArguments` indique au Validateur de mapper directement les arguments nommés de l'attribut PHP (ex: `#[ContainsAlphanumeric(mode: 'loose')]`) aux arguments du constructeur de la classe (`$mode`). Cela rend le code plus strict et lisible.

### Propriétés Privées et Cache
Le composant Validator met en cache les objets de contrainte pour optimiser les performances.
La classe parente `Constraint` utilise `get_object_vars()` pour savoir quelles propriétés sérialiser. **Problème :** Cette fonction ne voit pas les propriétés **privées** des classes enfants.

**Exemple du problème :**
Si vous avez `private string $mode` et que vous ne faites rien, après la mise en cache, la propriété `$mode` sera vide/perdue lors de la prochaine utilisation.

**Solution :** Implémenter `__sleep()` pour inclure explicitement les propriétés privées.

```php
    // Dans la classe de Contrainte
    private string $mode;

    public function __construct(string $mode = 'strict', ...) {
        $this->mode = $mode;
        // ...
    }

    public function __sleep(): array
    {
        // On fusionne les propriétés de la classe parent avec notre propriété privée 'mode'
        return array_merge(parent::__sleep(), ['mode']);
    }
```

## 2. Créer le Validateur (Service)
Le validateur contient la logique. Symfony l'instancie comme un service, donc l'**injection de dépendances** est possible (ex: `RequestStack`, `EntityManagerInterface`).

La méthode `validate` reçoit la valeur et la contrainte.

```php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ContainsAlphanumeric) {
            throw new UnexpectedTypeException($constraint, ContainsAlphanumeric::class);
        }

        // 1. Ignorer null et vide (laisser NotBlank s'en occuper)
        if (null === $value || '' === $value) {
            return;
        }

        // 2. Vérifier le type de la valeur attendue
        if (!is_string($value)) {
            // Lance une exception si le type n'est pas géré (ce n'est pas une violation de validation)
            throw new UnexpectedValueException($value, 'string');
        }
        
        // Accès aux options de la contrainte
        if ($constraint->mode === 'strict') {
             // ... logique spécifique
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
            // 3. Construire la violation
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
```

## 3. Contraintes Composées (Compound Constraints)
Introduit récemment, cela permet de créer une contrainte qui est en fait une collection d'autres contraintes existantes. Utile pour des ensembles de règles réutilisables (ex: politique de mot de passe).

La classe doit étendre `Symfony\Component\Validator\Constraints\Compound`. Il n'y a **pas de validateur** à créer !

```php
namespace App\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Compound;

#[\Attribute]
class PasswordRequirements extends Compound
{
    protected function getConstraints(array $options): array
    {
        return [
            new Assert\NotBlank(),
            new Assert\Length(min: 8),
            new Assert\NotCompromisedPassword(), // Vérifie haveibeenpwned
            new Assert\Regex('/[A-Z]+/'),
        ];
    }
}
```

## 4. Contraintes de Classe (Class Constraint)
Parfois, la validation dépend de plusieurs propriétés d'un objet. On applique alors la contrainte sur la **classe** entière.

1.  Dans la contrainte, surchargez `getTargets()` :
    ```php
    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
    ```
2.  Dans le validateur, `$value` sera l'**instance de l'objet**.
3.  Utilisez `atPath()` pour attacher l'erreur à un champ spécifique, sinon elle sera globale.

```php
// ... dans le validateur
public function validate(mixed $value, Constraint $constraint): void
{
    // $value est l'entité User ici
    if ($value->getEmail() !== $value->getConfirmationEmail()) {
        $this->context->buildViolation($constraint->message)
            ->atPath('email') // L'erreur apparaitra sur le champ 'email'
    ->addViolation();
    }
}
```

## 5. Le Violation Builder (`$this->context`)
L'objet `ExecutionContextInterface` permet de construire l'erreur.

*   **Paramètres** : `->setParameter('{{ value }}', $value)`
*   **Pluralisation** : `->setPlural((int) $limit)`
*   **Chemin** : `->atPath('propriete.sousPropriete')`
*   **Code** : `->setCode('MY_ERROR_CODE')` (pour API/Client)

## 6. Tester les Contraintes
Symfony fournit des classes de base pour tester vos contraintes sans démarrer tout le kernel.

### Atomic Constraints
Utilisez `Symfony\Component\Validator\Test\ConstraintValidatorTestCase`.

```php
class ContainsAlphanumericValidatorTest extends ConstraintValidatorTestCase
{
    protected function createValidator(): ConstraintValidatorInterface
    {
        return new ContainsAlphanumericValidator();
    }

    public function testInvalidValue(): void
    {
        $this->validator->validate('..@..', new ContainsAlphanumeric());

        $this->buildViolation('La chaîne "{{ string }}" contient des caractères interdits.')
            ->setParameter('{{ string }}', '..@..')
            ->assertRaised();
    }
}
```

### Compound Constraints (Depuis Symfony 7.2)
Utilisez `Symfony\Component\Validator\Test\CompoundConstraintTestCase`.

## 7. Traduction
*   Domaine par défaut : `validators`.
*   Fichier : `translations/validators.fr.yaml`.
*   Les clés sont les messages définis dans la classe de contrainte.

## ⚠️ Points de vigilance (Certification)
*   **Autowiring** : Le validateur est un service, vous pouvez injecter ce que vous voulez dans le `__construct`.
*   **Convention** : `MyRule` cherche automatiquement `MyRuleValidator`. Si vous ne respectez pas ça, surchargez `validatedBy()` dans la contrainte.
*   **Validation nulle** : Ne validez JAMAIS `null` ou chaine vide dans une contrainte personnalisée (sauf si c'est son but explicite). Laissez `NotNull` ou `NotBlank` faire ce travail. Retournez simplement `return` si la valeur est vide.

## Ressources
*   [Symfony Docs - Custom Constraints](https://symfony.com/doc/current/validation/custom_constraint.html)
