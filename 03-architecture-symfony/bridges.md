# Bridges (Ponts)

## Concept clé
Symfony applique une philosophie de **découplage fort**. Les composants Symfony (ex: `Validator`, `Form`) ne doivent pas dépendre de librairies externes (ex: `Doctrine`, `Twig`) pour fonctionner de base.
Cependant, pour faciliter la vie du développeur, Symfony fournit des **Bridges** : des paquets glu qui intègrent nativement ces librairies tierces dans l'écosystème Symfony.

## Principaux Bridges dans Symfony 7

### 1. Doctrine Bridge (`symfony/doctrine-bridge`)
Le plus critique.
*   **Registry** : Expose les services Doctrine (`EntityManager`, `Registry`) dans le conteneur.
*   **Form** : `EntityType` (liste déroulante d'entités).
*   **Validator** : Contrainte `UniqueEntity`.
*   **Security** : `EntityUserProvider` (charger le user depuis la DB).
*   **Messenger** : Middleware transactionnel (flush auto).
*   **Profiler** : Panneau Doctrine (requêtes SQL).

### 2. Twig Bridge (`symfony/twig-bridge`)
Transforme Twig (moteur neutre) en moteur de vue Symfony.
*   **Fonctions** : `path()`, `url()`, `asset()`, `is_granted()`, `form()`.
*   **Tags** : `{% trans %}`.
*   **AppVariable** : La variable globale `app` (user, request, session, flashes).
*   **Form** : Thèmes de formulaire (`form_div_layout.html.twig`).

### 3. Monolog Bridge (`symfony/monolog-bridge`)
Connecte les logs du `HttpKernel` et de la `Console` à Monolog.
*   **Handlers** : Ajoute la capacité d'envoyer des emails (`SwiftMailerHandler` ou `MailerHandler`), d'écrire dans la Console.
*   **Wiring** : Configure automatiquement les channels (`doctrine`, `request`, `security`).

### 4. PHPUnit Bridge (`symfony/phpunit-bridge`)
Plus qu'un simple pont, c'est un couteau suisse pour les tests.
*   **Deprecation Helper** : Signale les dépréciations déclenchées par les tests.
*   **Polyfills** : Installe des polyfills pour les fonctions PHP récentes si nécessaire.
*   **Namespaced PHPUnit** : Permet d'utiliser différentes versions de PHPUnit.
*   **Coverage** : Optimisations pour la couverture de code.

### Autres Bridges Notables
*   **Mailer** : `symfony/google-mailer`, `symfony/mailgun-mailer` (Adaptateurs spécifiques).
*   **Messenger** : `symfony/amqp-messenger`, `symfony/redis-messenger` (Transports).
*   **ProxyManager** : Pour le Lazy Loading des services.

## 🧠 Concepts Clés
1.  **Installation Transparente** : Grâce à Symfony Flex, vous installez rarement un bridge directement.
    *   `composer require twig` -> installe `twig/twig` (lib), `symfony/twig-bundle` (plugin), et `symfony/twig-bridge` (glu).
2.  **Abstraction** : Le bridge permet d'utiliser des interfaces Symfony (`UserProviderInterface`) implémentées via du code tiers (Doctrine).

## ⚠️ Points de vigilance (Certification)
*   **Responsabilité** : Savoir qui fait quoi.
    *   Question : "Qui fournit la fonction `path()` dans Twig ?"
    *   Réponse : Le `TwigBridge` (extension `RoutingExtension`). Ce n'est ni Twig (le moteur), ni le composant Routing (qui ne connaît pas Twig).
*   **Dépendances** : Un bridge a souvent des dépendances optionnelles (`suggests` dans composer.json). Par exemple, `DoctrineBridge` ne requiert pas forcément l'ORM complet, il peut marcher avec DBAL seul pour certaines fonctions.

## Ressources
*   [Symfony Packagist - Bridges](https://packagist.org/?query=symfony%20bridge)
*   [Documentation Doctrine Bridge](https://symfony.com/doc/current/doctrine.html)
