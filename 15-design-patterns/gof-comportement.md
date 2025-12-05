# Design Patterns GoF - Comportement

Ces patterns s'occupent de la communication entre les objets.

## 1. Chain of Responsibility
**Concept** : Passer une requête le long d'une chaîne de gestionnaires.

### Application Symfony
*   **HttpKernel** : Le noyau gère la requête à travers une série d'événements (`kernel.request`, `kernel.response`...).
*   **Security Firewalls** : Le système de sécurité vérifie les firewalls les uns après les autres jusqu'à trouver celui qui matche l'URL.
*   **Messenger Middlewares** : Lors du dispatch d'un message, il traverse une chaîne de middlewares (Logging, Validation, Transaction) avant d'arriver au Handler.

## 2. Command
**Concept** : Encapsuler une requête comme un objet.

### Application Symfony
Le composant **Console** est l'implémentation littérale de ce pattern. Chaque classe `Command` encapsule la logique d'une tâche CLI.
Le pattern **CQRS** (Command Query Responsibility Segregation), souvent implémenté avec Messenger, utilise aussi des objets "Command" pour représenter les intentions de l'utilisateur.

## 3. Interpreter
**Concept** : Définir une représentation grammaticale pour un langage et un interpréteur.

### Application Symfony
*   **ExpressionLanguage** : Permet d'évaluer des expressions dynamiques (`user.isSuperAdmin() and trusted_ip`).
*   **Route Compiler** : Interprète les patterns de route (`/blog/{slug}`) en Regex.

## 4. Iterator
**Concept** : Accéder séquentiellement aux éléments d'une collection.

### Application Symfony
*   **Finder** : Permet d'itérer sur des fichiers (`foreach ($finder as $file)`).
*   **Lazy Collections** : Doctrine retourne des `ArrayCollection` ou `PersistentCollection` qui sont itérables.

## 5. Mediator
**Concept** : Définir un objet qui encapsule la façon dont un ensemble d'objets interagissent.

### Application Symfony
L'**EventDispatcher** est un médiateur.
Au lieu que le service `OrderService` appelle directement `MailerService` et `StockService` (couplage fort), il notifie le Médiateur (`dispatch(OrderCreatedEvent)`). Le médiateur distribue ensuite aux services concernés.

## 6. Memento
**Concept** : Capturer et externaliser l'état interne d'un objet pour pouvoir le restaurer plus tard.

### Application Symfony
Moins explicite. On le retrouve dans la gestion des **Transactions Doctrine** (UnitOfWork conserve l'état original des entités pour calculer le changeset lors du flush). Si on fait un rollback, l'état est restauré (conceptuellement).

## 7. Observer
**Concept** : Dépendance un-à-plusieurs. Quand un objet change d'état, tous ses dépendants sont notifiés.

### Application Symfony
Les **Event Subscribers** et **Event Listeners** sont des observateurs. Ils "observent" le Kernel ou d'autres services via le Dispatcher.

## 8. State
**Concept** : Permettre à un objet de changer de comportement quand son état interne change.

### Application Symfony
Le composant **Workflow** implémente ce pattern (ou plutôt une Machine à États finis). Il gère les transitions d'un objet (ex: Article) d'un état à un autre (`draft` -> `review` -> `published`) et empêche les transitions invalides.

## 9. Strategy
**Concept** : Définir une famille d'algorithmes, les encapsuler, et les rendre interchangeables.

### Application Symfony
*   **PasswordHasher** : On peut changer la stratégie de hachage (Sodium, Bcrypt, Auto) par configuration, sans changer le code qui appelle `hashPassword()`.
*   **Serializer** : Les `Normalizers` et `Encoders` sont des stratégies choisies selon le format (JSON, XML, CSV).
*   **ArgumentResolver** : Le Kernel choisit la bonne stratégie pour injecter les arguments du contrôleur.

## 10. Template Method
**Concept** : Définir le squelette d'un algorithme dans une opération, en déléguant certaines étapes aux sous-classes.

### Application Symfony
La classe `Command` de la Console.
La méthode `run()` (définie dans le parent) appelle `configure()`, `interact()`, `initialize()` et enfin `execute()`. Votre classe fille implémente ces méthodes spécifiques, mais c'est le parent qui orchestre l'appel global.

## 11. Visitor
**Concept** : Séparer un algorithme de la structure d'objets sur laquelle il opère.

### Application Symfony
*   **Validator** : Le service de validation "visite" l'objet et ses propriétés pour appliquer les contraintes.
*   **Serializer** : Visite l'objet pour le transformer en tableau.
*   **Twig NodeTraverser** : Visite l'arbre syntaxique (AST) des templates pour les compiler en PHP.

