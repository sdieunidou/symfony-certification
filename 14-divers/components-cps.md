# Composants Cache, Process et Serializer

## 1. Composant Cache (PSR-6 / PSR-16)
Stockage clé-valeur haute performance pour les données (pas le cache HTTP).
*   **Contrat** : Implémente PSR-6 (CacheItemPool) et PSR-16 (SimpleCache).
*   **Adapters** : Redis, Memcached, DoctrineDbal, Filesystem, Array, Apcu.
*   **Chain** : Permet de chainer des caches (ex: Array -> Redis -> Filesystem).
*   **Tagging** : Permet d'invalider un groupe d'items d'un coup (`$item->tag('users')`).

```php
$value = $cache->get('user_123', function (ItemInterface $item) {
    $item->expiresAfter(3600);
    return $this->heavyComputation();
});
```

## 2. Composant Process
Exécution de sous-processus système de manière portable et orientée objet.
Gère les timeouts, les signaux, et le streaming de la sortie (stdout/stderr).

```php
use Symfony\Component\Process\Process;

$process = new Process(['ls', '-lsa']);
$process->run();

if (!$process->isSuccessful()) {
    throw new ProcessFailedException($process);
}

echo $process->getOutput();
```

## 3. Composant Serializer
Transforme des objets complexes en format d'échange (JSON, XML, CSV, YAML) et inversement.
Processus en 2 étapes :
1.  **Normalization** : Objet -> Tableau (Array). Géré par des `Normalizer`.
2.  **Encoding** : Tableau -> Chaîne (JSON). Géré par des `Encoder`.

### Groupes de Sérialisation
L'attribut `#[Groups]` permet de contrôler quels champs sont exposés.

```php
class User {
    #[Groups(['user:read'])]
    public string $email;
    
    #[Groups(['admin:read'])]
    public string $password;
}

$json = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
// JSON ne contiendra que l'email.
```

## 🧠 Concepts Clés
*   **Cache Stampede** : Le composant Cache gère nativement la protection contre le "Stampede" (quand le cache expire et que 1000 processus essaient de le régénérer en même temps). Le callback passé à `get()` est verrouillé.

## Ressources
*   [Symfony Docs - Cache](https://symfony.com/doc/current/components/cache.html)
*   [Symfony Docs - Process](https://symfony.com/doc/current/components/process.html)
*   [Symfony Docs - Serializer](https://symfony.com/doc/current/components/serializer.html)
