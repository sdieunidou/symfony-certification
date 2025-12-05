# Design Patterns GoF - Structure

Les patterns structurels traitent de la composition des classes et des objets.

## 1. Adapter
**Concept** : Convertir l'interface d'une classe en une autre interface attendue par le client.

### Application Symfony
Très courant pour l'interopérabilité.
*   **Cache Component** : Adapte Redis, Memcached, ou Filesystem à l'interface standard `CacheInterface`.
*   **Filesystem (Flysystem)** : Adapte le stockage local, AWS S3 ou FTP à une API unique.
*   **Monolog** : Adapte différents canaux de sortie (Fichier, Slack, Email) via des Handlers.

## 2. Bridge
**Concept** : Découpler une abstraction de son implémentation pour qu'elles puissent évoluer indépendamment.

### Application Symfony
Comme vu dans la section Architecture, les **Symfony Bridges** (TwigBridge, DoctrineBridge, MonologBridge) font le lien entre le cœur de Symfony et des librairies tierces. Cela permet à Twig d'évoluer sans casser Symfony, et vice-versa.

## 3. Composite
**Concept** : Composer des objets dans des structures arborescentes (Tout est partie). Traiter les objets individuels et les compositions de manière uniforme.

### Application Symfony
Le composant **Form** est un Composite pur.
*   Un `Form` (parent) contient des enfants (`Form`).
*   Un enfant peut être un champ simple (feuille) ou un sous-formulaire (noeud).
*   La méthode `isValid()` appelée sur le parent cascade automatiquement sur tous les enfants.

## 4. Decorator
**Concept** : Attacher dynamiquement des responsabilités supplémentaires à un objet. Alternative flexible à l'héritage.

### Application Symfony
Le mécanisme de **Décoration de Service** dans le conteneur d'injection de dépendances.
Voir la fiche "Injection de dépendances > Décoration".
Exemple : Le `TraceableEventDispatcher` décore l'`EventDispatcher` natif pour ajouter du logging dans le Profiler, sans modifier le code du dispatcher original.

## 5. Facade
**Concept** : Fournir une interface unifiée et simplifiée à un ensemble d'interfaces d'un sous-système.

### Application Symfony
Symfony évite les "Facades" statiques à la Laravel (`Route::get()`).
Cependant, l'objet **Client** dans les tests fonctionnels (`WebTestCase`) est une façade qui masque la complexité interne du Kernel, de la Request, et du Container pour fournir une API simple (`$client->request()`).

## 6. Flyweight (Poids-mouche)
**Concept** : Utiliser le partage pour supporter un grand nombre d'objets de fine granularité efficacement.

### Application Symfony
Moins visible dans l'API publique, mais utilisé dans le moteur de rendu ou de tokenisation.
Un exemple conceptuel pourrait être la gestion des **Rôles**. Les chaînes "ROLE_USER" sont partagées et réutilisées partout, plutôt que de créer un objet Rôle unique pour chaque utilisateur.

## 7. Proxy
**Concept** : Fournir un substitut ou un placeholder pour un autre objet afin d'en contrôler l'accès.

### Application Symfony
*   **Doctrine Proxies** : Quand vous faites `$post->getAuthor()`, Doctrine ne fait pas la requête SQL tout de suite (Lazy Loading). Il retourne un objet Proxy qui hérite de `User`. La vraie requête SQL n'est exécutée que si vous appelez une méthode sur cet objet (ex: `$user->getName()`).
*   **Lazy Services** : Symfony peut injecter un service "Lazy" (Proxy) qui ne sera instancié réellement que lors de son premier appel de méthode (via `ocramius/proxy-manager` ou le nouveau système natif `var-exporter` lazy ghosts).

