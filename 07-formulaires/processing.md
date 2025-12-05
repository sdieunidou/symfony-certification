# Traitement des Formulaires (Processing)

## Concept clé
Le traitement d'un formulaire est un processus standardisé appelé le **Workflow de Soumission**.
Il synchronise la requête HTTP avec l'objet PHP.

## Le Pattern Standard

```php
public function edit(Request $request, Task $task): Response
{
    $form = $this->createForm(TaskType::class, $task);
    
    // Étape critique : Injection de la requête
    $form->handleRequest($request);

    // Vérification d'état
    if ($form->isSubmitted() && $form->isValid()) {
        // À ce stade, $task est mis à jour avec les nouvelles données
        
        $this->entityManager->flush();

        return $this->redirectToRoute('task_list');
    }

    return $this->render('task/edit.html.twig', ['form' => $form]);
}
```

## Ce que fait `handleRequest`
1.  Vérifie si la méthode HTTP correspond (POST par défaut).
2.  Si oui, il soumet le formulaire (`submit`).
3.  Remplit les champs avec les données de la requête (`$_POST` ou `$_GET`).
4.  Exécute les DataTransformers (View -> Norm -> Model).
5.  Lance la validation (Constraints).

## API vs HTML Forms
*   **HTML (POST standard)** : `handleRequest` lit `$_POST`.
*   **API (JSON)** : Depuis Symfony 6.3, le `RequestHandler` natif sait lire le JSON payload automatiquement si le Content-Type est `application/json`.
    *   Avant, il fallait utiliser `$form->submit(json_decode($request->getContent(), true))`.

## Soumission Manuelle (`submit`)
Pour les cas avancés (API, tests) :

```php
// true = clearMissing (met à null les champs absents, comme PUT)
// false = patch (ne touche pas aux champs absents, comme PATCH)
$form->submit($dataArray, false);
```

## 🧠 Concepts Clés
1.  **Immutabilité** : L'objet `$task` passé au formulaire est modifié par référence.
2.  **État** : Un formulaire a trois états principaux :
    *   Initial (non soumis).
    *   Soumis et Valide.
    *   Soumis et Invalide (contient des erreurs).
3.  **Validité Dynamique** : `$form->isValid()` dépend des groupes de validation actifs. Si vous utilisez des groupes conditionnels (selon le bouton cliqué), la validité peut changer.

## ⚠️ Points de vigilance (Certification)
*   **Validation** : `$form->isValid()` ne peut être appelé que si `$form->isSubmitted()` est true.
*   **GET Forms** : Pour les formulaires de recherche, configurez `method => GET` dans `configureOptions`. `handleRequest` lira alors `$_GET`. Pour éviter une URL polluée par le token, désactivez CSRF.

## Ressources
*   [Symfony Docs - Form Processing](https://symfony.com/doc/current/forms.html#processing-forms)
