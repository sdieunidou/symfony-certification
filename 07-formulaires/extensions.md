# Extensions de Type de Formulaire

## Concept clé
Comment modifier un type de formulaire existant (natif ou tiers) sans utiliser l'héritage ?
Par exemple, ajouter une option `help_tooltip` à *tous* les champs de type `TextType`.
C'est le principe du "Décorateur" appliqué aux types.

## Application dans Symfony 7.0
Créer une classe implémentant `FormTypeExtensionInterface` (ou étendant `AbstractTypeExtension`).

```php
#[AsFormTypeExtension] // Autoconfiguration PHP 8
class HelpTooltipExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        // Appliquer à tous les champs
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        // Ajouter la nouvelle option
        $resolver->setDefined(['help_tooltip']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Passer la valeur à la vue Twig
        if (isset($options['help_tooltip'])) {
            $view->vars['help_tooltip'] = $options['help_tooltip'];
        }
    }
}
```

Ensuite, dans le thème Twig, on peut utiliser `{{ help_tooltip }}`.

## Points de vigilance (Certification)
*   **Config** : Si vous n'utilisez pas l'autoconfiguration, il faut taguer le service avec `form.type_extension`.
*   **Portée** : En étendant `FormType`, vous étendez *tous* les types (car tous en héritent). Pour cibler uniquement les champs texte, étendez `TextType`.

## Ressources
*   [Symfony Docs - Form Type Extensions](https://symfony.com/doc/current/form/create_form_type_extension.html)

