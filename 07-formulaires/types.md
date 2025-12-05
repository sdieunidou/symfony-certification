# Types de Formulaires (AbstractType)

## Concept clé
Tout champ de formulaire est un "Type". Votre formulaire global est aussi un "Type" (Composite).
Chaque Type définit :
1.  **BuildForm** : Quels champs il contient (enfants).
2.  **BuildView** : Comment passer les variables à la vue (Twig).
3.  **ConfigureOptions** : Les options par défaut (`label`, `required`, etc.).
4.  **Parent** : De quoi il hérite (par défaut `FormType`).

## Application dans Symfony 7.0
Créer un Type personnalisé pour un champ complexe (ex: `ColorPickerType`).

```php
class ColorPickerType extends AbstractType
{
    public function getParent(): string
    {
        // Hérite de TextType (se comportera comme un champ texte)
        return TextType::class;
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'color-picker'],
        ]);
    }
}
```

## Points de vigilance (Certification)
*   **Extension** : Ne pas confondre "Créer un Type personnalisé" (nouvelle classe) et "Créer une Extension de Type" (modifier un type existant partout).
*   **Nom** : Le nom du bloc Twig associé (`_color_picker_widget`) est dérivé du nom de la classe (snake case sans "Type").

## Ressources
*   [Symfony Docs - Custom Types](https://symfony.com/doc/current/form/create_custom_field_type.html)

