# Principes SOLID

Les cinq principes **SOLID** (Responsabilité unique, Ouvert/Fermé, Substitution de Liskov, Ségrégation des interfaces, Inversion des dépendances) sont des bonnes pratiques de conception objet qui améliorent la qualité du code. Ils visent à produire un code plus **lisible**, **maintenable**, **testable** et **extensible**, tout en réduisant le risque de bogues.

## 1. Principe de Responsabilité Unique (Single Responsibility Principle - SRP)

### Objectifs
*   **Comprendre le concept de responsabilité unique** : savoir qu’une classe doit remplir un seul rôle et n’avoir qu’une seule raison de changer.
*   **Identifier les classes “fourre-tout”** : détecter les endroits où une classe ou un contrôleur gère plusieurs tâches distinctes.
*   **Refactoriser pour clarifier le code** : apprendre à découper une classe multi-rôles en composants plus petits.

### Définition
**« Une classe doit avoir une et une seule raison de changer »**.
Chaque classe (ou module) ne devrait accomplir **qu’une seule fonction bien définie**. Si une classe s’occupe de plusieurs tâches sans rapport, elle viole ce principe.

### Exemple
Imaginons un service utilisateur dans une application Symfony. Une implémentation **à ne pas reproduire** serait une classe unique qui gère tout : validation, persistance, email.

**Mauvaise pratique :**
```php
// Une classe viole SRP en gérant plusieurs responsabilités
class UserService
{
    public function registerUser(array $data) {
        // 1. Valider les données utilisateur
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            throw new Exception("Données invalides");
        }

        // 2. Créer l'entité User et la sauvegarder en base
        $user = new User($data);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // 3. Envoyer un email de confirmation
        $this->mailer->sendWelcomeEmail($user);
    }
}
```

Dans cet exemple, `UserService` s’occupe *à la fois* de la validation, de la logique métier et de l’envoi d’email. Une modification dans le système d'email obligerait à modifier cette classe métier.

**Solution :** Diviser les responsabilités.

```php
// Bonne pratique : classes séparées par responsabilité
class UserService
{
    public function __construct(
        private UserValidator $validator,
        private UserRepository $userRepo,
        private EmailNotifier $notifier
    ) {}

    public function registerUser(array $data) {
        $this->validator->valider($data);
        $user = $this->userRepo->create($data);
        $this->notifier->envoyerEmailBienvenue($user);
    }
}
```
Maintenant, `UserService` est un orchestrateur. Chaque classe a un rôle distinct.

### Pièges à éviter
*   **Les classes “God Object”** : Des classes au nom générique (`Manager`, `Helper`) qui contiennent tout et n'importe quoi.
*   **Les contrôleurs trop complexes** : Dans Symfony, un contrôleur doit se limiter à orchestrer les services et retourner une réponse HTTP.
*   **Trop de raisons de changer** : Si un changement de format de fichier ET un changement de base de données vous obligent à modifier la même classe, elle viole SRP.

---

## 2. Principe Ouvert/Fermé (Open/Closed Principle - OCP)

### Objectifs
*   **Permettre l’extensibilité sans altérer l’existant**.
*   **Réduire les régressions** : moins on modifie du code testé, moins on risque de bugs.
*   **Utiliser le polymorphisme** : interfaces, classes abstraites, composition.

### Définition
**« Les entités doivent être ouvertes à l’extension, mais fermées à la modification. »**
On doit pouvoir ajouter de nouvelles fonctionnalités sans changer le code source déjà en place.

### Exemple
**Scénario :** Un `PaymentService` gère les paiements.

**Mauvaise approche (viole OCP) :**
```php
class PaymentService
{
    public function processPayment(Order $order, string $method) {
        if ($method === 'card') {
            // Traitement paiement par carte
        } elseif ($method === 'paypal') {
            // Traitement paiement PayPal
        }
        // Si on veut ajouter Bitcoin, on doit MODIFIER cette classe
    }
}
```

**Solution :** Appliquer le polymorphisme via une interface.

```php
interface PaymentMethodInterface {
    public function pay(Order $order): bool;
}

class CreditCardPayment implements PaymentMethodInterface {
    public function pay(Order $order): bool { /* ... */ }
}

class PaypalPayment implements PaymentMethodInterface {
    public function pay(Order $order): bool { /* ... */ }
}
```

Le service principal devient agnostique du moyen de paiement :

```php
class PaymentService
{
    public function __construct(private iterable $paymentMethods) { }

    public function processPayment(Order $order, string $methodName) {
        foreach ($this->paymentMethods as $methodService) {
            // On trouve le bon service (ex: via nom de classe ou méthode supports())
            if ($methodService instanceof PaymentMethodInterface 
                && $methodService::class === $methodName) {
                return $methodService->pay($order);
            }
        }
        throw new \InvalidArgumentException("Méthode de paiement inconnue.");
    }
}
```
Pour ajouter "Bitcoin", il suffit de créer une classe `BitcoinPayment`. `PaymentService` ne change pas.

### Pièges à éviter
*   **Les conditions sur types (`if/elseif/switch`)** : Souvent le symptôme d'un manque de polymorphisme.
*   **Modifier du code stable** : Évitez de rouvrir des classes éprouvées pour de nouveaux besoins.

---

## 3. Principe de Substitution de Liskov (Liskov Substitution Principle - LSP)

### Objectifs
*   **Assurer la cohérence de l’héritage**.
*   **Concevoir des sous-classes sans “effets de bord”**.

### Définition
**« Les objets d’une classe dérivée doivent pouvoir remplacer ceux de leur classe mère sans altérer le bon fonctionnement du programme. »**
Toute propriété vraie pour la classe mère doit le rester pour la classe fille.

### Exemple
Considérons une classe `Article`.

```php
class Article {
    protected string $title;
    public function __construct(string $title) { $this->title = $title; }
    
    public function getTitle(): string {
        return $this->title;
    }
}
```

Une sous-classe `ArticleEnVedette` décide de changer le comportement :

```php
class ArticleEnVedette extends Article {
    public function getTitle(): string {
        // Violation LSP : On modifie la donnée retournée !
        return "En vedette : ". parent::getTitle();
    }
}
```

**Le problème :** Si un service utilise `strtoupper($article->getTitle())` pour normaliser le titre en base de données, il va corrompre le titre de l'article en vedette en y incluant le préfixe "En vedette :". L'enfant ne se comporte pas comme le parent.

**Solution :** Ne pas changer le comportement de base. Créer une méthode spécifique `getDisplayTitle()` ou utiliser un décorateur/composition plutôt que l'héritage.

### Pièges à éviter
*   **Surcharger en restreignant** : La sous-classe ne doit pas être plus stricte sur les arguments acceptés.
*   **Exceptions non prévues** : Si le parent ne lance pas d'exception, l'enfant ne devrait pas le faire pour le même cas d'usage.
*   **L'exemple Carré/Rectangle** : Un Carré n'est pas un Rectangle en programmation (si on change la largeur d'un carré, sa hauteur doit changer, ce qui viole le comportement indépendant attendu d'un rectangle).

---

## 4. Principe de Ségrégation des Interfaces (Interface Segregation Principle - ISP)

### Objectifs
*   **Éviter les interfaces “bloat”** (obèses).
*   **Réduire les dépendances inutiles**.

### Définition
**« Une classe ne doit pas être obligée d’implémenter des méthodes qu’elle n’utilisera jamais. »**
Il vaut mieux plusieurs petites interfaces spécifiques qu’une seule grande interface généraliste.

### Exemple
On souhaite exporter des données.

**Mauvaise approche :**
```php
interface DataExportInterface {
    public function exportToCSV(array $data): string;
    public function exportToJSON(array $data): string;
    public function exportToXML(array $data): string;
}
```
Si une classe ne sait faire que du JSON, elle est obligée d'implémenter `exportToXML` (souvent avec une méthode vide ou une exception), ce qui est sale.

**Solution :** Découper les interfaces.

```php
interface CsvExportable {
    function exportCsv(array $data): string;
}

interface JsonExportable {
    function exportJson(array $data): string;
}
```

**Exemple Symfony :** Le composant Routing sépare `UrlGeneratorInterface` (générer des URL) et `UrlMatcherInterface` (analyser des URL).

### Bonnes pratiques
*   **Interfaces minimalistes** : Une interface = un rôle.
*   **Composition** : Une interface peut en étendre plusieurs petites.

---

## 5. Principe d’Inversion des Dépendances (Dependency Inversion Principle - DIP)

### Objectifs
*   **Découpler les niveaux de l’architecture**.
*   **Programmer contre des abstractions**.

### Définition
**« Les modules de haut niveau ne doivent pas dépendre des modules de bas niveau. Les deux doivent dépendre d’abstractions. »**
Il faut dépendre des **interfaces**, pas des implémentations concrètes.

### Exemple
Un `NotificationManager` qui instancie lui-même ses services.

**Mauvaise approche :**
```php
class NotificationManager
{
    public function send(string $type, Message $message) {
        if ($type === 'email') {
            // Violation DIP : Dépendance directe à la classe concrète (new)
            $mailer = new EmailService(); 
            $mailer->sendEmail($message);
        }
    }
}
```

**Solution :** Injection de dépendance via une interface.

```php
interface NotificationServiceInterface {
    public function send(Message $message);
}

class NotificationManager
{
    /** @var NotificationServiceInterface[] */
    private array $services;

    // On injecte une collection de services respectant l'interface
    public function __construct(iterable $services) {
        foreach ($services as $svc) {
            $this->services[get_class($svc)] = $svc;
        }
    }
    
    // ...
}
```

Le `NotificationManager` ne connaît plus `EmailService`, il ne connaît que `NotificationServiceInterface`. On peut changer le système de mail ou ajouter des SMS sans toucher au Manager.

### Bonnes pratiques
*   **Injection par constructeur** : Le standard dans Symfony.
*   **Type-hinting sur interfaces** : Ne typez pas `EntityManager`, typez `EntityManagerInterface`.
*   **Tests** : Cela permet de mocker facilement les dépendances (ex: injecter un `FakeMailer` qui n'envoie rien).

---

## Ressources
*   [Principes SOLID : Le guide – Alex so yes](https://alexsoyes.com/solid/)
*   [Symfony Blog: SOLID principles](https://symfony.com/blog/new-rule-emphasizing-the-importance-of-solid-principles)
*   [AFSY - Votre code est STUPID? Rendez le SOLID!](https://afsy.fr/avent/2013/02-principes-stupid-solid-poo)
