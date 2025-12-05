# Contraintes de Validation Natives

## Concept clé
Symfony fournit une vaste bibliothèque de contraintes prêtes à l'emploi.

## Application dans Symfony 7.0

### Basic
*   `NotBlank` (non vide et pas null)
*   `NotNull`
*   `IsNull`, `IsTrue`, `IsFalse`
*   `Type` (integer, string...)

### String
*   `Email`
*   `Length` (min, max)
*   `Url`
*   `Regex`
*   `UserPassword` (vérifie le mot de passe actuel, utile pour les changements de mot de passe)

### Number
*   `Positive`, `PositiveOrZero`, `Negative`
*   `GreaterThan`, `LessThan`, `Range`

### Date
*   `Date`, `DateTime`
*   `Time`
*   `GreaterThan` (fonctionne aussi pour les dates)

### Collection
*   `Choice` (valeur dans une liste)
*   `Count` (taille du tableau)
*   `Unique` (éléments uniques)

### File
*   `File` (taille, mime-type)
*   `Image` (dimensions)

## Points de vigilance (Certification)
*   **NotBlank vs NotNull** : `NotBlank` vérifie que la valeur n'est pas `null` ET n'est pas une chaîne vide `""`. `NotNull` accepte `""`.
*   **Null** : La plupart des contraintes (sauf `NotNull` et `NotBlank`) ignorent les valeurs `null`. Si une propriété est optionnelle, ne mettez pas `NotNull`.

## Ressources
*   [Symfony Docs - Constraints Reference](https://symfony.com/doc/current/reference/constraints.html)

