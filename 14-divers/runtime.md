# Composant Runtime

## Concept clé
Avant Symfony 5.3, le fichier `public/index.php` contenait la logique de démarrage du Kernel (`$kernel = new Kernel... $kernel->handle...`).
Le composant **Runtime** abstrait cette logique. Cela permet à l'application d'être agnostique vis-à-vis du serveur qui la fait tourner (PHP-FPM, CLI, Swoole, Lambda).

## Le nouveau `public/index.php`
Il est généré par la recette Flex et ne doit presque jamais être modifié.

```php
use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

## Fonctionnement
1.  `autoload_runtime.php` cherche une classe implémentant `Symfony\Component\Runtime\RuntimeInterface`.
2.  Par défaut, il utilise `Symfony\Component\Runtime\SymfonyRuntime`.
3.  Le Runtime instancie le Kernel (via la Closure retournée).
4.  Le Runtime appelle le "Runner" approprié (ex: `HttpKernelRunner` pour le web, `ConsoleApplicationRunner` pour la CLI).

## Options du Runtime
On peut configurer le Runtime via des variables d'environnement.
*   `APP_RUNTIME_ENV`
*   `APP_RUNTIME_DEBUG`

## 🧠 Concepts Clés
1.  **Découplage** : Votre code (`Kernel`) ne sait pas comment il est exécuté.
2.  **Long-Running** : Avec des Runtimes alternatifs (comme FrankenPHP ou RoadRunner), l'application reste en mémoire entre les requêtes. Le composant Runtime facilite cette transition (bien que l'application elle-même doive être écrite en conséquence, sans fuite de mémoire).

## ⚠️ Points de vigilance (Certification)
*   **Point d'entrée** : C'est toujours `index.php` pour le web ET `bin/console` pour la ligne de commande (qui utilise aussi le Runtime).

## Ressources
*   [Symfony Docs - Runtime Component](https://symfony.com/doc/current/components/runtime.html)
