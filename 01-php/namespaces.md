# Namespaces (Espaces de noms)

## Concept clé
Les namespaces permettent d'organiser le code PHP en regroupant les classes, interfaces, fonctions et constantes liées sous un même nom logique. Ils résolvent deux problèmes :
1.  Collision de noms : Avoir deux classes nommées `User` dans deux bibliothèques différentes.
2.  Lisibilité et organisation : Créer des alias (Short names) pour des noms longs.

## Application dans Symfony 7.0
Symfony utilise intensément les namespaces (PSR-4). Chaque classe Symfony appartient à un namespace reflétant son emplacement dans l'arborescence des fichiers.
Exemple : `App\Controller\HomeController` se trouve dans `src/Controller/HomeController.php`.

## Exemple de code

```php
<?php

namespace App\Service;

// Importation d'une classe d'un autre namespace (Aliasing)
use Symfony\Component\Mailer\MailerInterface as SymfonyMailer;
use App\Entity\User;

class NewsletterManager
{
    public function __construct(
        private SymfonyMailer $mailer // Utilisation de l'alias
    ) {}

    public function send(User $user): void
    {
        // ...
    }
}
```

### Syntaxe de groupe (PHP 7+)
```php
use Symfony\Component\HttpFoundation\{Request, Response};
```

## Points de vigilance (Certification)
*   **Résolution** : Comprendre comment PHP résout un nom de classe (nom pleinement qualifié vs nom relatif vs nom importé).
*   **Namespace global** : Savoir qu'une classe sans namespace est dans le namespace global (`\`). Pour utiliser une classe native PHP (ex: `DateTime`) dans un fichier namespacé, il faut soit l'importer (`use DateTime;`), soit utiliser le backslash initial (`new \DateTime()`).
*   **Conflits** : Savoir gérer les conflits avec `as`.
*   **Déclaration** : La déclaration `namespace` doit être la toute première instruction du fichier (sauf `declare`).

## Ressources
*   [Manuel PHP - Les espaces de noms](https://www.php.net/manual/fr/language.namespaces.php)
*   [PSR-4: Autoloader](https://www.php-fig.org/psr/psr-4/)

