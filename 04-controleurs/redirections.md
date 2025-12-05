# Redirections HTTP

## Concept clé
Rediriger l'utilisateur vers une autre URL (réponse 301 ou 302).

## Application dans Symfony 7.0
L'`AbstractController` fournit `redirectToRoute()` (vers une route interne) et `redirect()` (vers une URL externe).

## Exemple de code

```php
<?php

public function index(): Response
{
    // Vers une route avec paramètres
    return $this->redirectToRoute('blog_show', ['slug' => 'symfony-7']);
    
    // Vers une URL externe
    return $this->redirect('https://symfony.com');
    
    // Changement de code (301 Permanent)
    return $this->redirectToRoute('home', [], Response::HTTP_MOVED_PERMANENTLY);
}
```

## Points de vigilance (Certification)
*   **Retour** : N'oubliez pas le `return`. Appeler la méthode ne suffit pas, il faut retourner l'objet `RedirectResponse` qu'elle produit.
*   **RedirectResponse** : C'est une sous-classe de `Response`.
*   **Keep query** : Par défaut, les paramètres de requête (GET) ne sont pas conservés lors d'une redirection, sauf si vous les passez explicitement.

## Ressources
*   [Symfony Docs - Redirections](https://symfony.com/doc/current/controller.html#redirecting)

