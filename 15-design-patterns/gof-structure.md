# Design Patterns GoF - Structure

Les patterns structurels traitent de la composition des classes et des objets.

## 1. Adapter
**Concept** : Faire collaborer des interfaces incompatibles.

### Application Symfony & Exemple
Vous voulez utiliser une librairie tierce `StripeApi` qui a ses propres méthodes, mais votre application attend une interface générique `PaymentGatewayInterface`.

```php
// Interface attendue par notre application
interface PaymentGatewayInterface {
    public function pay(int $amount): void;
}

// Librairie tierce (incompatible)
class StripeApi {
    public function createCharge(float $dollars): void { ... }
}

// L'Adaptateur
class StripeAdapter implements PaymentGatewayInterface
{
    public function __construct(private StripeApi $stripe) {}

    public function pay(int $amount): void
    {
        // Conversion et délégation
        $this->stripe->createCharge($amount / 100);
    }
}
```

## 2. Bridge
**Concept** : Découpler l'abstraction de l'implémentation.

### Application Symfony & Exemple
Le système de **Monolog** utilise ce principe.
`LoggerInterface` (Abstraction) est utilisé dans votre code.
`HandlerInterface` (Implémentation) gère le stockage réel.

```php
// Abstraction : Logger
$logger->info('Message');

// Implémentation (Configurable sans changer le code qui logue)
// Peut être StreamHandler (Fichier), SyslogHandler, SlackHandler...
monolog:
    handlers:
        main:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
```

## 3. Composite
**Concept** : Traiter un groupe d'objets comme un seul objet (Arbre).

### Application Symfony & Exemple
Les **Formulaires**. Un `Form` peut être un champ ou une collection de champs.

```php
$form = $factory->createBuilder()
    ->add('firstName', TextType::class) // Feuille (Leaf)
    ->add('address', AddressType::class) // Composite (contient rue, ville...)
    ->getForm();

// L'appel se propage à tout l'arbre
$form->handleRequest($request); 
// -> handleRequest sur firstName
// -> handleRequest sur address (qui propage à rue, ville)
```

## 4. Decorator
**Concept** : Ajouter des fonctionnalités dynamiquement sans héritage.

### Application Symfony & Exemple
Ajouter du logging à un service existant via la **Décoration de Service**.

```php
// config/services.yaml
App\Service\DecoratedMailer:
    decorates: App\Service\Mailer
    arguments: ['@.inner']

// PHP
class DecoratedMailer implements MailerInterface
{
    public function __construct(private MailerInterface $inner) {}

    public function send(Email $email): void
    {
        // Ajout de comportement
        $this->log('Sending email...');
        
        // Appel original
        $this->inner->send($email);
    }
}
```

## 5. Facade
**Concept** : Interface simplifiée pour un système complexe.

### Application Symfony & Exemple
L'objet **Client** (`KernelBrowser`) dans les tests.

```php
// La Facade masque la complexité (Kernel, Request, Response, Container)
$client = static::createClient();

// Une ligne simple...
$client->request('GET', '/');

// ...qui déclenche en interne :
// 1. Boot Kernel
// 2. Create Request
// 3. Handle Request
// 4. Return Response
```

## 6. Flyweight (Poids-mouche)
**Concept** : Partager les données communes pour économiser la mémoire.

### Application Symfony & Exemple
Gestion des **Rôles de sécurité**.
Symfony ne crée pas un objet `Role` pour chaque utilisateur. Les chaînes "ROLE_USER" sont stockées et partagées.

Conceptuellement :
```php
class RoleFlyweightFactory {
    private array $roles = [];

    public function getRole(string $name): Role {
        if (!isset($this->roles[$name])) {
            $this->roles[$name] = new Role($name);
        }
        return $this->roles[$name];
    }
}
```

## 7. Proxy
**Concept** : Substitut contrôlant l'accès à un objet.

### Application Symfony & Exemple
Le **Lazy Loading** de Doctrine.

```php
$user = $em->find(User::class, 1);

// $user->getPosts() ne retourne pas une array de Post, mais une PersistentCollection (Proxy).
// La requête SQL n'est PAS encore faite.
$posts = $user->getPosts(); 

// La requête SQL est déclenchée ici (accès réel)
foreach ($posts as $post) { ... }
```
