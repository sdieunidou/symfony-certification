# Événements de Formulaire

## Concept clé
Les formulaires dispatchent des événements pour permettre de modifier le formulaire ou les données *pendant* le processus de soumission.
Exemple classique : Modifier la liste des villes disponibles dans un select en fonction du pays choisi (Formulaire dynamique).

## Application dans Symfony 7.0
Les événements du `FormEvents` class.

### Flux de soumission (ordre simplifié)
1.  `PRE_SET_DATA` : Avant de remplir le formulaire avec les données initiales. (Moment pour ajouter/supprimer des champs selon l'objet).
2.  `POST_SET_DATA` : Après le remplissage.
3.  `PRE_SUBMIT` : Avant de soumettre les données de la requête au formulaire. (On reçoit les données brutes $_POST).
4.  `SUBMIT` : Les données sont soumises et transformées (Norm Data).
5.  `POST_SUBMIT` : Tout est fini, l'objet est hydraté.

```php
$builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
    $product = $event->getData();
    $form = $event->getForm();

    // Si le produit a déjà un ID, on ne peut pas changer son type
    if ($product && $product->getId() !== null) {
        $form->remove('type');
    }
});
```

## Points de vigilance (Certification)
*   **Data vs Form** : `PRE_SET_DATA` permet de modifier la structure du formulaire ($form->add) en fonction des données ($event->getData()).
*   **Pre-Submit** : Seul moment où on peut modifier le formulaire en fonction des données *envoyées par l'utilisateur* (ex: l'utilisateur a choisi "France", j'ajoute le champ "Département").

## Ressources
*   [Symfony Docs - Form Events](https://symfony.com/doc/current/form/events.html)

