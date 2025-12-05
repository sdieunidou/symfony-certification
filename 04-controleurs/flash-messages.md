# Messages Flash

## Concept clé
Les messages "Flash" sont des messages stockés en session qui ne durent que le temps d'une seule requête supplémentaire (le temps d'une redirection). Ils sont utilisés pour notifier l'utilisateur ("Sauvegarde réussie", "Erreur...").

## Application dans Symfony 7.0
L'`AbstractController` fournit la méthode helper `addFlash()`.

## Exemple de code

```php
<?php

public function update(): Response
{
    // Traitement...
    
    // 1. Ajouter le message (type, message)
    $this->addFlash('success', 'Profil mis à jour !');
    $this->addFlash('warning', 'Pensez à changer votre mot de passe.');

    // 2. Rediriger
    return $this->redirectToRoute('profile');
}
```

```twig
{# Dans le template Twig (base.html.twig) #}
{% for label, messages in app.flashes %}
    {% for message in messages %}
        <div class="alert alert-{{ label }}">
            {{ message }}
        </div>
    {% endfor %}
{% endfor %}
```

## Points de vigilance (Certification)
*   **Session** : Les messages flash nécessitent que les sessions soient activées.
*   **Array** : `app.flashes` retourne un tableau de messages pour chaque type (car on peut ajouter plusieurs flashs du même type).
*   **Consommation** : Lire les messages flash (`app.flashes`) les supprime de la session. Ils ne s'afficheront pas à la requête suivante.

## Ressources
*   [Symfony Docs - Flash Messages](https://symfony.com/doc/current/controller.html#flash-messages)

