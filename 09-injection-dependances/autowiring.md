# Autowiring

## Concept clé
L'autowiring permet à Symfony de deviner quels services injecter dans votre constructeur en se basant sur les types (Type Hinting).

## Application dans Symfony 7.0
Activé par défaut dans `config/services.yaml`.

```php
// Symfony voit "LoggerInterface", il cherche le service aliasé à cette interface.
public function __construct(LoggerInterface $logger) { ... }
```

### Gestion des conflits
Si plusieurs services implémentent l'interface :
1.  Définir un **alias par défaut** (ex: `LoggerInterface` pointe vers `monolog.logger`).
2.  Utiliser le **nom de l'argument** (`$fileLogger` -> cherche un service nommé `fileLogger`).
3.  Utiliser l'attribut **#[Autowire]** (PHP 8).
4.  Utiliser l'attribut **#[Target]** (PHP 8).

```php
public function __construct(
    #[Target('filesystem.public')] FilesystemOperator $storage
) {}
```

## Points de vigilance (Certification)
*   **Performance** : L'autowiring est résolu à la compilation du conteneur. Il n'a **aucun** impact sur la performance en production (c'est juste du code PHP généré en dur).
*   **Magie** : Ce n'est pas vraiment magique, c'est juste de l'introspection de type.

## Ressources
*   [Symfony Docs - Autowiring](https://symfony.com/doc/current/service_container/autowiring.html)

