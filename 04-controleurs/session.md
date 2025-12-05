# Session (Usage Contrôleur)

## Concept clé
La session permet de persister des données utilisateur d'une page à l'autre.
Dans Symfony, la session est un "Service" accessible via la Requête.

## Accès (Injection)
Depuis Symfony 6, la manière recommandée est d'injecter `RequestStack`.

```php
use Symfony\Component\HttpFoundation\RequestStack;

class CartController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack
    ) {}

    public function index(): Response
    {
        $session = $this->requestStack->getSession();
        
        // API Fluide
        $cart = $session->get('cart', []);
        $session->set('cart', $updatedCart);
        $session->remove('cart');
        $session->clear(); // Vide tout
        
        return $this->render('...');
    }
}
```
*On peut aussi faire `$request->getSession()` si on a injecté `Request`.*

## Session Bags
La session Symfony est divisée en "Sacs" (Bags) pour organiser les données :
1.  **AttributeBag** : Les données générales (`get`, `set`). C'est le sac par défaut.
2.  **FlashBag** : Messages temporaires (`addFlash`).
3.  **MetadataBag** : Méta-données (date de création, dernière activité).

## Typage (Contrainte)
La session stocke des données sérialisées (PHP serialize).
*   On peut stocker des scalaires (int, string, array).
*   On **PEUT** stocker des objets, **MAIS** c'est déconseillé (problèmes de dé-sérialisation si la classe change, `__PHP_Incomplete_Class`). Préférez stocker des IDs et recharger les entités depuis la DB.

## 🧠 Concepts Clés
1.  **Lazy Start** : La session ne démarre (`session_start()`) que si vous lisez ou écrivez dedans. Si vous n'y touchez pas, aucun cookie `PHPSESSID` n'est créé (perf + cache friendly).
2.  **Invalidate** : `$session->invalidate()` détruit la session et en recrée une nouvelle (nouvel ID). Recommandé après le Login/Logout pour éviter la fixation de session.
3.  **Stateless** : Une application Stateless (API REST) ne doit pas utiliser la session.

## ⚠️ Points de vigilance (Certification)
*   **Service `session`** : Le service `session` est déprécié en injection directe. Il faut passer par `RequestStack`.
*   **Unit Testing** : En test unitaire, la session est souvent un `MockArraySessionStorage` qui simule le comportement en mémoire.

## Ressources
*   [Symfony Docs - Sessions](https://symfony.com/doc/current/session.html)
