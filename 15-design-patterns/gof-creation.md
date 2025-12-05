# Design Patterns GoF - Création

Les patterns de création abstraient le processus d'instanciation.

## 1. Singleton
**Concept** : Une seule instance d'une classe.

### Application Symfony & Exemple
Dans Symfony, c'est le **Conteneur de Services** qui joue ce rôle.
Vous ne devez **pas** créer de classe avec `private static $instance`. Vous devez configurer le service pour être partagé (ce qui est le défaut).

```yaml
# services.yaml
services:
    # Par défaut, ce service est un singleton au sein du conteneur
    # (Une seule instance créée et réutilisée partout)
    App\Service\MyHeavyService: ~
```

Si vous avez besoin d'une nouvelle instance à chaque fois (pattern Prototype), utilisez `shared: false`.

## 2. Factory Method
**Concept** : Déléguer l'instanciation à une méthode.

### Application Symfony & Exemple
Le **Contrôleur** agit souvent comme une factory pour les Formulaires ou les Réponses.

```php
class PostController extends AbstractController
{
    public function new(): Response
    {
        $post = new Post();
        
        // createForm() est une Factory Method
        // Elle cache la complexité de création du Form (FormBuilder, EventListeners, etc.)
        $form = $this->createForm(PostType::class, $post);
        
        return $this->render('...');
    }
}
```

## 3. Abstract Factory
**Concept** : Créer des familles d'objets apparentés.

### Application Symfony & Exemple
Imaginons un système de notification qui supporte Email et SMS.

```php
// Abstract Factory Interface
interface NotificationFactoryInterface {
    public function createMessage(): MessageInterface;
    public function createTransport(): TransportInterface;
}

// Concrete Factory 1
class EmailNotificationFactory implements NotificationFactoryInterface {
    public function createMessage(): MessageInterface { return new EmailMessage(); }
    public function createTransport(): TransportInterface { return new SmtpTransport(); }
}

// Concrete Factory 2
class SmsNotificationFactory implements NotificationFactoryInterface {
    public function createMessage(): MessageInterface { return new SmsMessage(); }
    public function createTransport(): TransportInterface { return new ApiTransport(); }
}
```

## 4. Builder
**Concept** : Construction étape par étape d'un objet complexe.

### Application Symfony & Exemple
Le **FormBuilder** est l'exemple parfait.

```php
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // On construit l'objet Form étape par étape
        $builder
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class);
            
        // Le "Director" est le FormFactory qui appellera $builder->getForm() à la fin
    }
}
```

## 5. Prototype
**Concept** : Créer un nouvel objet en clonant un existant.

### Application Symfony & Exemple
Utile pour dupliquer une entité avec ses relations.

```php
class Project
{
    private $tasks;
    private $name;

    public function __clone()
    {
        // Deep copy : on force le clonage des objets liés
        // Sinon PHP fait une "Shallow copy" (copie les références)
        if ($this->tasks) {
            $this->tasks = clone $this->tasks;
        }
        $this->name = 'Copie de ' . $this->name;
    }
}

// Usage
$project = $repo->find(1);
$newProject = clone $project; // Déclenche __clone()
$em->persist($newProject);
```
