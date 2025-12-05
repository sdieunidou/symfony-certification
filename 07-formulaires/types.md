# Création de Types Personnalisés (Custom Types)

## Concept clé
Si vous réutilisez souvent la même configuration de champ (ex: un sélecteur de code postal, un éditeur WYSIWYG), ou si vous avez besoin d'un comportement complexe (DataTransformer intégré), créez un **Custom Form Type**.

## Structure d'un Type

```php
namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class ZipCodeType extends AbstractType
{
    // 1. Héritage (Parent)
    public function getParent(): string
    {
        return TextType::class; // Se comporte comme un TextType
    }

    // 2. Options par défaut
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'attr' => ['class' => 'zip-code-input'],
            'help' => 'Format: 5 chiffres',
        ]);
    }
    
    // 3. Logique (DataTransformers / Listeners)
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // $builder->addModelTransformer(...)
    }

    // 4. Passage de variables à la vue (Twig)
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        // Ajoute une variable {{ is_metropolitan }} au template du widget
        $view->vars['is_metropolitan'] = true; 
    }
}
```

## Nom du Bloc (Block Prefix)
Par défaut, le nom du bloc pour le theming est dérivé du nom de la classe.
`App\Form\Type\ZipCodeType` -> `zip_code` (snake case sans "Type").
Bloc Twig associé : `zip_code_widget`.

Vous pouvez le forcer :
```php
public function getBlockPrefix(): string
{
    return 'mon_code_postal';
}
```

## 🧠 Concepts Clés
1.  **Composition** : Un Custom Type peut être simple (hérite de `TextType`) ou composite (hérite de `FormType` et ajoute plusieurs sous-champs via `buildForm`, comme `AddressType` qui a rue, ville, zip).
2.  **Service** : Les types sont des services. Vous pouvez injecter l'`EntityManager` ou le `RequestStack` dans le constructeur.

## ⚠️ Points de vigilance (Certification)
*   **Parent** : Si vous ne définissez pas `getParent()`, il hérite de `FormType` par défaut (le type de base composite). Si vous voulez juste styliser un champ texte, n'oubliez pas de retourner `TextType::class`.

## Ressources
*   [Symfony Docs - Creating Custom Types](https://symfony.com/doc/current/form/create_custom_field_type.html)
