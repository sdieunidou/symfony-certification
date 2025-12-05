# Principes SOLID

## 1. Single Responsibility Principle (SRP)
**"Une classe ne doit avoir qu'une seule raison de changer."**

### Exemple Symfony
Séparer la logique métier du contrôleur.

**❌ Mauvais (Controlleur "Dieu")**
```php
class OrderController extends AbstractController {
    public function create(Request $request) {
        // 1. Validation
        if (!$request->get('product')) { throw ... }
        // 2. Calcul métier
        $total = $price * 1.20;
        // 3. Persistance
        $this->em->persist($order);
        $this->em->flush();
        // 4. Notification
        $this->mailer->send(...);
        
        return $this->json(['status' => 'ok']);
    }
}
```

**✅ Bon (Délégation)**
```php
class OrderController extends AbstractController {
    public function create(Request $request, OrderManager $manager) {
        // Le contrôleur ne gère que HTTP
        $manager->createOrderFromRequest($request);
        return $this->json(['status' => 'ok']);
    }
}

class OrderManager {
    // Le service contient le métier
    public function createOrderFromRequest(...) { ... }
}
```

## 2. Open/Closed Principle (OCP)
**"Ouvert à l'extension, fermé à la modification."**

### Exemple Symfony
Utiliser les **Événements** pour étendre une fonctionnalité sans modifier le code source.

```php
class OrderService {
    public function create() {
        $order = new Order();
        // ... save ...
        
        // Point d'extension (Ouvert)
        $this->dispatcher->dispatch(new OrderCreatedEvent($order));
    }
}

// Extension (Nouveau fichier, pas de modif de OrderService)
class EmailSubscriber implements EventSubscriberInterface {
    public function onOrderCreated(OrderCreatedEvent $event) {
        // Envoi email
    }
}
```

## 3. Liskov Substitution Principle (LSP)
**"Une sous-classe doit pouvoir remplacer sa classe parente sans casser l'application."**

### Exemple Symfony
Si vous remplacez un service par une autre implémentation, cela doit fonctionner transparentement.

```php
interface StorageInterface {
    public function save(string $data): void;
}

class FileStorage implements StorageInterface {
    public function save(string $data): void { /* écriture fichier */ }
}

// ✅ Respect LSP
class S3Storage implements StorageInterface {
    public function save(string $data): void { /* upload AWS */ }
}

// ❌ Violation LSP
class ReadOnlyStorage implements StorageInterface {
    public function save(string $data): void {
        // Change le comportement attendu (ne sauvegarde pas ou lance une exception inattendue)
        throw new \Exception("Not supported"); 
    }
}
```

## 4. Interface Segregation Principle (ISP)
**"Pas d'interfaces obèses. Préférez plusieurs interfaces spécifiques."**

### Exemple Symfony
La séparation des interfaces User.

```php
// Au lieu d'une interface UserInterface géante qui force à implémenter getPassword()...
// ...ce qui est gênant pour un utilisateur API authentifié par Token (sans password).

// Symfony sépare :
interface UserInterface { 
    public function getRoles(): array; 
    public function getUserIdentifier(): string;
}

interface PasswordAuthenticatedUserInterface { 
    public function getPassword(): ?string; 
}

// Mon utilisateur API n'implémente que la première.
class ApiUser implements UserInterface { ... }
```

## 5. Dependency Inversion Principle (DIP)
**"Dépendre des abstractions, pas des implémentations."**

### Exemple Symfony
Injection de dépendance dans le constructeur.

**❌ Mauvais (Dépendance concrète)**
```php
class ReportGenerator {
    public function __construct() {
        // Couplage fort : Impossible de changer pour 'Dompdf' ou de mocker pour les tests
        $this->pdfEngine = new WkHtmlToPdf(); 
    }
}
```

**✅ Bon (Abstraction)**
```php
class ReportGenerator {
    // On demande une Interface (Contrat)
    public function __construct(private PdfGeneratorInterface $pdfEngine) {}
}

// Config services.yaml : on dit quelle implémentation utiliser
// services:
//    App\Service\PdfGeneratorInterface: '@App\Service\WkHtmlToPdf'
```
