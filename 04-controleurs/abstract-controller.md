# La classe AbstractController

## Concept clé
Bien que les contrôleurs puissent être de simples classes PHP, il est recommandé d'étendre `AbstractController`. Cela donne accès à de nombreuses méthodes helper ("shortcuts") pour les tâches courantes.

## Application dans Symfony 7.0
Méthodes helper principales fournies par `AbstractController` :
*   `render(string $view, array $params)` : Retourne une `Response` avec le HTML généré par Twig.
*   `json($data)` : Retourne une `JsonResponse`.
*   `redirectToRoute($route, $params)` : Retourne une `RedirectResponse`.
*   `createNotFoundException($msg)` : Lance une exception 404.
*   `createAccessDeniedException($msg)` : Lance une exception 403.
*   `getParameter($name)` : Récupère un paramètre de configuration.
*   `isGranted($attribute)` : Vérifie les droits de sécurité.
*   `getUser()` : Récupère l'utilisateur connecté.
*   `addFlash($type, $msg)` : Ajoute un message flash.

## Exemple de code

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends AbstractController
{
    public function index(): Response
    {
        // Accès à l'utilisateur
        $user = $this->getUser();
        
        // Vérification de rôle
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        // Rendu Twig
        return $this->render('dashboard/index.html.twig', [
            'user' => $user,
        ]);
    }
}
```

## Points de vigilance (Certification)
*   **ContainerBag** : `AbstractController` utilise un "Service Locator" pour accéder aux services (Twig, Router, Security...) uniquement quand on les demande. Cela rend le contrôleur léger.
*   **Controller vs AbstractController** : Il existait une classe `Controller` (dépréciée/supprimée) qui injectait *tout* le conteneur. `AbstractController` est la norme depuis Symfony 4.
*   **Injection** : Si vous avez besoin d'un service personnalisé, injectez-le dans le constructeur ou dans la méthode (action) du contrôleur. Ne pas essayer de faire `$this->get('mon_service')` (cette méthode n'existe plus ou est dépréciée pour les services privés).

## Ressources
*   [Symfony Docs - Controller Base Class](https://symfony.com/doc/current/controller.html#the-base-controller-class-abstractcontroller)

