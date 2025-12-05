# Conteneur de Services (Service Container)

## Concept clé
Le Conteneur de Services (ou DI Container) est un objet qui gère l'instanciation des services de votre application. C'est une "boîte magique" qui sait comment créer n'importe quel objet, avec ses dépendances.

## Application dans Symfony 7.0
Le conteneur est compilé pour la performance (il devient un gros fichier PHP `var/cache/dev/ContainerXYZ/App_KernelDevDebugContainer.php`).

### Principes
1.  **Services** : Des objets qui "font" quelque chose (Mailer, Logger, Repository). Ils sont généralement stateless et unique (Singleton).
2.  **Dépendances** : Les services dont un service a besoin pour fonctionner. Le conteneur les injecte (généralement dans le constructeur).

## Exemple de code (Conceptuel)

```php
// Sans conteneur
$logger = new Logger();
$mailer = new Mailer($logger, 'smtp://...');
$userManager = new UserManager($mailer, $dbConnection);

// Avec conteneur
// Vous demandez UserManager, le conteneur fabrique tout le graphe pour vous.
```

## Points de vigilance (Certification)
*   **Public vs Privé** : Par défaut (depuis Symfony 4), les services sont **privés**. On ne peut pas les récupérer via `$container->get('id')` dans un contrôleur (sauf s'ils sont explicitement publics).
*   **Frozen** : Une fois le kernel booté, le conteneur est "gelé" (frozen). On ne peut plus ajouter de services dynamiquement.

## Ressources
*   [Symfony Docs - Service Container](https://symfony.com/doc/current/service_container.html)

