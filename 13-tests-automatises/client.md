# Objet Client (KernelBrowser)

## Concept clé
L'objet `KernelBrowser` est votre interface principale pour les tests fonctionnels. Il remplace le navigateur web.
Il permet d'envoyer des requêtes et d'interagir avec la réponse via le `Crawler`.

## Méthodes Principales

### 1. Navigation (`request`)
```php
// Signature : method, uri, parameters, files, server, content
$crawler = $client->request('GET', '/posts');

// POST API JSON
$client->request(
    'POST', 
    '/api/posts', 
    [], 
    [], 
    ['CONTENT_TYPE' => 'application/json'], 
    json_encode(['title' => 'Test'])
);
```

### 2. Interaction (`click`, `submit`)
```php
// Clic sur un lien (Link object du Crawler)
$client->click($link);

// Soumission de formulaire (Form object du Crawler)
$client->submit($form, ['field' => 'value']);
```

### 3. Authentification (`loginUser`)
C'est un helper magique pour connecter un utilisateur sans passer par le formulaire de login (lent).

```php
$user = $userRepository->findOneByEmail('admin@test.com');
// Simule le login sur le firewall 'main'
$client->loginUser($user);
```

Vous pouvez aussi utiliser un utilisateur en mémoire (sans base de données) si configuré dans `security.yaml` :

```php
use Symfony\Component\Security\Core\User\InMemoryUser;

$testUser = new InMemoryUser('admin', 'password', ['ROLE_ADMIN']);
$client->loginUser($testUser);
```

### 4. AJAX (`xmlHttpRequest`)
Raccourci pour `request()` avec le header `X-Requested-With: XMLHttpRequest`.

```php
$client->xmlHttpRequest('GET', '/api/search');
```

## Historique et Navigation
*   `$client->back()` : Retour page précédente.
*   `$client->forward()` : Page suivante.
*   `$client->reload()` : Rafraîchir.

## Gestion du Kernel (Reboot)
Par défaut, le client **reboote le kernel** entre chaque requête (`request()`). Cela garantit l'isolation (nouveaux services).
Conséquence : les entités Doctrine sont détachées.

Si vous avez besoin de persister des états en mémoire (non recommandé mais parfois utile), vous pouvez désactiver le reboot :
```php
$client->disableReboot();
```

## 🧠 Concepts Clés
1.  **Interne** : Le client ne fait **pas** de vraies requêtes HTTP réseau (pas de cURL). Il instancie le Kernel et appelle `handle()`. C'est très rapide.
2.  **Panther** : Si vous avez besoin de tester du Javascript (React/Vue), `KernelBrowser` ne suffit pas (il ne parse pas le JS). Utilisez `Symfony\Panther` (qui pilote un vrai Chrome/Firefox).

## ⚠️ Points de vigilance (Certification)
*   **Formulaires** : `submit()` prend un objet `Form` (extrait du Crawler), pas le nom du formulaire.
    *   `$client->submitForm('Button Label', [...])` est un raccourci pratique introduit récemment.

## Ressources
*   [Symfony Docs - KernelBrowser](https://symfony.com/doc/current/testing.html#making-requests)
