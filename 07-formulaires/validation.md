# Validation des Formulaires

## Concept clé
Le composant Form s'appuie sur le composant **Validator** pour vérifier la validité des données soumises.
La validation est déclenchée automatiquement lors du `handleRequest()` ou `$form->submit()`.

## Configuration de la Validation

### 1. Validation de l'Objet Sous-jacent (Recommended)
C'est la méthode standard. Le formulaire ne contient pas de règles, il se contente de déléguer à l'objet mappé (`data_class`).

```php
// src/Entity/Task.php
class Task
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private string $title;
}
```
Le formulaire lit ces attributs via le `ValidatorExtension` et mappe les erreurs sur les champs correspondants.

### 2. Validation dans le Formulaire (`constraints` option)
Pour les champs non mappés (`mapped: false`) ou les formulaires sans classe, définissez les contraintes directement dans le `buildForm`.

```php
$builder->add('terms', CheckboxType::class, [
    'mapped' => false,
    'constraints' => [
        new IsTrue([
            'message' => 'Vous devez accepter les conditions.',
        ]),
    ],
]);
```

## Groupes de Validation
Les formulaires permettent de valider uniquement un sous-ensemble de contraintes via les **Validation Groups**.

### Configuration Globale
```php
public function configureOptions(OptionsResolver $resolver): void
{
    $resolver->setDefaults([
        'validation_groups' => ['Default', 'Registration'],
    ]);
}
```

### Groupes Dynamiques (Callback)
On peut choisir les groupes dynamiquement selon les données soumises (ex: Client Pro vs Particulier).

```php
use Symfony\Component\Form\FormInterface;

$resolver->setDefaults([
    'validation_groups' => function (FormInterface $form) {
        $data = $form->getData();
        if ($data->isProfessional()) {
            return ['Default', 'Pro'];
        }
        return ['Default'];
    },
]);
```

### Groupes selon le Bouton Cliqué
Très utile pour les formulaires multi-actions (Brouillon vs Publier).

```php
$builder->add('draft', SubmitType::class, [
    'validation_groups' => false, // Aucune validation
]);

$builder->add('publish', SubmitType::class, [
    'validation_groups' => ['Default', 'Publish'],
]);
```

## Désactiver la Validation HTML5
Symfony génère des attributs `required`, `maxlength`, `pattern` HTML5 automatiquement.
Pour tester la validation serveur ou avoir un style d'erreur unifié, désactivez la validation client :

```twig
{{ form_start(form, {'attr': {'novalidate': 'novalidate'}}) }}
```

## 🧠 Concepts Clés
1.  **Bubbling** : Si une erreur ne peut pas être attachée à un champ spécifique (ex: champ supprimé dynamiquement), elle "remonte" au formulaire parent (`error_bubbling`). Elle sera affichée via `form_errors(form)`.
2.  **Validité** : `$form->isValid()` retourne `true` si aucune contrainte n'a été violée. Cela implique que `$form->isSubmitted()` est aussi true.

## ⚠️ Points de vigilance (Certification)
*   **DataTransformers** : Si la transformation échoue (`TransformationFailedException`), le formulaire est invalide, mais ce n'est pas une erreur de "Validation" au sens `ConstraintViolation`. C'est une erreur de synchronisation.
*   **Cascade** : Utilisez `#[Assert\Valid]` sur les relations (Embedded Forms) pour valider les sous-objets.

## Ressources
*   [Symfony Docs - Form Validation](https://symfony.com/doc/current/form/validation.html)
