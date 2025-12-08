# Namespaces (Espaces de noms)

## Concept clé
Les namespaces sont des conteneurs virtuels qui encapsulent des éléments PHP (Classes, Interfaces, Traits, Fonctions, Constantes).
Ils résolvent deux problèmes majeurs :
1.  **Collisions de noms** : Permet d'avoir une classe `User` dans `App\Entity` et une autre dans `Vendor\Lib`.
2.  **Organisation (PSR-4)** : Fournit une structure logique mappée sur le système de fichiers pour l'Autoloading.

## Application dans Symfony 7.0
Symfony respecte strictement la norme **PSR-4**.
*   Namespace racine `App\` pointe vers le dossier `src/`.
*   Exemple : La classe `App\Controller\HomeController` **DOIT** se trouver dans `src/Controller/HomeController.php`.
*   Les bundles tiers vivent dans le dossier `vendor/` avec leurs propres namespaces (ex: `Symfony\Component\HttpKernel\`).

## Syntaxe et Importation

```php
<?php

// 1. Déclaration (TOUJOURS la première ligne significative)
namespace App\Service;

// 2. Importation (Aliasing)
// Import de classe
use Symfony\Component\Mailer\MailerInterface;
// Import avec Alias (pour éviter conflit ou raccourcir)
use App\Entity\User as AppUser; 
// Import de Fonction (PHP 5.6+)
use function str_replace;
// Import de Constante (PHP 5.6+)
use const PHP_VERSION;

class UserManager
{
    public function __construct(
        // Utilisation du nom court (importé)
        private MailerInterface $mailer
    ) {}

    public function create(): AppUser
    {
        // Utilisation du nom pleinement qualifié (Fully Qualified Class Name - FQCN)
        // Utile si on n'a pas fait de 'use'
        return new \App\Entity\User();
    }
}
```

## Group Use (PHP 7.0+)
Permet d'importer plusieurs classes du même namespace en une ligne.

```php
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Doctrine\ORM\Mapping as ORM; // Import de tout le namespace pour les attributs
```

## Résolution de Noms (Règles de priorité)

1.  **Nom non qualifié** (`$u = new User()`):
    *   Cherche `CurrentNamespace\User`.
    *   Si n'existe pas (et qu'on est dans un namespace), cela échoue pour une classe.
    *   **Exception pour Fonctions/Constantes** : PHP "fallback" (retombe) sur le namespace global si la fonction n'existe pas localement.
        *   `strlen()` dans `App\` cherche `App\strlen`, puis `\strlen`.
2.  **Nom qualifié** (`new Utils\Date()`):
    *   Cherche `CurrentNamespace\Utils\Date`.
3.  **Nom pleinement qualifié** (`new \DateTime()`):
    *   Cherche `\DateTime` (racine). Indispensable pour utiliser les classes natives PHP à l'intérieur d'un namespace, sauf si importées.

## 🧠 Concepts Clés
1.  **Le mot clé `namespace`** : Utilisé sans nom (`namespace\MyClass`), il fait référence au namespace courant (équivalent de `self` mais pour le package).
2.  **Pas d'impact Runtime** : Les namespaces sont résolus à la compilation. Ils n'ont aucun impact sur la performance à l'exécution (contrairement aux appels de fonctions).
3.  **Bonnes pratiques** :
    *   Un fichier = Une classe = Un namespace cohérent avec le dossier.
    *   Toujours importer les classes (`use`) plutôt que d'utiliser les FQCN inline, pour la lisibilité.
    *   Organisez vos namespaces par **Domaine** (Fonctionnalité) plutôt que par couche technique si possible (DDD).

## ⚠️ Points de vigilance (Certification)
*   **Déclaration multiple** : Il est techniquement possible de définir plusieurs namespaces dans un seul fichier (avec accolades `{}`), mais c'est **interdit** par la PSR-4 et les standards Symfony.
*   **Classes natives** : Erreur classique : utiliser `Exception` dans un namespace sans mettre `\` devant ou sans l'importer. PHP cherchera `App\Service\Exception` et plantera.
*   **Sensibilité à la casse** : Les namespaces sont insensibles à la casse (comme les classes), mais les systèmes de fichiers (Linux) le sont. Respectez toujours la casse exacte (PascalCase).

## Ressources
*   [Manuel PHP - Les espaces de noms](https://www.php.net/manual/fr/language.namespaces.php)
*   [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/)
