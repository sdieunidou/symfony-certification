# Composants Cache, Process et Serializer

## Cache
Stockage clé-valeur haute performance. Implémente PSR-6 et PSR-16.
Adapters : `Filesystem`, `Redis`, `Memcached`, `Apcu`, `Array` (test).
Concept de "Cache Pools" pour isoler les caches.

## Process
Exécuter des commandes système (sous-processus).
Remplaçant robuste de `exec()` ou `passthru()`.
```php
$process = new Process(['ls', '-lsa']);
$process->run();
```

## Serializer
Transformer des objets en format spécifique (JSON, XML, CSV) et inversement.
Composé de :
*   **Normalizers** : Objet <-> Tableau.
*   **Encoders** : Tableau <-> Format (JSON string).
Utilise les Groupes de sérialisation (`#[Groups(['user_read'])]`) pour contrôler les champs exposés.

## Ressources
*   [Symfony Docs - Cache](https://symfony.com/doc/current/components/cache.html)
*   [Symfony Docs - Process](https://symfony.com/doc/current/components/process.html)
*   [Symfony Docs - Serializer](https://symfony.com/doc/current/components/serializer.html)

