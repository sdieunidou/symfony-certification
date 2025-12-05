# Objet Client (KernelBrowser)

## Concept clé
L'objet `Client` (retourné par `static::createClient()`) simule un navigateur web. Il maintient un état (cookies, historique) entre les requêtes.

## Application dans Symfony 7.0
Le client permet d'interagir avec l'application sans passer par Apache/Nginx (requête interne).

```php
// Requête simple
$client->request('GET', '/post/1');

// Soumettre un formulaire
$client->submitForm('Login', [
    'email' => 'user@example.com',
    'password' => 'password',
]);

// Login automatique (pour les tests, évite de passer par le form)
$user = $userRepository->findOneByEmail('admin@example.com');
$client->loginUser($user);

// AJAX
$client->xmlHttpRequest('POST', '/api/data');

// Suivre redirection
$client->followRedirect();
```

## Points de vigilance (Certification)
*   **Isolation** : Le client redémarre le Kernel à chaque requête (`request()`) pour isoler les environnements, mais il garde les cookies (session PHP).
*   **Insulated** : On peut forcer le client à s'exécuter dans un processus PHP séparé (`$client->insulate()`), mais c'est lent.

## Ressources
*   [Symfony Docs - Test Client](https://symfony.com/doc/current/testing.html#making-requests)

