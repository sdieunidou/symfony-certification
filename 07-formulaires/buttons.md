# Gestion des Boutons (Buttons)

## Concept clé
Les boutons de soumission font partie intégrante du formulaire en Symfony.
Ils permettent non seulement de soumettre, mais aussi de savoir **quel** bouton a été cliqué pour déclencher des logiques différentes.

## Types de Boutons
*   `SubmitType` : Soumet le formulaire.
*   `ResetType` : Réinitialise le formulaire (HTML pur, rarement utilisé côté serveur).
*   `ButtonType` : Bouton générique (type="button"), utile pour le JS.

## Détecter le Clic (Controller)
C'est la fonctionnalité la plus puissante.

```php
$form->handleRequest($request);

if ($form->isSubmitted() && $form->isValid()) {
    // Vérifier quel bouton a été cliqué
    if ($form->get('saveAndAdd')->isClicked()) {
        // Logique "Sauvegarder et Nouveau"
        return $this->redirectToRoute('task_new');
    }
    
    if ($form->get('save')->isClicked()) {
        // Logique standard
        return $this->redirectToRoute('task_list');
    }
}
```

## Options Utiles
*   `attr` : Classes CSS (`btn btn-primary`).
*   `label` : Texte du bouton.
*   `validation_groups` : Groupes de validation spécifiques à ce bouton.

## Bonnes Pratiques
Bien qu'on puisse ajouter les boutons dans la classe `FormType`, il est souvent recommandé de les ajouter **dans le template Twig** ou dans le **Builder du Contrôleur**.
Pourquoi ? Pour rendre la classe `FormType` réutilisable (ex: le même formulaire utilisé pour Créer et Éditer n'a pas forcément les mêmes boutons).

**Option A : Dans le Contrôleur**
```php
$form = $this->createForm(TaskType::class, $task);
$form->add('save', SubmitType::class, ['label' => 'Créer']);
```

**Option B : Dans le FormType (si générique)**
```php
$builder->add('save', SubmitType::class);
```

## ⚠️ Points de vigilance (Certification)
*   **Nommage** : Donnez des noms explicites (`save`, `delete`, `publish`) pour pouvoir les récupérer via `$form->get('nom')`.
*   **HTML5** : Un bouton `<button type="submit">` sans nom/valeur n'est pas détectable par `isClicked()` de manière fiable. Symfony génère le bon HTML.

## Ressources
*   [Symfony Docs - Buttons](https://symfony.com/doc/current/form/multiple_buttons.html)
