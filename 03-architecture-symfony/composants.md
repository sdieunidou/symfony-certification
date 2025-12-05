# Composants (Components)

## Concept clé
Symfony n'est pas un bloc monolithique. C'est une collection de **+30 composants PHP découplés**, testés et réutilisables.
Le "Framework Symfony" est en réalité une application qui assemble ces composants via le composant `HttpKernel` et `FrameworkBundle`.

Vous pouvez utiliser `symfony/console` dans une application Laravel, ou `symfony/yaml` dans un script procédural.

## Liste des Composants Majeurs (Catégorisation)

### Cœur (Low Level)
*   **HttpKernel** : Le chef d'orchestre. Gère le cycle Request -> Response.
*   **HttpFoundation** : Abstraction objet de HTTP (Request, Response, Session, File).
*   **DependencyInjection** : Le conteneur de services (ContainerBuilder).
*   **EventDispatcher** : Implémentation Mediator/Observer.
*   **Config** : Chargement et validation de configuration (YAML, XML).

### Fonctionnalités Clés
*   **Routing** : Mappage URL -> Contrôleur.
*   **Form** : Création, rendu et validation de formulaires HTML.
*   **Validator** : Validation de données objets (JSR-303 like).
*   **Security** : Authentification et Autorisation (complexe, divisé en sous-modules).
*   **Console** : Création de commandes CLI (`bin/console`).
*   **Serializer** : Transformation Objet <-> Format (JSON, XML, CSV).

### Utilitaires
*   **Filesystem** : Manipulation de fichiers (mkdir, touch, copy).
*   **Finder** : Recherche de fichiers fluide (`Finder::create()->in(__DIR__)->name('*.php')`).
*   **Dotenv** : Parsing des fichiers `.env`.
*   **String** : Manipulation orientée objet de chaînes (Unicode aware).
*   **Process** : Exécution de sous-processus système.

### Nouveautés & Écosystème (High Level)
Ces paquets ne sont parfois pas des "Components" au sens strict (namespace) mais font partie intégrante de l'offre :
*   **HttpClient** : Client HTTP (remplace Guzzle).
*   **Mailer** : Envoi de mails (remplace SwiftMailer).
*   **Messenger** : Bus de messages et files d'attente (Queue).
*   **Notifier** : Notifications multicanales (SMS, Slack, Telegram).
*   **Scheduler** (7.0) : Planification de tâches (Cron-like).
*   **AssetMapper** (7.0) : Gestion d'assets sans Node.js.

## Bundles vs Composants
*   **Composant** : Librairie PHP pure (Namespace `Symfony\Component\...`). Pas de config automatique, pas de dépendance au Kernel.
*   **Bundle** : Plugin pour le Framework Symfony (Namespace `Symfony\Bundle\...` ou Vendor). Contient la configuration, les services, les listeners pour intégrer un ou plusieurs composants dans l'application.
    *   Ex: `SecurityBundle` intègre les composants `Security-Core`, `Security-Http`, `Security-Csrf`.

## 🧠 Concepts Clés
1.  **Standalone** : Chaque composant a son propre dépôt Git (miroir du monorepo) et peut être installé seul (`composer require symfony/finder`).
2.  **Stabilité** : Les composants sont soumis à la BC Promise stricte. C'est pourquoi des projets comme Drupal 8+, PrestaShop 1.7+, Laravel utilisent massivement ces composants.

## ⚠️ Points de vigilance (Certification)
*   **Rôle précis** : L'examen vous demandera quel composant utiliser pour une tâche.
    *   "Générer une URL" -> `Routing`.
    *   "Gérer un upload" -> `HttpFoundation` (UploadedFile).
    *   "Valider un email" -> `Validator`.
    *   "Envoyer un email" -> `Mailer` (pas `SwiftMailer` qui est mort).
*   **Contracts** : Symfony extrait les interfaces dans des paquets séparés (`symfony/contracts`, ex: `ServiceSubscriberInterface`, `HttpClientInterface`) pour réduire le couplage.

## Ressources
*   [Symfony Components Documentation](https://symfony.com/components)
