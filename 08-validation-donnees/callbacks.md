# Validateurs Callback

## Concept clé
Pour des règles simples qui ne méritent pas une classe de contrainte dédiée.

## Application dans Symfony 7.0
Attribut `#[Assert\Callback]`.

```php
class Transaction
{
    private $cardNumber;
    private $paymentMethod;

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        if ($this->paymentMethod === 'card' && empty($this->cardNumber)) {
            $context->buildViolation('Le numéro de carte est requis pour le paiement par carte.')
                ->atPath('cardNumber')
                ->addViolation();
        }
    }
}
```

## Points de vigilance (Certification)
*   **Public** : La méthode callback doit être publique (ou protected/private si invoquée à l'intérieur de la classe, mais publique est plus sûr pour l'appel externe).
*   **Context** : C'est l'objet `ExecutionContextInterface` qui permet d'ajouter des violations.

## Ressources
*   [Symfony Docs - Callback Constraint](https://symfony.com/doc/current/reference/constraints/Callback.html)

