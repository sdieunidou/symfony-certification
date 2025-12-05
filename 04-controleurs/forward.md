# Redirections Internes (Forward)

## Concept clé
La méthode `forward()` permet de transférer le traitement d'une action de contrôleur à une autre **en interne**, sans que le navigateur du client ne le sache (pas de changement d'URL).
C'est une **Sous-Requête** (Sub-Request).

## Différence avec Redirection HTTP
*   **Redirect (`redirectToRoute`)** :
    1.  Serveur répond 302 Location: /new-url.
    2.  Navigateur fait une nouvelle requête GET /new-url.
    3.  URL change. Performance : 2 requêtes HTTP complètes.
*   **Forward (`forward`)** :
    1.  Serveur instancie le nouveau contrôleur et l'appelle directement.
    2.  Serveur renvoie la réponse finale.
    3.  Navigateur ne voit rien (URL inchangée). Performance : 1 requête HTTP, mais 2 cycles Kernel.

## Utilisation
Méthode helper `AbstractController::forward()`.

```php
public function index(string $username): Response
{
    // Appelle App\Controller\OtherController::fancy($username, 'green')
    $response = $this->forward('App\Controller\OtherController::fancy', [
        'name'  => $username,
        'color' => 'green',
    ]);

    return $response;
}
```

## 🧠 Concepts Clés
1.  **Sub-Request** : Le Kernel est relancé (`handle` avec `HttpKernelInterface::SUB_REQUEST`).
2.  **Indépendance** : La sous-requête a son propre objet `Request` (cloné de la principale), ses propres attributs, etc.
3.  **Fragment Rendering** : C'est le même mécanisme utilisé par Twig `{{ render(controller('...')) }}` pour insérer des blocs dynamiques (ex: panier dans le header) sans dupliquer la logique.

## ⚠️ Points de vigilance (Certification)
*   **Usage** : C'est devenu assez rare en code moderne. On préfère souvent extraire la logique métier dans un **Service** réutilisable et l'appeler depuis les deux contrôleurs. Le Forward est "lourd" (instanciation contrôleur, cycle kernel).
*   **Arguments** : Les arguments passés à `forward` (tableau) sont injectés comme **attributs de requête** (`$request->attributes`) pour correspondre aux arguments de la méthode cible.

## Ressources
*   [Symfony Docs - Forwarding](https://symfony.com/doc/current/controller/forwarding.html)
