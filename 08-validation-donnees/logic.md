# Validation Logique et Conditionnelle
     
## Concept clé
Parfois, la validation d'un champ dépend de la valeur d'un autre champ ou d'une logique complexe.
Symfony fournit des contraintes puissantes pour gérer ces scénarios sans écrire de validateur personnalisé.

## 1. Contrainte `Expression` (ExpressionLanguage)
Permet d'utiliser le langage d'expression Symfony pour valider l'objet ou une propriété.
C'est souvent une alternative plus rapide aux `Callback`.

```php
use Symfony\Component\Validator\Constraints as Assert;

class Order
{
    public bool $isFreeShipping = false;
    public ?float $shippingCost = null;

    // Valide si la condition est vraie
    #[Assert\Expression(
        "this.isFreeShipping or this.shippingCost > 0",
        message: "Les frais de port doivent être positifs si la livraison n'est pas gratuite."
    )]
    public function isValid(): bool
    {
        return true; // Dummy method si placé sur la classe, ou direct sur la propriété
    }
}
```
*   **Variables** : `this` (l'objet courant), `value` (la valeur de la propriété).

## 2. Contrainte `When` (Validation Conditionnelle)
Introduite récemment, elle permet d'activer des contraintes **seulement si** une condition est remplie.
C'est une alternative élégante aux Groupes de validation dynamiques.

```php
class User
{
    public bool $hasAddress = false;

    #[Assert\When(
        expression: "this.hasAddress == true",
        constraints: [
            new Assert\NotNull(message: "L'adresse est requise."),
            new Assert\Length(min: 10)
        ]
    )]
    public ?string $address = null;
}
```
*   **Fonctionnement** : Si l'expression est vraie, les `constraints` imbriquées sont validées. Sinon, elles sont ignorées.

## 3. Contrainte `Sequentially` (Arrêt Rapide)
Permet d'appliquer une liste de contraintes l'une après l'autre et de **s'arrêter à la première erreur**.
C'est l'équivalent de `GroupSequence` mais au niveau d'une seule propriété.

```php
class User
{
    #[Assert\Sequentially([
        new Assert\NotBlank(),
        new Assert\Email(),
        new Assert\Length(min: 10), // Ne sera pas exécuté si ce n'est pas un email valide
        new Assert\Regex('/@company\.com$/') // Ne sera pas exécuté si longueur incorrecte
    ])]
    public string $email;
}
```
*   **Avantage** : Améliore l'UX (une seule erreur affichée) et la performance (évite les regex lourdes sur des données vides).

## 4. Contrainte `AtLeastOneOf`
Valide si **au moins une** des contraintes internes passe.

```php
#[Assert\AtLeastOneOf([
    new Assert\Email(),
    new Assert\Regex('/^@[a-z]+\.com$/') // Exemple fictif : soit un email, soit un handle twitter
], message: "Vous devez fournir un email ou un handle valide.")]
public string $contact;
```

## 🧠 Concepts Clés
1.  **Performance** : `Expression` et `When` utilisent le composant `ExpressionLanguage`. C'est interprété au runtime, donc un peu plus lent que du code PHP natif (Callback), mais plus flexible (stockable en YAML/XML).
2.  **Lisibilité** : Préférez `When` aux `Groupes` pour des conditions simples basées sur l'état de l'objet.

## ⚠️ Points de vigilance (Certification)
*   **Syntaxe** : Dans une `Expression`, pour accéder à une propriété privée, il faut utiliser la notation getter implicite (`this.shippingCost` appelle `$this->getShippingCost()`).
*   **Sequentially** : Important à connaître pour éviter d'afficher 3 erreurs pour le même champ ("Vide", "Pas un email", "Trop court").

## Ressources
*   [Symfony Docs - Expression](https://symfony.com/doc/current/reference/constraints/Expression.html)
*   [Symfony Docs - When](https://symfony.com/doc/current/reference/constraints/When.html)
*   [Symfony Docs - Sequentially](https://symfony.com/doc/current/reference/constraints/Sequentially.html)
