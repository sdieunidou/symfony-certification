# Enregistrement de Services

## Concept clé
Dire au conteneur comment créer vos objets.
Depuis Symfony 4, grâce à l'Autowiring et l'Autoconfigure, c'est souvent automatique.

## Application dans Symfony 7.0

### Configuration par défaut (services.yaml)
```yaml
services:
    # Configuration par défaut pour les services de ce fichier
    _defaults:
        autowire: true      # Injecte automatiquement les dépendances
        autoconfigure: true # Ajoute les tags automatiquement (ex: Twig Extension)

    # Rend les classes dans src/ disponibles comme services
    App\:
        resource: '../src/'
        exclude:
            - '../src/DependencyInjection/'
            - '../src/Entity/'
            - '../src/Kernel.php'
```

### Attributs PHP (AsService)
Rarement nécessaire car la config par défaut couvre 99% des cas, mais on peut exclure ou configurer un service via attributs ou config manuelle.

## Points de vigilance (Certification)
*   **Autowiring** : Le conteneur regarde le type (Type Hint) de l'argument du constructeur (`LoggerInterface $logger`) et trouve le service correspondant.
*   **Scalar arguments** : L'autowiring ne marche pas pour les scalaires (string, int). Il faut utiliser `bind` ou `#[Autowire]`.
*   **Multiple implementations** : Si vous avez 2 classes qui implémentent `MailerInterface`, l'autowiring échouera (ambiguïté). Il faut nommer l'argument (`$defaultMailer`) ou utiliser `#[Target('my.mailer')]`.

## Ressources
*   [Symfony Docs - Service Configuration](https://symfony.com/doc/current/service_container.html#creating-configuring-services-in-the-container)

