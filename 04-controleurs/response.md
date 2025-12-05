# L'objet Response dans le Contrôleur

## Concept clé
Un contrôleur **doit** toujours retourner un objet `Response`.
Cependant, pour des cas simples, `AbstractController` fournit des méthodes pour créer ces réponses sans faire `new Response()`.

## Application dans Symfony 7.0
Helpers courants :
*   `$this->render(...)` : Crée une `Response` (200 OK) avec le contenu HTML.
*   `$this->json(...)` : Crée une `JsonResponse`.
*   `$this->file(...)` : Crée une `BinaryFileResponse` (téléchargement).

## Exemple de code

```php
<?php

public function api(): Response
{
    // Retourner du JSON
    return $this->json(['status' => 'ok']); 
    // Équivalent à :
    // return new JsonResponse(['status' => 'ok']);
}

public function download(): Response
{
    // Télécharger un fichier
    return $this->file('/path/to/file.pdf', 'facture.pdf');
}
```

## Points de vigilance (Certification)
*   **Exceptions** : Si vous lancez une exception (ex: `throw new \Exception()`), Symfony l'attrape et la convertit en Réponse d'erreur (500). Donc techniquement, lancer une exception "retourne" une réponse au client in fine.
*   **StreamedResponse** : Pour les gros fichiers ou les flux, ne pas utiliser `file()` ou `render()` simples.
*   **Attribut #[CurrentUser]** : Pour injecter l'utilisateur courant directement dans la méthode (`public function index(#[CurrentUser] ?User $user)`).

## Ressources
*   [Symfony Docs - Responses](https://symfony.com/doc/current/controller.html#returning-responses)

