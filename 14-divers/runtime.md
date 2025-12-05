# Composant Runtime

## Concept clé
Introduit dans Symfony 5.3, le composant Runtime découple l'application (Kernel) du script d'entrée (Front Controller `index.php`).
Il permet de supporter nativement PHP-FPM, mais aussi des serveurs asynchrones (Swoole, RoadRunner, ReactPHP) ou des fonctions Serverless (Lambda) sans changer le code de l'app.

## Application dans Symfony 7.0
Le fichier `public/index.php` est minimaliste :

```php
use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

C'est `autoload_runtime.php` qui détecte le Runtime à utiliser (par défaut `Symfony\Component\Runtime\SymfonyRuntime`).

## Points de vigilance (Certification)
*   **Autoload** : On n'inclut plus `autoload.php` mais `autoload_runtime.php`.
*   **Return** : Le script retourne une Closure qui crée le Kernel, il ne l'exécute pas lui-même (`$kernel->handle()`). C'est le Runtime qui appelle le handler.

## Ressources
*   [Symfony Docs - Runtime](https://symfony.com/doc/current/components/runtime.html)

