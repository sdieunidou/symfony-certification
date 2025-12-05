# Interfaces

## Concept clé
Une interface est un contrat qui définit quelles méthodes une classe doit implémenter, sans définir comment elles sont implémentées. Elle permet d'assurer qu'un objet dispose de certains comportements, facilitant le polymorphisme et le découplage.

## Application dans Symfony 7.0
Les interfaces sont au cœur de l'injection de dépendances dans Symfony. On type-hint souvent sur une interface (ex: `LoggerInterface`, `EventSubscriberInterface`) plutôt que sur une implémentation concrète. Cela permet de changer l'implémentation sous-jacente sans casser le code dépendant.

## Exemple de code

```php
<?php

interface NotifierInterface
{
    public function send(string $message): bool;
}

class SmsNotifier implements NotifierInterface
{
    public function send(string $message): bool
    {
        // Logique SMS...
        return true;
    }
}

class EmailNotifier implements NotifierInterface
{
    public function send(string $message): bool
    {
        // Logique Email...
        return true;
    }
}

// Le code dépend de l'interface, pas de la classe concrète
function alertUser(NotifierInterface $notifier, string $msg) {
    $notifier->send($msg);
}
```

## Points de vigilance (Certification)
*   **Héritage multiple** : Une classe ne peut hériter que d'une seule classe, mais peut implémenter **plusieurs** interfaces. Une interface peut hériter de plusieurs interfaces (`interface A extends B, C`).
*   **Constantes** : Les interfaces peuvent contenir des constantes. Les classes qui implémentent l'interface héritent de ces constantes.
*   **Méthodes** : Toutes les méthodes déclarées dans une interface doivent être `public`.
*   **Propriétés** : Les interfaces ne peuvent pas contenir de propriétés (attributs).
*   **Covariance/Contravariance** : Comprendre les règles de typage lors de l'implémentation des méthodes (depuis PHP 7.4+).

## Ressources
*   [Manuel PHP - Interfaces objet](https://www.php.net/manual/fr/language.oop5.interfaces.php)

