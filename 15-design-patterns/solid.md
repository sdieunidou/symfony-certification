# Principes SOLID

## Concept clé
SOLID est un acronyme représentant 5 principes de conception orientée objet destinés à rendre le code plus maintenable, flexible et évolutif.

## 1. Single Responsibility Principle (SRP)
**"Une classe ne doit avoir qu'une seule raison de changer."**

### Exemple Symfony
Ne mettez pas de logique métier, d'envoi d'email et de requête SQL dans un Contrôleur.
*   **Mauvais** : Un contrôleur qui valide la donnée, crée l'entité, appelle le mailer et flushe en base.
*   **Bon** : Un contrôleur qui appelle un `UserRegistrationService`. Le service délègue l'email à un `MailerService`.

```php
// Violation SRP
class OrderController extends AbstractController {
    public function create() {
        // Validation...
        // Calcul prix...
        // Sauvegarde DB...
        // Envoi email...
        // Génération PDF...
    }
}

// Respect SRP
class OrderService {
    public function createOrder(OrderDto $dto) {
        // Orchestre la création
    }
}
```

## 2. Open/Closed Principle (OCP)
**"Les entités logicielles doivent être ouvertes à l'extension, mais fermées à la modification."**

### Exemple Symfony : Event Dispatcher & Voters
Vous voulez modifier le comportement lors de la création d'une commande sans toucher au code du cœur ?
Utilisez les **Événements**.
Le code qui dispatche l'événement est "fermé" (on ne le touche pas), mais "ouvert" via l'ajout de nouveaux Listeners.

```php
// Core (Fermé)
$this->dispatcher->dispatch(new OrderCreatedEvent($order));

// Extension (Ouvert)
class SendInvoiceSubscriber implements EventSubscriberInterface {
    // J'ajoute une fonctionnalité sans toucher à la classe Commande
}
```

Les **Voters** de sécurité sont aussi un excellent exemple : on ajoute une règle de sécurité en créant une nouvelle classe Voter, sans modifier le `AuthorizationChecker`.

## 3. Liskov Substitution Principle (LSP)
**"Les objets d'un programme doivent pouvoir être remplacés par des instances de leurs sous-types sans que cela n'altère le fonctionnement correct du programme."**

### Exemple Symfony
Si vous type-hintez une interface, n'importe quelle implémentation doit fonctionner.
Si vous étendez une classe, ne changez pas les pré-conditions (paramètres) ou post-conditions (type de retour).

```php
interface StorageInterface {
    public function save(string $data): void;
}

class FileStorage implements StorageInterface {
    public function save(string $data): void { /* ... */ }
}

class ReadOnlyStorage implements StorageInterface {
    public function save(string $data): void {
        // Violation LSP ! Si le code client attend que ça sauvegarde, 
        // lancer une exception change le contrat.
        throw new \Exception("Cannot save"); 
    }
}
```

## 4. Interface Segregation Principle (ISP)
**"Mieux vaut plusieurs interfaces spécifiques qu'une seule interface générale."**

### Exemple Symfony : UserInterface
Avant, `UserInterface` obligeait parfois à implémenter des méthodes inutiles pour certains types d'utilisateurs (ex: API users vs Admin users).
Symfony découpe ses interfaces : `PasswordAuthenticatedUserInterface` est séparée de `UserInterface`. Si votre utilisateur n'a pas de mot de passe (authentification via Google), vous n'avez pas à implémenter `getPassword()`.

## 5. Dependency Inversion Principle (DIP)
**"Les modules de haut niveau ne doivent pas dépendre des modules de bas niveau. Les deux doivent dépendre d'abstractions."**

### Exemple Symfony : Injection de Dépendances
C'est le cœur de Symfony. On n'instancie jamais (`new`) un service dans un autre. On demande l'interface.

*   **Mauvais** : Dépendance forte vers une implémentation concrète.
    ```php
    class UserManager {
        public function __construct() {
            $this->mailer = new GmailMailer(); // Couplage fort
        }
    }
    ```

*   **Bon** : Dépendance vers une abstraction.
    ```php
    class UserManager {
        public function __construct(MailerInterface $mailer) {
            $this->mailer = $mailer; // Couplage faible via DIP
        }
    }
    ```

## Ressources
*   [Wikipedia - SOLID](https://fr.wikipedia.org/wiki/SOLID_(informatique))

