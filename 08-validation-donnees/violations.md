# Violations et Contraintes Personnalisées

## Concept clé
Quand les contraintes natives ne suffisent pas, vous devez créer vos propres règles.
Cela implique trois concepts :
1.  **La Contrainte** (`Constraint`) : La définition (Attribute/Options).
2.  **Le Validateur** (`ConstraintValidator`) : La logique.
3.  **La Violation** (`ConstraintViolation`) : L'erreur générée via le `ViolationBuilder`.

## 1. Créer la Contrainte (Classe)
C'est un DTO qui porte la configuration.

```php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ContainsAlphanumeric extends Constraint
{
    public string $message = 'La chaîne "{{ string }}" contient des caractères interdits.';
    public string $mode = 'strict'; // Option personnalisée

    // Optionnel : pour utiliser l'attribut sur la classe entière
    public function getTargets(): string|array
    {
        return self::PROPERTY_CONSTRAINT;
    }
}
```

## 2. Créer le Validateur (Service)
C'est ici que la logique réside. Symfony l'instancie comme un service (Autowiring possible).

```php
namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class ContainsAlphanumericValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ContainsAlphanumeric) {
            throw new UnexpectedTypeException($constraint, ContainsAlphanumeric::class);
        }

        // Règle d'or : Null et Vide sont valides (c'est le rôle de NotBlank)
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $value, $matches)) {
            // C'est ici qu'on utilise le Violation Builder
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
```

## 3. Le Violation Builder (`$this->context`)
L'objet `ExecutionContextInterface` permet de construire l'erreur avec précision.

### Paramètres de message
```php
$this->context->buildViolation('La valeur {{ value }} est invalide.')
    ->setParameter('{{ value }}', $value)
    ->addViolation();
```

### Cibler un chemin (Property Path)
Utile pour les validateurs de classe (Class Constraint) qui veulent attacher l'erreur à un champ spécifique.
```php
$this->context->buildViolation('Erreur globale.')
    ->atPath('address.zipCode') // L'erreur s'affichera sous le champ zipCode
    ->addViolation();
```

### Pluralisation
```php
$this->context->buildViolation('Il faut au moins {{ limit }} choix.')
    ->setParameter('{{ limit }}', $limit)
    ->setPlural((int) $limit)
    ->addViolation();
```

## Traduction des Messages
Symfony traduit automatiquement les messages de violation.
*   Par défaut, le domaine de traduction est `validators`.
*   Les paramètres `{{ value }}` sont remplacés après la traduction.
*   Vous devez créer un fichier `translations/validators.fr.yaml` pour vos messages personnalisés.

```yaml
# translations/validators.fr.yaml
"La valeur {{ value }} est invalide.": "La valeur {{ value }} n'est pas bonne."
```

## 🧠 Concepts Clés
1.  **Naming** : Si la contrainte est `App\Validator\MyRule`, Symfony cherche `App\Validator\MyRuleValidator`.
2.  **Service** : Le validateur peut injecter d'autres services (Repository, RequestStack) via son constructeur.
3.  **Payload** : On peut attacher un payload arbitraire à une violation pour le traiter côté client/API (`->setCode('MY_ERROR_CODE')`).

## ⚠️ Points de vigilance (Certification)
*   **AddViolation** : N'oubliez jamais `->addViolation()` à la fin de la chaîne du builder. Sinon, aucune erreur n'est levée.
*   **Validator vs Constraint** : La Contrainte est l'annotation, le Validateur est le code.

## Ressources
*   [Symfony Docs - Custom Constraints](https://symfony.com/doc/current/validation/custom_constraint.html)
*   [Symfony Docs - Translations](https://symfony.com/doc/current/validation/translations.html)
