# Fonctionnalités Avancées d'Injection

## Concept clé
Ce fichier couvre les mécanismes d'injection complexes comme l'Expression Language, les services Lazy, et l'injection de valeurs spéciales.

## 1. Expression Language (`@=`)
Vous permet d'injecter des valeurs calculées dynamiquement. Utile pour injecter le résultat d'une méthode d'un autre service.

```yaml
services:
    App\Service\Mailer:
        arguments:
            # Injecte le résultat de config.get('mailer_host')
            $host: "@=service('App\\Config\\AppConfig').get('mailer_host')"
            
            # Condition ternaire
            $isDebug: "@=parameter('kernel.debug') ? true : false"
```

## 2. Lazy Services
Si un service est lourd à instancier (connexion DB, API externe) mais rarement utilisé, marquez-le `lazy: true`.

```yaml
services:
    App\Service\HeavyService:
        lazy: true
```
Symfony injectera un **Proxy** (une fausse instance légère). Le vrai service ne sera instancié que lorsque vous appellerez une de ses méthodes.
*   Nécessite le package `symfony/proxy-manager-bridge`.
*   Fonctionne aussi avec l'interface `GhostObjectInterface` (moderne).

## 3. Services Synthétiques
Un service `synthetic: true` est un service qui n'est pas créé par le conteneur, mais injecté **au runtime** (dans le code PHP) avant que le conteneur ne soit utilisé.

```yaml
services:
    app.runtime_context:
        synthetic: true
```
C'est utilisé par le Kernel pour injecter `kernel`, `request_stack`, etc.

## 4. Types d'Injection
Il existe 3 façons principales d'injecter des dépendances :

1.  **Constructor Injection** (Recommandé) :
    *   Les dépendances sont requises.
    *   Le service est immuable.
    *   Facile à tester.
2.  **Setter Injection** (Via `calls`) :
    *   Les dépendances sont optionnelles ou mutables.
    *   Résout les références circulaires.
3.  **Property Injection** :
    *   Injecter directement dans une propriété publique ou annotée (`#[Required]`).
    *   Utilisé par l'autowiring pour configurer automatiquement les dépendances sans passer par le constructeur.

    ```php
    use Symfony\Contracts\Service\Attribute\Required;
    use Psr\Log\LoggerInterface;

    class ReportGenerator
    {
        // Injection directe dans la propriété publique
        #[Required]
        public LoggerInterface $logger;
    }
    ```

## 5. Constantes et PHP Natif
Vous pouvez injecter des constantes PHP directement.

```yaml
services:
    App\Client:
        arguments:
            $timeout: !php/const App\Client::DEFAULT_TIMEOUT
```

## 6. Options sur les arguments
Symfony permet de modifier le comportement de l'injection sur un argument spécifique.

*   `!optional` : Si le service n'existe pas, l'argument est ignoré (rarement utilisé en YAML, plutôt via `ContainerBuilder`).
*   `on-invalid: null` / `ignore` : Comportement si le service injecté n'existe pas.

```yaml
services:
    App\Service\OptionalHandler:
        arguments:
            # Si 'app.logger' n'existe pas, l'argument sera null
            $logger: '@?app.logger'
            
            # Syntaxe verbeuse équivalente
            $otherLogger:
                type: service
                id: app.logger
                on_invalid: null # ou 'ignore'
```

```yaml
arguments:
    $logger: '@?logger' # Le '?' signifie : injecter null si le service n'existe pas
```

## 🧠 Concepts Clés
1.  **Proxies** : Pour les services Lazy, Symfony génère une classe qui hérite de votre service. Attention aux propriétés `final` ou `private` qui peuvent poser problème selon la version.
2.  **ExpressionProvider** : On peut étendre le langage d'expression avec ses propres fonctions via un `ExpressionFunctionProviderInterface`.

## ⚠️ Points de vigilance (Certification)
*   **Expression Language** : C'est puissant mais cela couple la configuration à la logique. À utiliser avec parcimonie.
*   **Performance** : Les expressions sont évaluées à chaque demande du service (sauf si compilées, mais attention au runtime overhead).

## Ressources
*   [Symfony Docs - Expression Language in DI](https://symfony.com/doc/current/service_container/expression_language.html)
*   [Symfony Docs - Lazy Services](https://symfony.com/doc/current/service_container/lazy_services.html)
