# Composants (Components)

## Concept clé
Symfony est un ensemble de composants PHP découplés et réutilisables. Le Framework Symfony (le produit Full Stack) est construit en assemblant ces composants.
Mais ces composants peuvent être utilisés individuellement dans n'importe quel projet PHP (Laravel, Drupal, projet legacy, etc.).

## Application dans Symfony 7.0
Il existe plus de 30 composants.
Exemples de composants autonomes :
*   `HttpFoundation` : Gestion Request/Response.
*   `Routing` : Système de routage.
*   `Console` : Création de CLI.
*   `Dotenv` : Gestion des variables d'environnement.
*   `Yaml` : Parsing YAML.

## Exemple de code (Utilisation hors framework)

```php
<?php
// Un script PHP simple utilisant juste le composant Finder
require 'vendor/autoload.php';

use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->files()->in(__DIR__);

foreach ($finder as $file) {
    echo $file->getRealPath() . "\n";
}
```

## Points de vigilance (Certification)
*   **Framework vs Composants** : Le framework est le "tout", les composants sont les "briques".
*   **Dépendances** : Les composants essaient d'avoir le minimum de dépendances entre eux.
*   **Bundle** : Un Bundle n'est pas un composant. Un Bundle est un plugin pour le framework Symfony qui intègre souvent un ou plusieurs composants.

## Ressources
*   [Symfony Components](https://symfony.com/components)

