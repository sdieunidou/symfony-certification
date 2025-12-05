# Extensions de Type de Formulaire

## Concept clé
Symfony applique le principe Open/Closed. Vous ne pouvez pas modifier le code de `TextType` ou `FormType` (classes natives), mais vous pouvez les étendre via une **Extension de Type**.
C'est l'équivalent d'un "Décorateur global" pour tous les formulaires de ce type.

## Cas d'usage
*   Ajouter une option globale (ex: `help_tooltip` sur tous les champs).
*   Ajouter une classe CSS par défaut sur tous les boutons.
*   Modifier la manière dont une option est gérée.

## Implémentation

Créer une classe qui implémente `FormTypeExtensionInterface` (ou étend `AbstractTypeExtension`).

```php
namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormTypeExtensionInterface;

class ImageIconExtension extends AbstractTypeExtension
{
    // 1. Quels types j'étends ?
    public static function getExtendedTypes(): iterable
    {
        // J'étends UNIQUEMENT TextType
        return [TextType::class];
    }

    // 2. Ajouter l'option
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['icon']); // Optionnelle
        $resolver->setAllowedTypes('icon', 'string');
    }

    // 3. Passer la valeur à la vue (Twig)
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (isset($options['icon'])) {
            $view->vars['icon'] = $options['icon'];
        }
    }
}
```

Ensuite dans le thème Twig :
```twig
{% block text_widget %}
    {% if icon is defined %}
        <i class="fa fa-{{ icon }}"></i>
    {% endif %}
    {{ parent() }}
{% endblock %}
```

## 🧠 Concepts Clés
1.  **Autoconfiguration** : Grâce à l'interface `FormTypeExtensionInterface`, Symfony enregistre automatiquement votre extension.
2.  **Héritage** : Si vous étendez `FormType::class` (le type racine), votre extension s'appliquera à **TOUS** les champs (car tous héritent de FormType).

## ⚠️ Points de vigilance (Certification)
*   **Plusieurs extensions** : Plusieurs extensions peuvent s'appliquer au même type. L'ordre d'exécution dépend de la priorité du service (rarement critique).
*   **Surcharge** : Vous ne pouvez pas *supprimer* une option existante, mais vous pouvez changer sa valeur par défaut dans `configureOptions`.

## Ressources
*   [Symfony Docs - Form Type Extensions](https://symfony.com/doc/current/form/create_form_type_extension.html)
