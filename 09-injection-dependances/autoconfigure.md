# Autoconfigure (Service Container)

## Concept clé
L'**autoconfigure** est une fonctionnalité du conteneur de services qui automatise la configuration (principalement l'ajout de **tags**) de vos services en fonction des classes qu'ils étendent ou des interfaces qu'ils implémentent.

Au lieu de déclarer manuellement chaque commande, validateur ou extension Twig avec des tags spécifiques, Symfony détecte l'interface et configure le service pour vous.

## La Configuration Standard

Dans une application Symfony standard, l'autoconfigure est activée par défaut pour tous les services dans `config/services.yaml` via la section `_defaults`.

```yaml
services:
    # Configuration par défaut
    _defaults:
        autowire: true
        autoconfigure: true # C'est ici que ça se passe

    # Vos services
    App\:
        resource: '../src/'
        # ...
```

## Exemples Concrets

Grâce à `autoconfigure: true`, Symfony applique automatiquement les tags suivants :

| Interface / Classe Mère | Tag ajouté automatiquement | Effet |
| :--- | :--- | :--- |
| `Symfony\Component\Console\Command\Command` | `console.command` | La commande est disponible via `php bin/console` |
| `Symfony\Component\EventDispatcher\EventSubscriberInterface` | `kernel.event_subscriber` | Les méthodes sont enregistrées comme écouteurs d'événements |
| `Twig\Extension\AbstractExtension` | `twig.extension` | Les filtres/fonctions Twig sont disponibles dans les templates |
| `Symfony\Component\Validator\ConstraintValidator` | `validator.constraint_validator` | Le validateur est reconnu par le composant Validator |
| `Symfony\Component\Serializer\Normalizer\NormalizerInterface` | `serializer.normalizer` | Ajoute le normalizer au Serializer |

## Fonctionnalités Avancées

### 1. Configuration Personnalisée (`_instanceof`)
Vous pouvez définir vos propres règles d'autoconfiguration pour vos interfaces personnalisées directement dans `services.yaml`. C'est très puissant pour créer des architectures de plugins.

```yaml
services:
    # ... _defaults ...

    _instanceof:
        # Tous les services implémentant cette interface recevront ce tag
        App\Contract\ReportGeneratorInterface:
            tags: ['app.report_generator']
            # On peut aussi ajouter des appels de méthode ou des propriétés
            calls:
                - [setLogger, ['@logger']]
```

### 2. Attributs PHP (Alternative moderne)
Depuis Symfony 5.3+, vous pouvez aussi utiliser l'attribut `#[AutoconfigureTag]` directement sur la classe PHP, ce qui rend la configuration YAML optionnelle pour ce cas.

```php
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.handler')]
class MyHandler implements HandlerInterface
{
    // ...
}
```

Il existe aussi `#[Autoconfigure]` pour modifier d'autres propriétés (public, shared, lazy, etc.).

### 3. Désactiver l'autoconfigure
Si vous avez besoin d'un contrôle total sur un service spécifique, vous pouvez désactiver l'autoconfigure.

```yaml
App\Service\SpecialCommand:
    autoconfigure: false
    tags: [] # Aucun tag ne sera ajouté automatiquement
```

## 🧠 Concepts Clés
1.  **Lien avec les Tags** : Fondamentalement, `autoconfigure` est un moteur à règles : "Si instance de X, alors ajouter tag Y".
2.  **Compilation** : Tout ceci se produit lors de la compilation du conteneur (cache warmup). Il n'y a **aucun surcoût** de performance au runtime (production).
3.  **Extension de Bundle** : Les bundles tiers définissent leurs propres règles d'autoconfiguration dans leurs classes `DependencyInjection\MyBundleExtension`.

## ⚠️ Points de vigilance (Certification)
*   **Priorité** : Une configuration explicite sur un service spécifique l'emporte sur les règles d'autoconfiguration.
*   **Interfaces vs Classes** : L'autoconfiguration fonctionne aussi bien avec des interfaces qu'avec des classes abstraites ou concrètes (c'est un `instanceof` check).
*   **Ordre** : La section `_instanceof` est traitée par le `ResolveInstanceofConditionalsPass`.

## Ressources
*   [Symfony Docs - Configuring Services](https://symfony.com/doc/current/service_container.html#the-autoconfigure-option)
*   [Symfony Docs - Service Tags](https://symfony.com/doc/current/reference/dic_tags.html)
