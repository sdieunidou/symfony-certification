# Configuration du Client

## Concept clé
Le client de test (`KernelBrowser`) peut être configuré pour simuler différents environnements ou comportements (HTTPS, Headers, Auth).

## Application dans Symfony 7.0

### Création avec options
```php
$client = static::createClient([], [
    'HTTP_HOST' => 'api.example.com',
    'HTTPS' => true,
]);
```

### Headers par défaut (SetServerParameters)
```php
$client->setServerParameters([
    'HTTP_AUTHORIZATION' => 'Bearer token123',
    'HTTP_USER_AGENT' => 'MyTestBot/1.0',
]);
```

### Comportement
*   `$client->followRedirects(true/false)` : Suivre automatiquement les redirections (défaut: true).
*   `$client->setMaxRedirects(3)` : Éviter les boucles infinies.
*   `$client->catchExceptions(false)` : Laisser les exceptions remonter (pour voir la stack trace PHPUnit au lieu de la page d'erreur HTML Symfony).

## Points de vigilance (Certification)
*   **Server Parameters** : Les headers HTTP sont préfixés par `HTTP_` (norme CGI/PHP). `Content-Type` devient `CONTENT_TYPE`.

## Ressources
*   [Symfony Docs - Test Client Config](https://symfony.com/doc/current/testing.html#making-requests)

