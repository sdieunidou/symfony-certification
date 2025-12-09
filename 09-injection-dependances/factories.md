# Factories (Usines)

## Concept clé
Une Factory est utilisée pour créer des services qui nécessitent une logique d'instanciation complexe (calculs, conditions), qui ont des constructeurs statiques, ou qui proviennent de bibliothèques tierces (legacy).

## 1. Types de Factories

### Static Factory
C'est le cas le plus simple : une méthode statique crée l'objet.
La classe du service (`class`) n'a pas d'importance technique (c'est le retour de la factory qui compte), mais il est bon de la spécifier pour les outils d'analyse.

```yaml
services:
    App\Service\NewsletterManager:
        # Syntaxe : [Classe, MéthodeStatique]
        factory: ['App\Email\NewsletterManagerStaticFactory', 'createNewsletterManager']
```

### Service Factory (Non-Static)
Si votre factory a besoin de dépendances (ex: logger, configuration), elle doit être elle-même un service.

```yaml
services:
    # 1. On enregistre la factory comme service
    App\Factory\PaymentClientFactory:
        arguments: ['%api_key%']

    # 2. On utilise le service factory
    App\Lib\PaymentClient:
        # Syntaxe : [@ServiceID, Méthode]
        factory: ['@App\Factory\PaymentClientFactory', 'createClient']
```

### Invokable Factory
Si votre factory implémente `__invoke()`, vous pouvez omettre le nom de la méthode.

```yaml
services:
    App\Lib\PaymentClient:
        # Symfony détecte automatiquement __invoke
        factory: '@App\Factory\InvokableFactory'
```

## 2. Factory Interne (Self-Factory)
Souvent, une classe possède sa propre méthode statique de création (ex: `MyClass::create()`).

```php
// src/Email/NewsletterManager.php
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

// Moderne : via Attribut (Symfony 6.3+)
#[Autoconfigure(constructor: 'create')] 
class NewsletterManager
{
    public static function create(string $apiKey): self { ... }
}
```

Equivalent YAML (Legacy) :
```yaml
services:
    App\Email\NewsletterManager:
        factory: [null, 'create'] # null signifie "la classe elle-même"
        # OU
        constructor: 'create'     # Syntaxe raccourcie
```

## 3. Expression Factory (Avancé)
Vous pouvez utiliser le langage d'expression pour choisir dynamiquement quel service créer.

```yaml
services:
    App\Mailer\MailerInterface:
        # Retourne un service différent selon le mode debug
        factory: '@=parameter("kernel.debug") ? service("app.mailer.debug") : service("app.mailer.real")'
```

## 4. Passer des arguments à la Factory
Les arguments définis sous `arguments` sont passés à la méthode de la factory, pas au constructeur du service produit (puisque c'est la factory qui appelle `new`).

```yaml
services:
    App\Service\MyService:
        factory: ['@App\Factory', 'create']
        arguments:
            $arg1: 'valeur pour la méthode create'
```

## 🧠 Concepts Clés
1.  **Découplage** : La factory encapsule la complexité. Le consommateur demande `PaymentClient` et reçoit une instance prête, sans savoir comment elle a été créée.
2.  **Lazy** : La méthode factory n'est exécutée que lorsque le service est demandé.

## ⚠️ Points de vigilance (Certification)
*   **Factory vs Constructor** : Si `factory` est défini, le constructeur de la classe n'est **jamais** appelé directement par le conteneur. C'est la factory qui est responsable de faire `new`.
*   **Class** : L'option `class` dans le YAML est optionnelle si l'ID du service est un FQCN (Fully Qualified Class Name), mais la factory peut retourner n'importe quel objet (polymorphisme).

## Ressources
*   [Symfony Docs - Factories](https://symfony.com/doc/current/service_container/factories.html)
