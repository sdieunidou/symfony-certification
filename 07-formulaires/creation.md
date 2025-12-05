# Création de Formulaires

## Concept clé
Symfony sépare la définition du formulaire de son rendu. On définit une classe "Type" (le plan) qui construit le formulaire, puis on l'instancie dans le contrôleur.

## Application dans Symfony 7.0

### 1. Créer la classe (Recommandé)
```php
// src/Form/TaskType.php
namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('task', TextType::class)
            ->add('dueDate', null, ['widget' => 'single_text']) // null = devine le type
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
```

### 2. Instancier dans le contrôleur
```php
public function new(): Response
{
    $task = new Task();
    // createForm est un helper de AbstractController
    $form = $this->createForm(TaskType::class, $task);
    
    return $this->render('task/new.html.twig', [
        'form' => $form, // On passe la FormView, pas l'objet Form
    ]);
}
```

## Points de vigilance (Certification)
*   **data_class** : Il est crucial de définir `data_class` dans `configureOptions` si votre formulaire est lié à un objet (Entité ou DTO).
*   **createFormBuilder** : On peut créer des formulaires "à la volée" sans classe dans le contrôleur (`$this->createFormBuilder($task)...`), mais c'est moins réutilisable.
*   **Form Factory** : En interne, `createForm` utilise le service `FormFactory`.

## Ressources
*   [Symfony Docs - Forms](https://symfony.com/doc/current/forms.html)

