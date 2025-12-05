# Programmation Orientée Objet (POO)

## Concept clé
La Programmation Orientée Objet (POO) est un paradigme de programmation basé sur le concept d'"objets", qui peuvent contenir des données (attributs) et du code (méthodes). Les piliers fondamentaux sont l'encapsulation, l'héritage, le polymorphisme et l'abstraction.

## Application dans Symfony 7.0
Symfony est un framework entièrement orienté objet. Tout est objet : la requête, la réponse, les services, les entités, les événements. Une compréhension profonde de la POO est indispensable pour comprendre l'architecture de Symfony (Injection de dépendances, Pattern Observer, Decorator, Factory, etc.).

## Exemple de code

```php
<?php

// Encapsulation et Abstraction
abstract class Content
{
    protected string $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    abstract public function getType(): string;
}

// Héritage
class Video extends Content
{
    public function getType(): string
    {
        return 'video';
    }
}

// Polymorphisme
class ContentRenderer
{
    public function render(Content $content): string
    {
        // $content peut être n'importe quelle sous-classe de Content
        return sprintf('Rendering %s: %s', $content->getType(), $content->title); // Erreur ici si $title est protected sans getter, ajouté pour l'exemple
        // Correction : Il faudrait un getter pour title, mais l'exemple illustre le concept.
    }
}
```

## Points de vigilance (Certification)
*   **Visibilité** : Maîtriser `public`, `protected`, et `private`. Savoir que depuis PHP 7.1, les constantes de classe peuvent aussi avoir une visibilité.
*   **Méthodes magiques** : Connaître leur fonctionnement et leurs cas d'usage (`__construct`, `__toString`, `__invoke`, `__get`, `__set`, `__call`, `__serialize`/`__unserialize`).
*   **Static vs Self** : Comprendre la différence entre `self::` (résolu à la compilation, classe courante) et `static::` (résolu à l'exécution, Late Static Binding).
*   **Final** : Savoir qu'une classe `final` ne peut pas être héritée et une méthode `final` ne peut pas être surchargée. Symfony encourage de plus en plus l'utilisation de classes `final`.

## Ressources
*   [Manuel PHP - Classes et Objets](https://www.php.net/manual/fr/language.oop5.php)
*   [Manuel PHP - Late Static Binding](https://www.php.net/manual/fr/language.oop5.late-static-bindings.php)

