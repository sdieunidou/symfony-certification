# Design Patterns GoF - Comportement

Ces patterns s'occupent de la communication entre les objets.

## 1. Chain of Responsibility
**Concept** : Passer une requête le long d'une chaîne de gestionnaires. Chaque gestionnaire décide soit de traiter la requête, soit de la passer au suivant.

### Application Symfony & Exemple
L'exemple le plus parlant est le système de **Middlewares** dans le composant Messenger.

```php
// config/packages/messenger.yaml
framework:
    messenger:
        buses:
            command_bus:
                middleware:
                    - 'App\Middleware\LoggingMiddleware'
                    - 'App\Middleware\AuditMiddleware'
                    - 'doctrine_transaction'
```

```php
// Implementation d'un maillon de la chaîne
class LoggingMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // 1. Avant (Pre-processing)
        $this->logger->info('Handling message...');

        // 2. Passe au suivant
        $result = $stack->next()->handle($envelope, $stack);

        // 3. Après (Post-processing)
        $this->logger->info('Message handled.');

        return $result;
    }
}
```

## 2. Command
**Concept** : Encapsuler une requête comme un objet.

### Application Symfony & Exemple
Le pattern **CQRS** (Command Query Responsibility Segregation) avec Messenger. La "Commande" est un simple DTO (Data Transfer Object).

```php
// La Commande (L'intention)
class CreateUserCommand
{
    public function __construct(
        public readonly string $email,
        public readonly string $password
    ) {}
}

// Le Handler (L'exécution)
#[AsMessageHandler]
class CreateUserHandler
{
    public function __invoke(CreateUserCommand $command): void
    {
        // Logique métier...
    }
}

// L'appelant (Invoker)
$bus->dispatch(new CreateUserCommand('test@test.com', 'secret'));
```

## 3. Interpreter
**Concept** : Définir une représentation grammaticale pour un langage et un interpréteur.

### Application Symfony & Exemple
Le composant **ExpressionLanguage** permet d'exécuter de la logique dynamique (souvent stockée en DB ou config).

```php
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

$language = new ExpressionLanguage();

// Définition du contexte
$vars = [
    'user' => $user,
    'cart' => $cart
];

// L'expression (Le langage)
$rule = 'user.isVip() and cart.total > 100';

// L'interpréteur
if ($language->evaluate($rule, $vars)) {
    // Appliquer remise...
}
```

## 4. Iterator
**Concept** : Accéder séquentiellement aux éléments d'une collection sans exposer sa structure interne.

### Application Symfony & Exemple
Le composant **Finder** retourne un itérateur.

```php
use Symfony\Component\Finder\Finder;

$finder = new Finder();
$finder->in(__DIR__)->files()->name('*.php');

// Finder implémente \IteratorAggregate
// On ne sait pas comment il parcours le disque (récursif, plat...), on juste itère.
foreach ($finder as $file) {
    // $file est un SplFileInfo
    echo $file->getRealPath();
}
```

## 5. Mediator
**Concept** : Définir un objet qui coordonne les interactions entre d'autres objets (Collègues) pour réduire leur couplage.

### Application Symfony & Exemple
L'**EventDispatcher** est le médiateur.

```php
// Sans Médiateur (Couplage fort)
class OrderService {
    public function create() {
        $this->db->save();
        $this->mailer->send(); // Dépendance directe
        $this->stock->update(); // Dépendance directe
    }
}

// Avec Médiateur (Découplage)
class OrderService {
    public function __construct(private EventDispatcherInterface $dispatcher) {}
    
    public function create() {
        // ... save order ...
        
        // On notifie le médiateur. On ne sait pas qui écoute.
        $this->dispatcher->dispatch(new OrderCreatedEvent($order));
    }
}
```

## 6. Memento
**Concept** : Capturer l'état interne d'un objet pour le restaurer plus tard.

### Application Symfony & Exemple
Doctrine utilise ce pattern pour calculer les changements (UnitOfWork).

```php
// Conceptuel : Restauration d'état
$entity = $repo->find(1);
$originalData = $em->getUnitOfWork()->getOriginalEntityData($entity);

$entity->setName('New Name');

// Si on voulait "Annuler" (Rollback manuel)
if ($error) {
    $entity->setName($originalData['name']);
}
```

## 7. Observer
**Concept** : Quand un objet change d'état, ses abonnés sont notifiés.

### Application Symfony & Exemple
Les **Event Subscribers**.

```php
class UserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        // Je m'abonne à l'événement "user.registered"
        return [
            UserRegisteredEvent::class => 'onUserRegistered',
        ];
    }

    public function onUserRegistered(UserRegisteredEvent $event): void
    {
        // Réaction au changement d'état
        $this->sendWelcomeEmail($event->getUser());
    }
}
```

## 8. State
**Concept** : Permettre à un objet de changer de comportement quand son état interne change.

### Application Symfony & Exemple
Le composant **Workflow**.

```yaml
# config/packages/workflow.yaml
framework:
    workflows:
        article_publishing:
            type: 'state_machine'
            supports: ['App\Entity\Article']
            places: ['draft', 'review', 'published']
            transitions:
                to_review:
                    from: 'draft'
                    to: 'review'
```

```php
public function publish(Article $article, WorkflowInterface $workflow)
{
    // L'objet Workflow gère la logique d'état
    if ($workflow->can($article, 'to_review')) {
        $workflow->apply($article, 'to_review');
    }
}
```

## 9. Strategy
**Concept** : Rendre des algorithmes interchangeables.

### Application Symfony & Exemple
Calculer des frais de port selon le transporteur.

```php
// Interface de la stratégie
interface ShippingStrategyInterface {
    public function calculateCost(Order $order): float;
}

// Stratégies concrètes
class UpsStrategy implements ShippingStrategyInterface { ... }
class FedexStrategy implements ShippingStrategyInterface { ... }

// Context (Le service qui utilise la stratégie)
class ShippingCalculator
{
    public function __construct(
        // On injecte un itérateur de stratégies (pattern courant dans Symfony)
        #[TaggedIterator('app.shipping_strategy')] 
        private iterable $strategies
    ) {}

    public function calculate(Order $order): float
    {
        // On choisit la bonne stratégie à l'exécution
        foreach ($this->strategies as $strategy) {
            if ($strategy->supports($order)) {
                return $strategy->calculateCost($order);
            }
        }
    }
}
```

## 10. Template Method
**Concept** : Définir le squelette d'un algorithme dans une classe mère et laisser les filles implémenter les étapes spécifiques.

### Application Symfony & Exemple
Créer une commande Console personnalisée.

```php
// Symfony\Component\Console\Command\Command définit la méthode run() (Le template)
// qui appelle initialize(), interact(), execute()...

class ImportUsersCommand extends Command
{
    // On implémente juste les étapes spécifiques
    protected function configure(): void { ... }
    protected function execute(InputInterface $input, OutputInterface $output): int { ... }
}
```

## 11. Visitor
**Concept** : Séparer un algorithme de la structure d'objets.

### Application Symfony & Exemple
Le **Serializer**. Il parcours un graphe d'objets complexe (User -> Posts -> Comments) et applique une transformation (Normalisation) à chaque nœud.

```php
// Le Serializer est le visiteur
$json = $serializer->serialize($user, 'json', ['groups' => 'api']);

// En interne, il visite chaque propriété :
// Visite User -> transform name
// Visite User -> transform posts -> Visite Post 1 -> transform title...
```
