# Conventions de Nommage (Contrôleurs)

## Concept clé
Les contrôleurs sont le point d'entrée de la logique applicative. Pour que Symfony les détecte et les configure correctement (autowiring, routing), il faut suivre les conventions.

## Application dans Symfony 7.0
1.  **Namespace** : `App\Controller`.
2.  **Suffixe** : Le nom de la classe doit finir par `Controller` (ex: `ProductController`).
3.  **Méthode** : Doit être `public` et retourner un objet `Response`.
4.  **Fichier** : `src/Controller/ProductController.php`.

## Exemple de code

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BlogPostController extends AbstractController
{
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        return $this->render('blog/list.html.twig');
    }
}
```

## Points de vigilance (Certification)
*   **Invokable Controller** : Un contrôleur peut ne définir qu'une seule méthode `__invoke()`. C'est pratique pour les actions complexes (ex: `UserRegistrationController`).
*   **Services** : Les contrôleurs sont enregistrés comme des services dans le conteneur. Ils sont "autowired" et "autoconfigured" (tagués `controller.service_arguments`).
*   **Public** : Depuis Symfony 4/5, les services contrôleurs sont **privés** par défaut (on ne peut pas faire `$container->get(MyController::class)`).

## Ressources
*   [Symfony Docs - Controller](https://symfony.com/doc/current/controller.html)

