# Séquence de Groupe (Group Sequence)

## Concept clé
Définir un ordre de validation.
Par défaut, toutes les contraintes sont validées en parallèle. Avec une séquence, on peut dire : "Valide le groupe A d'abord. S'il y a des erreurs, arrête-toi. Sinon, valide le groupe B".
Utile pour éviter des validations lourdes (appel API/DB) si les validations simples (format) échouent déjà.

## Application dans Symfony 7.0
Attribut `#[Assert\GroupSequence]` sur la classe.

```php
#[Assert\GroupSequence(['User', 'Strict'])]
class User
{
    #[Assert\NotBlank]
    private string $username;

    #[Assert\IsTrue(groups: ['Strict'])]
    public function isPasswordSafe(): bool
    {
        // Validation lourde...
    }
}
```

Ici, `User` représente le groupe `Default`.
Le validateur va d'abord vérifier `NotBlank`. S'il échoue, il s'arrête. Si OK, il lance `Strict`.

## Points de vigilance (Certification)
*   **Provider** : `GroupSequenceProvider` permet de définir la séquence dynamiquement (ex: selon l'état "Premium" de l'utilisateur).

## Ressources
*   [Symfony Docs - Group Sequence](https://symfony.com/doc/current/validation/sequence_provider.html)

