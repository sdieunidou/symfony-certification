# Création de Formulaires

## Concept clé
Le composant Form de Symfony permet de construire des formulaires orientés objet.
Il dissocie la **définition** (Classe PHP), le **traitement** (Mapping Objet, Validation) et le **rendu** (Twig).

## 1. Créer une classe de formulaire (`AbstractType`)
C'est la méthode recommandée (réutilisable, testable).

```php
// src/Form/TaskType.php
namespace App\Form;

use App\Entity\Task;
use App\Enum\PriorityEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de la tâche',
                'attr' => ['placeholder' => 'Acheter du pain'],
            ])
            ->add('priority', EnumType::class, [
                'class' => PriorityEnum::class,
            ])
            ->add('dueDate', DateType::class, [
                'widget' => 'single_text', // Input type="date" HTML5
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class, // Lie le formulaire à l'entité Task
            // 'method' => 'GET', // Optionnel (défaut POST)
        ]);
    }
}
```

## 2. Instancier dans le Contrôleur
L'`AbstractController` fournit la méthode `createForm`.

```php
#[Route('/new', name: 'task_new')]
public function new(Request $request, EntityManagerInterface $em): Response
{
    $task = new Task();
    $form = $this->createForm(TaskType::class, $task);

    // Traitement (voir processing.md)
    // ...

    return $this->render('task/new.html.twig', [
        'form' => $form, // On passe l'objet FormView
    ]);
}
```

## Formulaires sans classe ("On-the-fly")
Pour des formulaires simples (ex: suppression), on peut utiliser le `FormBuilder` directement dans le contrôleur.

```php
$form = $this->createFormBuilder($task)
    ->add('title', TextType::class)
    ->getForm();
```

## 🧠 Concepts Clés
1.  **Data Class** : L'option `data_class` est cruciale. Elle dit à Symfony "Ce formulaire manipule une instance de `App\Entity\Task`". Symfony utilisera le *PropertyAccessor* pour lire (`getTitle`) et écrire (`setTitle`) les données.
2.  **Form Registry** : Symfony charge les types via le conteneur de services. Vous pouvez injecter des dépendances (ex: `Security`) dans le constructeur de votre `TaskType`.

## ⚠️ Points de vigilance (Certification)
*   **DTO** : Il est souvent recommandé d'utiliser des DTOs (Data Transfer Objects) spécifiques au formulaire plutôt que les entités Doctrine directement, pour éviter de coupler la structure de la DB à l'interface utilisateur.
*   **Guessing** : Si vous omettez le type (`->add('title')`), Symfony essaie de deviner le type de champ en introspectant les métadonnées Doctrine et Assert (Validator). C'est magique mais moins explicite.

## Ressources
*   [Symfony Docs - Forms](https://symfony.com/doc/current/forms.html)
