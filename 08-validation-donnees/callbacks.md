# Validateurs Callback

## Concept clé
Parfois, une règle de validation est trop spécifique pour être réutilisable (ex: "Si le mode de livraison est 'Express', l'adresse ne doit pas être une boîte postale").
Au lieu de créer une classe de Contrainte personnalisée, on peut utiliser un **Callback** directement dans l'entité.

## Application dans Symfony 7.0
Utilisation de l'attribut `#[Assert\Callback]`.

```php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Delivery
{
    #[Assert\Choice(['standard', 'express'])]
    public string $mode = 'standard';

    public string $address = '';

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        // Logique métier spécifique
        if ($this->mode === 'express' && str_contains(strtolower($this->address), 'boite postale')) {
            $context->buildViolation('La livraison Express est impossible pour les boîtes postales.')
                ->atPath('address') // Attache l'erreur au champ 'address'
                ->addViolation();
        }
    }
}
```

## Static Callback
On peut aussi utiliser une méthode statique (utile pour les DTOs ou pour ne pas polluer l'entité).

```php
#[Assert\Callback([ValidationHelper::class, 'validateDelivery'])]
class Delivery { ... }
```

## 🧠 Concepts Clés
1.  **ExecutionContext** : L'objet clé injecté (`ExecutionContextInterface`) qui permet d'ajouter des violations (`addViolation`).
2.  **Moment** : Le callback est exécuté après les validations de champs simples (NotBlank, Length), sauf si des groupes ou séquences changent l'ordre.

## ⚠️ Points de vigilance (Certification)
*   **Pas de Service** : Un callback dans une entité ne peut pas utiliser de services (pas d'injection de dépendance). Si vous avez besoin de la base de données ou d'un service externe, créez une **Contrainte Personnalisée** ou utilisez l'événement `FormEvents::POST_SUBMIT` (si c'est lié à un formulaire).
*   **Visibilité** : La méthode doit être `public` (ou `private`/`protected` si utilisée via l'attribut sur la méthode elle-même à l'intérieur de la classe, mais `public` est recommandé).

## Ressources
*   [Symfony Docs - Callback Constraint](https://symfony.com/doc/current/reference/constraints/Callback.html)
