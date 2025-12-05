# Component HttpFoundation

## Concept Clé
Le composant **HttpFoundation** fournit une couche orientée objet pour la spécification HTTP. Il remplace les variables globales PHP (`$_GET`, `$_POST`, `$_SESSION`, etc.) par des objets (`Request`, `Response`).

## Objets Principaux

### Request
Représente la requête HTTP entrante.
```php
use Symfony\Component\HttpFoundation\Request;

$request = Request::createFromGlobals();
$id = $request->query->get('id'); // $_GET['id']
$name = $request->request->get('name'); // $_POST['name']
$cookie = $request->cookies->get('PHPSESSID');
```

### Response
Représente la réponse HTTP sortante.
```php
use Symfony\Component\HttpFoundation\Response;

$response = new Response(
    'Contenu de la page',
    Response::HTTP_OK,
    ['content-type' => 'text/html']
);
$response->send();
```

### Session
Gestion des sessions utilisateur.
```php
$session = $request->getSession();
$session->set('user_id', 123);
```

## Fonctionnalités Avancées
*   **FileBag** : Gestion des uploads (`$request->files`).
*   **HeaderBag** : Normalisation des en-têtes HTTP.
*   **StreamedResponse** : Pour les réponses volumineuses (streaming).
*   **BinaryFileResponse** : Pour le téléchargement de fichiers.

## Ressources
*   [Symfony Docs - HttpFoundation](https://symfony.com/doc/current/components/http_foundation.html)
