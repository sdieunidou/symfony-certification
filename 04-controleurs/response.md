# L'objet Response (Usage Contrôleur)

## Concept clé
Un contrôleur **DOIT** retourner un objet `Symfony\Component\HttpFoundation\Response`.
Cela permet au Kernel d'envoyer les headers et le contenu proprement.

## Helpers de Création (`AbstractController`)

### 1. HTML (`render`)
```php
return $this->render('blog/index.html.twig', ['posts' => $posts]);
// Crée une Response(content, 200, ['Content-Type' => 'text/html'])
```

### 2. JSON (`json`)
```php
return $this->json($data, 201, ['X-Custom' => 'foo'], ['groups' => 'api']);
// Utilise le Serializer Symfony pour transformer $data en JSON.
// Le 4ème argument est le Context du Serializer (ex: Groupes de sérialisation).
```

### 3. Fichier (`file`)
```php
return $this->file($path, 'download_name.pdf');
// Crée une BinaryFileResponse optimisée.
```

### 4. Streaming (`stream`)
```php
return $this->stream(function () {
    echo "Hello";
    flush();
    sleep(1);
    echo "World";
});
// Crée une StreamedResponse.
```

### 5. Early Hints (`sendEarlyHints`)
Indique au navigateur de commencer à télécharger des ressources (CSS, JS, Fonts) **avant** même que le contrôleur ait fini de générer la page. Améliore la performance perçue (LCP).

```php
use Symfony\Component\WebLink\Link;

public function index(): Response
{
    $response = $this->sendEarlyHints([
        (new Link(href: '/style.css'))->withAttribute('as', 'style'),
        new Link(rel: 'preconnect', href: 'https://fonts.google.com'),
    ]);

    // ... traitement long (DB calls, rendering) ...

    return $this->render('index.html.twig', response: $response);
}
```
*Note : Nécessite un serveur compatible (ex: FrankenPHP) ou un proxy supportant le status HTTP 103.*

## Modification de la Réponse
Parfois, il faut créer la réponse, la modifier, puis la retourner.

```php
$response = $this->render('...');
$response->setStatusCode(404); // Changer le status
$response->headers->set('X-Robots-Tag', 'noindex'); // Ajouter header
$response->setPublic(); // Cache HTTP
$response->setMaxAge(3600);

return $response;
```

## 🧠 Concepts Clés
1.  **Serializer Integration** : La méthode helper `json()` est très puissante car elle s'intègre au composant Serializer. Si le Serializer n'est pas installé, elle utilise `json_encode`.
2.  **Empty Response** : Pour une 204 No Content (API), retournez `return new Response(null, 204);`.

## ⚠️ Points de vigilance (Certification)
*   **RenderView vs Render** :
    *   `render()` retourne une **Response** (prêt à l'emploi).
    *   `renderView()` retourne une **string** (le HTML brut). Utile pour générer un corps d'email ou du JSON contenant du HTML.
*   **Exceptions** : Lancer une exception interrompt le contrôleur. C'est le Kernel qui attrapera l'exception et générera une Réponse d'erreur.

## Ressources
*   [Symfony Docs - Response](https://symfony.com/doc/current/components/http_foundation.html#response)
