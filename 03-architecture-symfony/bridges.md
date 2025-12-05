# Bridges (Ponts)

## Concept clé
Les "Bridges" sont des paquets qui font le lien entre un composant Symfony et une bibliothèque tierce (ex: Doctrine, Monolog, Twig, PHPUnit).
Ils découplent le composant pur de son intégration spécifique.

## Application dans Symfony 7.0
Symfony nomme ces paquets `symfony/xy-bridge`.
Exemples :
*   `symfony/monolog-bridge` : Connecte le composant `HttpKernel` (logs système) avec la librairie `Monolog`.
*   `symfony/twig-bridge` : Ajoute des extensions Twig spécifiques à Symfony (ex: `path()`, `is_granted()`) qui ne sont pas dans Twig natif.
*   `symfony/doctrine-bridge` : Intègre Doctrine ORM (Validators, Registry, Event Listeners).
*   `symfony/phpunit-bridge` : Améliore PHPUnit (gestion des dépréciations, polyfills).

## Points de vigilance (Certification)
*   **Pourquoi ?** : Pour que le composant principal reste agnostique. Par exemple, le composant `Validator` ne dépend pas de Doctrine. C'est le `DoctrineBridge` qui ajoute la capacité de valider des contraintes d'unicité en base (`UniqueEntity`).
*   **Installation** : Souvent installés automatiquement par Flex via les méta-paquets (ex: `require doctrine/orm` installe `doctrine-bundle` qui dépend de `doctrine-bridge`).

## Ressources
*   [Symfony Packagist (Rechercher Bridge)](https://packagist.org/?query=symfony%20bridge)

