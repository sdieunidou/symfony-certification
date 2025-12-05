# Messages Flash

## Concept clé
Les Messages Flash sont un pattern UX pour afficher des notifications temporaires à l'utilisateur après une action (ex: "Votre profil a été mis à jour").
Techniquement, ils sont stockés en **Session**, affichés une fois, puis **détruits automatiquement** (auto-expiring).

## Utilisation dans le Contrôleur
L'`AbstractController` fournit le helper `addFlash(string $type, mixed $message)`.

```php
public function delete(int $id): Response
{
    // ... suppression ...

    // On peut ajouter plusieurs messages du même type
    $this->addFlash('success', 'Élément supprimé.');
    $this->addFlash('info', 'Un email de confirmation a été envoyé.');

    return $this->redirectToRoute('list');
}
```

## Affichage dans Twig
Symfony expose la variable globale `app`.

```twig
{# templates/base.html.twig #}

{% for label, messages in app.flashes %}
    {% for message in messages %}
        <div class="alert alert-{{ label }}">
            {{ message }}
        </div>
    {% endfor %}
{% endfor %}
```
*Note : La lecture `app.flashes` consomme les messages. Si vous rafraîchissez la page, ils disparaissent.*

## Types de Messages
Le "type" (`success`, `warning`, `danger`, `info`) est une convention libre. Il correspond souvent aux classes CSS de Bootstrap ou Tailwind.

## Cas Avancés (FlashBagInterface)
Si vous avez besoin de manipuler les flashes sans les supprimer (peek) ou vérifier s'il y en a :

```php
// Injection de RequestStack
$flashBag = $requestStack->getSession()->getFlashBag();

// Vérifier sans consommer
if ($flashBag->has('error')) { ... }

// Lire sans supprimer (Peek)
$errors = $flashBag->peek('error');

// Lire et supprimer (Get - comportement par défaut)
$errors = $flashBag->get('error');
```

## 🧠 Concepts Clés
1.  **Survivre à la redirection** : C'est le but principal. HTTP est stateless, donc une variable PHP normale meurt à la fin du script. La Flash survit en session pour la requête suivante (qui affiche le résultat).
2.  **Stateless API** : Les messages flash ne fonctionnent **PAS** dans une API Stateless (JWT), car il n'y a pas de session. Le client (Frontend) doit gérer ses propres notifications basées sur la réponse HTTP (200/201/400).

## ⚠️ Points de vigilance (Certification)
*   **Array** : `addFlash` ajoute à une liste. Il n'écrase pas le message précédent.
*   **Session Required** : Si les sessions sont désactivées, `addFlash` lancera une exception.

## Ressources
*   [Symfony Docs - Flash Messages](https://symfony.com/doc/current/controller.html#flash-messages)
