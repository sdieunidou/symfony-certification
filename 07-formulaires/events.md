# Événements de Formulaire

## Concept clé
Les formulaires Symfony ne sont pas statiques. On peut modifier leur structure (ajouter/supprimer des champs) ou leurs données dynamiquement pendant le cycle de vie (soumission).
Cela se fait via l'**EventDispatcher** interne au composant Form.

## Les Événements (Ordre Chronologique)

### Phase 1 : Pré-remplissage (Initialisation)
1.  **`PRE_SET_DATA`** : Avant que les données initiales (objet ou array) ne soient injectées dans le formulaire.
    *   *Usage* : Modifier le formulaire selon l'objet existant (ex: si `$user->getId()` existe, désactiver le champ 'username').
2.  **`POST_SET_DATA`** : Après que les données sont injectées.
    *   *Usage* : Lire les données générées par les DataTransformers.

### Phase 2 : Soumission (RequestHandler)
3.  **`PRE_SUBMIT`** : On reçoit les données brutes du client (`$_POST`). L'objet sous-jacent n'est pas encore touché.
    *   *Usage* : Modifier le formulaire selon le choix de l'utilisateur (Champs dépendants : Pays -> Villes).
4.  **`SUBMIT`** : Les données sont converties (View -> Norm -> Model) mais pas encore injectées dans l'objet final.
    *   *Usage* : Modifier les données normalisées.
5.  **`POST_SUBMIT`** : L'objet final est hydraté.
    *   *Usage* : Actions finales, validation complexe nécessitant l'objet complet.

## Exemple : Champs Dépendants (Dynamic Modification)
Scénario classique : Un champ "Sport" apparaît seulement si l'utilisateur a coché "Aime le sport".

```php
$builder->get('likesSport')->addEventListener(
    FormEvents::POST_SUBMIT, // On écoute sur le champ 'likesSport'
    function (FormEvent $event) {
        $likesSport = $event->getData(); // true ou false
        $form = $event->getForm(); // Le champ 'likesSport'
        $parent = $form->getParent(); // Le formulaire complet

        if ($likesSport) {
            $parent->add('sportName', TextType::class);
        }
    }
);
```

## 🧠 Concepts Clés
1.  **FormEvents vs KernelEvents** : Rien à voir. Ce sont des événements internes au composant Form.
2.  **Event Subscriber** : Pour une logique complexe réutilisable, créez une classe Subscriber (`EventSubscriberInterface`) plutôt que des Closures dans le `buildForm`.

## ⚠️ Points de vigilance (Certification)
*   **Data vs Form** :
    *   `PRE_SET_DATA` : `$event->getData()` est votre **Objet** (Entity/DTO).
    *   `PRE_SUBMIT` : `$event->getData()` est le **Tableau** des données soumises (`['field' => 'value']`).
*   **Modification structurelle** : On ne peut ajouter/supprimer des champs que lors des événements `PRE_SET_DATA` et `PRE_SUBMIT`. Si vous le faites plus tard, c'est trop tard (le framework a déjà mappé les données).

## Ressources
*   [Symfony Docs - Form Events](https://symfony.com/doc/current/form/events.html)
*   [Dynamic Forms](https://symfony.com/doc/current/form/dynamic_form_modification.html)
