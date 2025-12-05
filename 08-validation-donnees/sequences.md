# Séquence de Groupe (Group Sequence)

## Concept clé
Par défaut, toutes les contraintes d'un groupe sont validées.
La **Group Sequence** permet de définir un **ordre** et un **arrêt conditionnel**.
"Valide d'abord les champs basiques. Si OK, valide les règles complexes (DB, API)."

## Application dans Symfony 7.0
On utilise l'attribut `#[Assert\GroupSequence]` au niveau de la classe.

```php
namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;

#[Assert\GroupSequence(['User', 'Strict'])]
class User
{
    #[Assert\NotBlank]
    public string $username;

    #[Assert\IsTrue(groups: ['Strict'])]
    public function isExternalApiValid(): bool
    {
        // Appel lourd à une API
    }
}
```

**Comportement :**
1.  Symfony remplace le groupe `Default` par la séquence définie `['User', 'Strict']`.
2.  Étape 1 : Il valide le groupe `User` (qui est l'alias de la classe, donc les contraintes sans groupe comme `NotBlank`).
3.  **Stop ou Encore** :
    *   Si `NotBlank` échoue -> On s'arrête. `Strict` n'est jamais exécuté. (Gain de perf + UX plus claire).
    *   Si `NotBlank` passe -> On passe à l'étape 2 : valider le groupe `Strict`.

## GroupSequenceProvider (Dynamique)
Si la séquence dépend de l'état de l'objet (ex: un User Premium a des validations en plus), implémentez `GroupSequenceProviderInterface`.

```php
use Symfony\Component\Validator\GroupSequenceProviderInterface;

#[Assert\GroupSequenceProvider]
class User implements GroupSequenceProviderInterface
{
    public function getGroupSequence(): array|GroupSequence
    {
        $groups = ['User'];
        
        if ($this->isPremium()) {
            $groups[] = 'Premium';
        }
        
        return $groups;
    }
}
```

## 🧠 Concepts Clés
1.  **Optimisation** : Évite de lancer des requêtes DB lourdes (Unicité, Validateur custom) si le format de base (Email, Length) est déjà invalide.
2.  **Substitution** : La séquence remplace le groupe `Default`.

## ⚠️ Points de vigilance (Certification)
*   **Nom du groupe classe** : Dans la séquence, il faut inclure le nom court de la classe (`User`) ou `Default`. Si vous l'oubliez, les contraintes de base ne seront jamais validées.

## Ressources
*   [Symfony Docs - Group Sequence](https://symfony.com/doc/current/validation/sequence_provider.html)
