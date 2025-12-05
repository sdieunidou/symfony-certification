# Logging (Journalisation)

## Concept Clé
Symfony utilise la librairie **Monolog** pour gérer les logs.
Le système respecte l'interface standard **PSR-3** (`Psr\Log\LoggerInterface`).

## Installation
Le `monolog-bundle` est généralement installé par défaut via `symfony/framework-bundle` (recipe `webapp`), sinon :
```bash
composer require symfony/monolog-bundle
```

## Utilisation de base
Pour logger un message, injectez `Psr\Log\LoggerInterface`.

```php
use Psr\Log\LoggerInterface;

class ProductController extends AbstractController
{
    public function index(LoggerInterface $logger): Response
    {
        $logger->info('Visite de la page produit', [
            'id' => 123, // Contexte (données structurées)
            'user' => 'admin'
        ]);

        $logger->error('Une erreur critique est survenue !');

        return $this->render('...');
    }
}
```

## Niveaux de Log (PSR-3)
Du moins critique au plus critique :
1.  `debug` : Infos détaillées pour le débogage.
2.  `info` : Événements normaux (Login, Commande passée).
3.  `notice` : Normal mais significatif.
4.  `warning` : Situations exceptionnelles mais pas d'erreur (Disque presque plein).
5.  `error` : Erreur d'exécution qui ne nécessite pas d'action immédiate.
6.  `critical` : Composant indisponible (BDD down).
7.  `alert` : Action immédiate requise (Site down).
8.  `emergency` : Système inutilisable.

## Canaux (Channels)
Les logs sont organisés en "canaux" (`app`, `request`, `doctrine`, `security`).
Par défaut, `LoggerInterface` écrit dans le canal `app`.

### Cibler un canal spécifique
Pour écrire dans un canal particulier, utilisez le type-hint ou l'attribut `#[Target]`.

```php
// Symfony 6+ : Injection par nom de variable
public function __construct(LoggerInterface $requestLogger) {}

// Symfony 7+ : Attribut Target
use Symfony\Component\DependencyInjection\Attribute\Target;

public function __construct(
    #[Target('mon_canal')] LoggerInterface $logger
) {}
```

### Créer un canal personnalisé
Définissez-le dans `monolog.yaml` :
```yaml
monolog:
    channels: ['mon_canal', 'facturation']
```

## Configuration (`monolog.yaml`)
La configuration se fait par **Handlers** (Gestionnaires). Chaque handler décide **quoi** faire des logs (écrire dans un fichier, envoyer un email, ignorer).

### Exemple Dev
```yaml
monolog:
    handlers:
        main:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
            channels: ["!event"] # Exclure le canal 'event' (trop verbeux)
        console:
            type: console
            process_psr_3_messages: false
            channels: ["!event", "!doctrine", "!console"]
```

### Exemple Prod (Fingers Crossed)
En production, on utilise souvent `fingers_crossed`.
**Concept** : Il garde tous les logs en mémoire (buffer). Si une erreur (`error`) survient, il écrit **tout** le buffer (même les `debug` précédents) dans le fichier. Sinon, il jette tout.
Cela permet d'avoir le contexte complet d'une erreur sans remplir le disque de logs inutiles.

```yaml
monolog:
    handlers:
        main:
            type: fingers_crossed
            action_level: error # Déclencheur
            handler: nested
        nested:
            type: stream
            path: "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
```

## Processors (Enrichissement)
Les processors ajoutent des infos à **tous** les logs (IP, User, Session ID).
Symfony en fournit plusieurs par défaut (`PsrLogMessageProcessor`).

Pour ajouter le vôtre :
1.  Créer une classe (Service).
2.  Lui ajouter le tag `monolog.processor`.
3.  La méthode `__invoke(array $record)` modifie l'enregistrement.

## Rotation des logs
Le handler `rotating_file` permet de créer un fichier par jour et de supprimer les vieux logs automatiquement.
```yaml
handlers:
    main:
        type: rotating_file
        path: "%kernel.logs_dir%/%kernel.environment%.log"
        max_files: 10 # Garder 10 jours
```

## 🧠 Concepts Clés
1.  **Buffer** : En prod, les logs ne sont pas écrits instantanément pour la performance.
2.  **Contexte** : Ne jamais concaténer de variables dans le message (`$logger->info('User '.$id)`). Utilisez le tableau de contexte (`$logger->info('User {id}', ['id' => $id])`) pour que les outils d'analyse (Elasticsearch/Kibana) puissent indexer les valeurs.

## Ressources
*   [Symfony Docs - Logging](https://symfony.com/doc/current/logging.html)
*   [Monolog Configuration](https://symfony.com/doc/current/reference/configuration/monolog.html)
