# Traitement des Formulaires

## Concept clé
Le traitement d'un formulaire suit un pattern strict ("Handle Request").
1.  Le formulaire reçoit la requête HTTP.
2.  Il mappe les données de la requête sur l'objet sous-jacent.
3.  Il vérifie la soumission et la validité.

## Application dans Symfony 7.0

```php
public function new(Request $request): Response
{
    $task = new Task();
    $form = $this->createForm(TaskType::class, $task);

    // 1. Mappe la requête (POST) sur le formulaire et l'objet $task
    $form->handleRequest($request);

    // 2. Vérifie si le formulaire a été soumis ET est valide
    if ($form->isSubmitted() && $form->isValid()) {
        // $task contient maintenant les données soumises
        
        // Sauvegarder en DB...
        // $entityManager->persist($task);
        // $entityManager->flush();

        // Redirection (pattern Post-Redirect-Get)
        return $this->redirectToRoute('task_success');
    }

    return $this->render('task/new.html.twig', [
        'form' => $form,
    ]);
}
```

## Points de vigilance (Certification)
*   **handleRequest** : Cette méthode est magique. Elle regarde si la méthode est POST (par défaut), si les champs sont présents, remplit l'objet, et lance la validation.
*   **isSubmitted** : Retourne `true` si le formulaire a été envoyé.
*   **isValid** : Retourne `true` si les contraintes de validation (Validation Constraints) sont respectées.
*   **Patch** : Si la méthode est PATCH (API), `handleRequest` ne mettra à jour que les champs soumis (submit partiel).

## Ressources
*   [Symfony Docs - Processing Forms](https://symfony.com/doc/current/forms.html#processing-forms)

