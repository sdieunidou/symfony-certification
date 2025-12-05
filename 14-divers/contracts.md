# Symfony Contracts

## Concept Clé
Le composant **Contracts** fournit un ensemble d'abstractions (Interfaces, Traits) extraites des composants Symfony.
L'objectif est de permettre un **couplage faible** et une meilleure **interopérabilité**.

Au lieu de dépendre d'une implémentation concrète (ex: `Symfony\Component\Cache\Adapter\FilesystemAdapter`), vous dépendez d'un contrat (ex: `Symfony\Contracts\Cache\CacheInterface`). Cela vous permet de changer d'implémentation (passer de fichiers à Redis) sans changer une ligne de votre code métier.

## Pourquoi utiliser les Contrats ?
1.  **Découplage** : Votre code ne dépend plus du framework entier, mais de petites interfaces stables.
2.  **Interopérabilité** : D'autres librairies PHP peuvent implémenter ces contrats.
3.  **Légèreté** : Les paquets `symfony/*-contracts` sont très légers (quelques fichiers PHP, pas de dépendances lourdes).

## Principaux Contrats

### 1. Service Contracts (`symfony/service-contracts`)
*   **`ServiceSubscriberInterface`** : Pour les services qui ont besoin d'accéder au conteneur de manière contrôlée (utilisé par les contrôleurs et les locator).
*   **`ResetInterface`** : Pour les services qui doivent être remis à zéro entre deux requêtes (ex: dans un worker Messenger).

### 2. Cache Contracts (`symfony/cache-contracts`)
*   **`CacheInterface`** : L'interface moderne pour le cache (avec la méthode `get(key, callback)` qui gère le "Stampede protection").
*   **`TagAwareCacheInterface`** : Pour gérer l'invalidation par tags.

### 3. EventDispatcher Contracts (`symfony/event-dispatcher-contracts`)
*   **`EventDispatcherInterface`** : Permet de dispatcher des événements (compatible PSR-14).

### 4. HttpClient Contracts (`symfony/http-client-contracts`)
*   **`HttpClientInterface`** : Abstraction pour faire des requêtes HTTP. Permet de mocker facilement le client dans les tests ou de changer de backend (Curl, Native).

### 5. Translation Contracts (`symfony/translation-contracts`)
*   **`TranslatorInterface`** : Pour traduire des messages.
*   **`LocaleAwareInterface`** : Pour les services qui ont besoin de connaître la locale courante.

### 6. Deprecation Contracts (`symfony/deprecation-contracts`)
*   Fournit une fonction `trigger_deprecation()` standardisée pour gérer les dépréciations de manière unifiée dans l'écosystème PHP.

## Différence avec les PSRs (PHP-FIG)
Les PSRs (PHP Standards Recommendations) sont des standards globaux pour PHP.
Les Contrats Symfony :
*   Sont souvent **bâtis sur les PSRs** quand elles existent et sont pertinentes.
*   Vont parfois **plus loin** ou proposent une approche différente quand la PSR est jugée trop complexe ou limitante pour l'usage dans Symfony (ex: Cache PSR-6 vs Symfony Cache Contract qui est plus simple).
*   Évoluent au rythme de Symfony (Backward Compatibility Promise).

## Installation
Les contrats sont des paquets séparés.
```bash
composer require symfony/service-contracts
composer require symfony/cache-contracts
```
Note : En installant un composant principal (ex: `symfony/cache`), le contrat correspondant est souvent installé automatiquement.

## Ressources
*   [Symfony Docs - Contracts](https://symfony.com/doc/current/components/contracts.html)
