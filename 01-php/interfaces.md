# Interfaces

## Concept clé
Une interface est un contrat strict (API). Elle définit **quelles méthodes** une classe doit posséder, mais jamais **comment** elles fonctionnent.
Elle favorise :
1.  **Polymorphisme** : Traiter différents objets de la même manière s'ils signent le même contrat.
2.  **Découplage** : Le code dépend de l'interface (abstraction), pas de l'implémentation concrète (DIP - Dependency Inversion Principle de SOLID).
3.  **Interchangeabilité** : Facilite le remplacement d'une brique par une autre (ex: changer de driver mailer).

## Application dans Symfony 7.0
Symfony est un framework "Interface-driven".
*   **Autowiring** : On injecte `LoggerInterface $logger` au lieu de `Monolog\Logger`. Symfony sait trouver le service qui implémente cette interface.
*   **Service Subscribers** : Utilisent des interfaces pour le lazy-loading.
*   **Events** : Les `EventSubscriberInterface`.
*   **Markers** : Certaines interfaces sont vides (Marker Interfaces) et servent juste à étiqueter une classe pour qu'un `CompilerPass` la détecte (bien que les Attributs PHP 8 remplacent souvent cet usage).

## Exemple de code : ISP (Interface Segregation Principle)

Un principe clé de SOLID est de préférer plusieurs petites interfaces spécifiques plutôt qu'une interface géante ("God Interface").

```php
<?php

// ❌ Mauvais : Force l'implémentation de méthodes inutiles
interface WorkerInterface {
    public function work();
    public function sleep();
}

class Robot implements WorkerInterface {
    public function work() { /* ... */ }
    public function sleep() { throw new \Exception("Robots don't sleep!"); } // Violation LSP
}

// ✅ Bon : Ségrégation
interface Workable {
    public function work(): void;
}

interface Sleepable {
    public function sleep(): void;
}

class Human implements Workable, Sleepable {
    public function work(): void { /* ... */ }
    public function sleep(): void { /* ... */ }
}

class RobotV2 implements Workable {
    public function work(): void { /* ... */ }
}
```

## Héritage et Constantes

```php
interface Cacheable {
    // Les interfaces peuvent avoir des constantes
    public const TTL_DEFAULT = 3600;
}

interface Loggable {
    public function log(string $msg): void;
}

// Héritage multiple d'INTERFACES
interface ServiceInterface extends Cacheable, Loggable {
    public function execute(): void;
}
```

## 🧠 Concepts Clés
1.  **Multi-implémentation** : Une classe peut `implements` plusieurs interfaces (contournement de l'héritage unique).
2.  **Contrat public** : Toutes les méthodes d'une interface sont implicitement `public`. Impossible de déclarer `private` ou `protected`.
3.  **Pas d'état** : Une interface ne peut pas contenir de propriétés (variables). Seulement des constantes.
4.  **Typage** : `instanceof MyInterface` renvoie `true` si l'objet implémente l'interface (directement ou via héritage).

## ⚠️ Points de vigilance (Certification)
*   **Covariance / Contravariance** :
    *   Depuis PHP 7.4, l'implémentation peut avoir un type de retour **plus précis** (Covariance).
    *   L'implémentation peut accepter des arguments **moins précis** (Contravariance).
    *   *Exemple* : Interface `get(): ?User` -> Implémentation `get(): User`.
*   **Magic Methods** : Certaines méthodes magiques (`__toString`, `__invoke`) peuvent être déclarées dans une interface. Cependant, `__construct` et `__destruct` sont déconseillés (voire interdits selon les contextes) car ils lient au cycle de vie, pas au comportement.
*   **Abstract vs Interface** :
    *   Utilisez une **Interface** pour définir un comportement (Can-Do).
    *   Utilisez une **Classe Abstraite** pour partager du code et une identité (Is-A).

## Ressources
*   [Manuel PHP - Interfaces objet](https://www.php.net/manual/fr/language.oop5.interfaces.php)
*   [Principe de Ségrégation des Interfaces (Wikipedia)](https://fr.wikipedia.org/wiki/Principe_de_s%C3%A9gr%C3%A9gation_des_interfaces)
