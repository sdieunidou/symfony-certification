# Traits

## Concept clé
Les Traits sont un mécanisme de réutilisation de code permettant de contourner la limitation de l'héritage simple. Un trait permet d'insérer des méthodes et des propriétés dans une classe ("Copier-Coller" horizontal).

## Application dans Symfony 7.0
Symfony utilise quelques traits, souvent pour des fonctionnalités optionnelles ou transversales.
Exemples :
*   `LoggerAwareTrait` : Implémente `setLogger()` pour l'injection de dépendance optionnelle.
*   `RouteCollectionTrait` : Dans le composant Routing.

Dans vos applications Symfony, ils sont utiles pour partager du code entre Entités (ex: `TimestampableTrait`) ou Contrôleurs.

## Exemple de code

```php
<?php

trait LoggableTrait
{
    private string $logPrefix = '[APP] ';

    public function log(string $msg): void
    {
        echo $this->logPrefix . $msg;
    }
}

trait SerializableTrait
{
    public function toJson(): string
    {
        return json_encode($this); // $this fera référence à l'objet utilisant le trait
    }
}

class Order
{
    use LoggableTrait, SerializableTrait;
    
    public string $id = '123';
}

$order = new Order();
$order->log('Order created'); // [APP] Order created
```

## Points de vigilance (Certification)
*   **Précédence** :
    1.  Méthode de la classe courante (écrase tout).
    2.  Méthode du Trait (écrase la classe parente).
    3.  Méthode de la classe parente (héritée).
*   **Conflits** : Si deux traits utilisés définissent la même méthode, il faut résoudre le conflit avec `insteadof` (pour choisir l'une) ou `as` (pour aliaser l'autre).
*   **Visibilité** : On peut changer la visibilité d'une méthode de trait lors de l'import (`use MyTrait { myMethod as protected; }`).
*   **Constantes** : Depuis PHP 8.2, les traits peuvent définir des constantes.
*   **Propriétés** : Si un trait définit une propriété, la classe ne peut pas définir une propriété de même nom (sauf si elle est strictement identique : même visibilité, type, valeur par défaut), sinon c'est une erreur fatale.

## Ressources
*   [Manuel PHP - Traits](https://www.php.net/manual/fr/language.oop5.traits.php)

