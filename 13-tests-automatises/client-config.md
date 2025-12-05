# Configuration du Client de Test (`KernelBrowser`)

## Concept clé
Le `KernelBrowser` (le client retourné par `createClient()`) simule un navigateur. Il peut être configuré pour chaque test ou globalement.

## Options de Création
On peut passer des options lors de la création du client.

```php
$client = static::createClient([], [
    // Simuler un host (pour le routing par sous-domaine)
    'HTTP_HOST' => 'api.mysite.com',
    // Simuler HTTPS
    'HTTPS' => true,
]);
```

## Configuration de l'Environnement

### Fichiers .env
En environnement de test, Symfony charge les fichiers dans cet ordre spécifique (le dernier écrase le précédent) :
1.  `.env` (Défauts globaux)
2.  `.env.test` (Spécifique aux tests, commité)
3.  `.env.test.local` (Spécifique à la machine, non commité)

⚠️ **Attention** : Le fichier `.env.local` est **ignoré** en test pour garantir la cohérence des résultats entre machines.

### Options de Boot
Lors du démarrage du kernel (via `bootKernel` ou `createClient`), vous pouvez surcharger l'environnement et le mode debug :

```php
self::bootKernel([
    'environment' => 'my_test_env',
    'debug'       => false, // Recommandé pour la performance (désactive le cache clearing auto)
]);
```

## Configuration du Comportement

### 1. Redirections (`followRedirects`)
Par défaut, le client **ne suit pas** les redirections (il s'arrête sur la 302 pour vous laisser l'asserter).
*   `$client->followRedirects(true)` : Suit automatiquement (comportement navigateur).
*   `$client->followRedirect()` : Suit une fois manuellement.

### 2. Exceptions (`catchExceptions`)
Par défaut, le client attrape les exceptions PHP et retourne une réponse 500 (HTML Symfony Error Page).
*   `$client->catchExceptions(false)` : Laisse l'exception remonter jusqu'à PHPUnit.
    *   **Avantage** : Le test échoue avec la stack trace de l'erreur dans la console (beaucoup plus facile à débugger).
    *   **Usage** : Recommandé en dev/debug, sauf si vous testez spécifiquement l'affichage de la page d'erreur 500 personnalisée.

### 3. Headers par défaut (`setServerParameters`)
Pour simuler une authentification API ou un User-Agent sur toutes les requêtes du client.

```php
$client->setServerParameters([
    'HTTP_AUTHORIZATION' => 'Bearer MY_TOKEN',
    'HTTP_ACCEPT' => 'application/json',
]);
```

## 🧠 Concepts Clés
1.  **Stateful** : Le client garde les cookies (et la session) entre les requêtes tant qu'il n'est pas détruit.
2.  **Reboot** : Le Kernel est redémarré à chaque appel de `request()` pour isoler la mémoire, mais le client persiste les cookies pour simuler la continuité.

## ⚠️ Points de vigilance (Certification)
*   **Server vs Headers** : Les méthodes utilisent la nomenclature PHP `$_SERVER` (`HTTP_HOST`, `REMOTE_ADDR`) et non les noms de headers HTTP standard (`Host`, `X-Forwarded-For`).

## Ressources
*   [Symfony Docs - Client Configuration](https://symfony.com/doc/current/testing.html#making-requests)
