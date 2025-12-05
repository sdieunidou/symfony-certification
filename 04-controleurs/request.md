# L'objet Request dans le Contrôleur

## Concept clé
L'accès à la requête HTTP courante se fait par Injection de Dépendance dans la méthode du contrôleur.

## Application dans Symfony 7.0
Il suffit de typer un argument avec `Symfony\Component\HttpFoundation\Request`.

## Exemple de code

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search')]
    public function index(Request $request): Response
    {
        // Récupération des paramètres GET
        $query = $request->query->get('q');
        $page = $request->query->getInt('page', 1);
        
        // Vérification AJAX
        if ($request->isXmlHttpRequest()) {
            // ...
        }

        return $this->render('search/results.html.twig', [
            'query' => $query
        ]);
    }
}
```

## Points de vigilance (Certification)
*   **Argument Resolver** : C'est le composant `HttpKernel` (via `ArgumentResolver`) qui inspecte la signature de votre méthode, voit le type `Request`, et injecte l'objet requête courant.
*   **Ordre** : L'ordre des arguments n'importe pas (sauf pour les paramètres de route optionnels qui doivent être à la fin ou correspondre aux noms).
*   **ParamConverter** : Ne pas confondre l'injection de `Request` (native) avec les convertisseurs de paramètres (Entity) qui transforment un `id` en objet `User`.

## Ressources
*   [Symfony Docs - The Request Object](https://symfony.com/doc/current/controller.html#the-request-object-as-a-controller-argument)

