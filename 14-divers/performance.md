# Performance

## Concept clé
Symfony est rapide par défaut, mais une configuration serveur et applicative optimisée est cruciale pour la production.
L'objectif est de réduire l'overhead du framework, optimiser le chargement des classes (OPcache) et minimiser les I/O.

## Checklist de Production

### 1. Application
*   **APCu Polyfill** : Si le serveur utilise l'extension APC legacy, installer `symfony/polyfill-apcu`.
*   **Locales** : Restreindre `framework.enabled_locales` aux seules langues utilisées (évite de charger inutilement les fichiers de traduction).
*   **Service Container** : Compiler le conteneur en un seul fichier pour maximiser le preloading PHP 7.4+.
    ```yaml
    # config/services.yaml
    parameters:
        .container.dumper.inline_factories: true
    ```
*   **Composer** : Optimiser l'autoloader.
    ```bash
    composer dump-autoload --no-dev --classmap-authoritative
    ```
    *   `--classmap-authoritative` : Empêche Composer de scanner le disque si une classe n'est pas dans la map (Gain I/O majeur).

### 2. Serveur (PHP.ini)
*   **OPcache** : Le cache de bytecode est OBLIGATOIRE.
    ```ini
    opcache.memory_consumption=256
    opcache.max_accelerated_files=20000
    opcache.validate_timestamps=0 ; CRUCIAL EN PROD
    ```
    *   `validate_timestamps=0` : PHP ne vérifie plus si les fichiers ont changé. Gain de perf énorme. Implique de redémarrer PHP-FPM à chaque déploiement.
*   **Realpath Cache** : Cache les chemins de fichiers résolus.
    ```ini
    realpath_cache_size=4096K
    realpath_cache_ttl=600
    ```
*   **Preloading** (PHP 7.4+) : Charge les classes en mémoire au démarrage du serveur (avant même la requête).
    *   Symfony génère un script de preload dans `var/cache/prod/App_KernelProdContainer.preload.php`.
    *   Configurer dans `php.ini` : `opcache.preload=/path/to/project/config/preload.php`.

### 3. Debug
*   **Désactiver le dump XML du conteneur** : En debug, Symfony dumper un gros fichier XML du conteneur. Sur les gros projets, cela ralentit le cache warmup.
    ```yaml
    # config/services.yaml
    parameters:
        debug.container.dump: false
    ```

## Profiling (Mesure)

### 1. Symfony Stopwatch
Composant natif pour mesurer le temps d'exécution de vos propres services.

```php
use Symfony\Component\Stopwatch\Stopwatch;

public function __construct(private Stopwatch $stopwatch) {}

public function export(): void
{
    // Démarre un événement "export-data" dans la catégorie "export"
    $this->stopwatch->start('export-data', 'export');

    // ... traitement ...
    $this->stopwatch->lap('export-data'); // Mesure des tours intermédiaires

    $event = $this->stopwatch->stop('export-data');
    // $event->getDuration(), $event->getMemory()
}
```
Apparaît dans la Timeline du Web Profiler.

**Sections :**
Permet de grouper des événements.
```php
$stopwatch->openSection();
$stopwatch->start('validation');
$stopwatch->stopSection('parsing');
```

### 2. Twig
```twig
{% stopwatch 'render-blog-posts' %}
    {% for post in posts %} ... {% endfor %}
{% endstopwatch %}
```

### 3. Blackfire
La solution recommandée (commerciale) pour le profiling avancé (Call graphs, I/O, CPU, RAM). Bien plus précis que le Stopwatch pour trouver les goulots d'étranglement profonds.

## ⚠️ Points de vigilance (Certification)
*   **Classmap Authoritative** : Savoir que cette option de Composer empêche le "file system lookup" pour les classes manquantes.
*   **OPcache Timestamps** : Savoir que le désactiver (`0`) est la meilleure opti prod, mais nécessite un restart PHP.
*   **Inline Factories** : L'option `.container.dumper.inline_factories` est liée à l'optimisation du Preloading.

## Ressources
*   [Symfony Docs - Performance](https://symfony.com/doc/current/performance.html)

