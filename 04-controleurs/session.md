# Session

## Concept clé
La session permet de stocker des données utilisateur entre les requêtes.
Symfony fournit un service de session accessible via la requête ou l'injection de dépendance.

## Application dans Symfony 7.0
Depuis Symfony 6, on injecte `RequestStack` ou on utilise `$request->getSession()`.
L'objet Session implémente `SessionInterface`.

## Exemple de code

```php
<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    public function add(Request $request, int $id): Response
    {
        // Récupérer la session depuis la requête
        $session = $request->getSession();
        
        // Lire/Écrire
        $cart = $session->get('cart', []);
        $cart[$id] = ($cart[$id] ?? 0) + 1;
        $session->set('cart', $cart);
        
        return $this->redirectToRoute('cart_show');
    }
    
    // Via Injection de Service (utile dans les Services, pas forcément Controller)
    public function clear(RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $session->clear();
        // ...
    }
}
```

## Points de vigilance (Certification)
*   **Start** : La session démarre automatiquement dès qu'on essaie de lire/écrire.
*   **Storage** : Symfony supporte le stockage natif PHP (`php.ini`), mais aussi Pdo, Redis, etc. via la config `framework.session.handler_id`.
*   **Stateless** : Si vous faites une API REST pure (authentification par token JWT par exemple), n'utilisez **pas** les sessions. Configurez `framework.session.enabled: false` ou le firewall en `stateless: true`.

## Ressources
*   [Symfony Docs - Sessions](https://symfony.com/doc/current/session.html)

