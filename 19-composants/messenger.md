# Le Composant Messenger

Le composant **Messenger** permet aux applications d'envoyer et de recevoir des messages vers/depuis d'autres applications ou via des files d'attente. Il simplifie la communication inter-processus et permet d'implémenter des architectures découplées (CQRS, Event-Driven).

## 1. Concepts Fondamentaux

L'architecture repose sur un flux unidirectionnel :

1.  **Message** : Un objet PHP simple (DTO) contenant des données. Il ne doit contenir *aucune logique*.
2.  **Bus** : Responsable de dispatcher le message.
3.  **Envelope** : Une structure qui enveloppe le message et peut porter des métadonnées (via des **Stamps**).
4.  **Middleware** : Une couche logicielle traversée par le message avant et après son traitement.
5.  **Handler** : La classe qui exécute la logique métier associée au message.
6.  **Transport** : Le mécanisme d'envoi et de réception (Synchrone, Doctrine, AMQP, Redis, etc.).

---

## 2. Implémentation de Base

### Création d'un Message et Handler

Depuis Symfony 6+, l'attribut `#[AsMessageHandler]` est la méthode privilégiée.

```php
// src/Message/SmsNotification.php
class SmsNotification
{
    public function __construct(
        public readonly string $content,
        public readonly string $phoneNumber
    ) {}
}

// src/MessageHandler/SmsNotificationHandler.php
namespace App\MessageHandler;

use App\Message\SmsNotification;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SmsNotificationHandler
{
    public function __invoke(SmsNotification $message): void
    {
        // Logique d'envoi...
    }
}
```

*Note : Vous pouvez avoir plusieurs handlers pour un même message. Dans ce cas, le bus doit être configuré pour autoriser plusieurs handlers (pattern Event).*

### Dispatch

```php
use Symfony\Component\Messenger\MessageBusInterface;

public function index(MessageBusInterface $bus): Response
{
    $bus->dispatch(new SmsNotification('Hello', '+33600000000'));
    // ...
}
```

---

## 3. Traitement Asynchrone (Transports & Queues)

Par défaut, les messages sont traités de manière **synchrone** (dès le `dispatch`). Pour passer en asynchrone, on utilise le **Routing**.

### Configuration (`config/packages/messenger.yaml`)

```yaml
framework:
    messenger:
        # Définition des transports
        transports:
            # Traitement immédiat (par défaut si pas de route)
            sync: 'sync://'

            # Stockage en BDD (nécessite doctrine/orm)
            async_db: 
                dsn: '%env(MESSENGER_TRANSPORT_DSN)%' # ex: doctrine://default
                options:
                    auto_setup: false # Pour prod, optimiser les tables manuellement

            # RabbitMQ (AMQP)
            async_priority: 
                dsn: '%env(MESSENGER_TRANSPORT_DSN_AMQP)%'
                options:
                    exchange:
                        name: messages
                        type: direct
                    queues:
                        messages_high: { binding_keys: [high] }

        # Routage des messages vers les transports
        routing:
            'App\Message\SmsNotification': async_db
            'App\Message\VideoProcessing': async_priority
```

### Le Worker (Consommation)

Pour traiter les messages en attente dans les transports asynchrones, il faut lancer un worker.

```bash
# Consomme les messages du transport 'async_db'
php bin/console messenger:consume async_db

# Consomme plusieurs transports avec priorité (le premier est prioritaire)
php bin/console messenger:consume async_priority async_db
```

**Options importantes du worker :**
*   `--limit=10` : S'arrête après 10 messages (utile pour éviter les fuites de mémoire PHP, combiné avec Supervisor).
*   `--time-limit=3600` : S'arrête après 1 heure.
*   `--memory-limit=128M` : S'arrête si la mémoire dépasse ce seuil.

---

## 4. Gestion des Échecs (Retries & Failure)

Messenger possède un mécanisme robuste de reprise sur erreur.

### Stratégie de Retry
Si un Handler lance une exception, le Worker réessaie le message selon la configuration.

```yaml
framework:
    messenger:
        transports:
            async_db:
                dsn: ...
                retry_strategy:
                    max_retries: 3
                    delay: 1000        # 1 seconde
                    multiplier: 2      # Attente : 1s, 2s, 4s
                    max_delay: 0
```

### Failure Transport (Dead Letter Queue)
Si après tous les essais le message échoue toujours, il est envoyé vers un `failure_transport`.

```yaml
framework:
    messenger:
        failure_transport: failed

        transports:
            # ... autres transports
            failed: 'doctrine://default?queue_name=failed'
```

### Commandes de gestion des échecs
*   `messenger:failed:show` : Liste les messages en erreur.
*   `messenger:failed:retry` : Relance un ou plusieurs messages échoués.
*   `messenger:failed:remove` : Supprime définitivement un message.

---

## 5. Architecture Avancée : Bus Multiples & CQRS

Pour séparer les responsabilités (Command Query Responsibility Segregation), on configure souvent plusieurs bus.

```yaml
framework:
    messenger:
        default_bus: command.bus
        buses:
            command.bus:
                middleware:
                    - validation
                    - doctrine_transaction
            query.bus:
                middleware:
                    - validation
            event.bus:
                default_middleware: allow_no_handlers
```

### Injection du bon bus
Pour cibler un bus spécifique, on utilise le Type Hinting avec l'attribut `Target` (Symfony 6.3+) ou le nom du paramètre variable.

```php
public function __construct(
    #[Target('command.bus')] private MessageBusInterface $commandBus,
    #[Target('query.bus')] private MessageBusInterface $queryBus
) {}
```

### Récupérer une valeur (Query Bus)
Le `dispatch` retourne une `Envelope`. Pour récupérer la valeur de retour d'un Handler (synchrone), on utilise le `HandledStamp`.

```php
use Symfony\Component\Messenger\Stamp\HandledStamp;

$envelope = $queryBus->dispatch(new GetUserCount());
$handledStamp = $envelope->last(HandledStamp::class);
$count = $handledStamp->getResult();
```

Or, plus simplement via le Trait `HandleTrait` fourni par Symfony dans vos services.

---

## 6. Middlewares

Le système de middleware fonctionne comme un "oignon". Le message traverse les middlewares à l'aller (dispatch) et au retour (résultat).

### Middlewares Natifs
*   `doctrine_ping_connection` : Réouvre la connexion SQL si fermée (indispensable pour les workers).
*   `doctrine_close_connection` : Ferme la connexion après traitement.
*   `doctrine_transaction` : Enveloppe le Handler dans une transaction BDD. Commit si succès, Rollback si exception.
*   `validation` : Valide le DTO message via le composant Validator avant le handling.
*   `router_context` : Restaure le contexte (host, scheme) pour la génération d'URL dans les workers.

### Middleware Personnalisé
Pour créer un middleware (ex: Logging) :

```php
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Envelope;

class AuditMiddleware implements MiddlewareInterface
{
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        // Code AVANT le handler
        // dump('Avant');

        $envelope = $stack->next()->handle($envelope, $stack);

        // Code APRÈS le handler
        // dump('Après');

        return $envelope;
    }
}
```
Il doit ensuite être déclaré dans `messenger.yaml`.

---

## 7. Envelopes & Stamps (Métadonnées)

Les **Stamps** sont des marqueurs attachés à l'Enveloppe du message pour modifier le comportement du bus ou du transport.

### Stamps Utiles
1.  **`DelayStamp`** : Différer l'exécution.
    ```php
    $bus->dispatch(new SmsNotification(...), [new DelayStamp(5000)]); // 5 sec
    ```
2.  **`DispatchAfterCurrentBusStamp`** : N'envoyer le message que si le handler actuel termine avec succès.
3.  **`TransportNamesStamp`** : Forcer un transport spécifique dynamiquement.
4.  **`ValidationStamp`** : Configurer les groupes de validation pour le middleware `validation`.

---

## 8. Événements & Workers

Le Worker dispatch des événements natifs Symfony sur lesquels on peut se brancher :

*   `WorkerStartedEvent`
*   `WorkerMessageReceivedEvent`
*   `WorkerMessageHandledEvent`
*   `WorkerMessageFailedEvent`
*   `WorkerStoppedEvent`

**Cas d'usage** : Réinitialiser un service (ex: EntityManager, Logger) entre chaque message pour éviter les fuites de mémoire ou états corrompus.

---

## 9. Production & Déploiement

### Supervisor
En production, le worker doit tourner en permanence. S'il crash ou s'arrête (via `--limit`), il doit redémarrer. On utilise **Supervisor**.

Exemple de config `/etc/supervisor/conf.d/messenger-worker.conf` :
```ini
[program:messenger-consume]
command=php /path/to/your/app/bin/console messenger:consume async_db --time-limit=3600
user=www-data
numprocs=2
autostart=true
autorestart=true
process_name=%(program_name)s_%(process_num)02d
```

### Déploiement (Graceful Shutdown)
Lors d'un déploiement, il faut arrêter proprement les workers existants pour qu'ils ne traitent pas le nouveau code avec l'ancienne mémoire chargée, ou vice-versa.

1.  Lancer `php bin/console messenger:stop-workers`.
2.  Cette commande dépose un signal (fichier cache).
3.  Les workers finissent leur message en cours, détectent le signal, et s'arrêtent.
4.  Supervisor les redémarre automatiquement avec le nouveau code.

---

## Fonctionnement Interne

### Architecture
*   **MessageBus** : Itère sur la stack de middleware.
*   **Middleware** : Couches successives (Logging, Transaction, Handle).
*   **Receiver** : Poll les messages depuis une source externe.
*   **Worker** : Boucle infinie qui appelle le Receiver puis le Bus.

### Le Flux (Worker)
1.  **Get** : `Receiver->get()`.
2.  **Dispatch** : Envoie l'`Envelope` dans le bus.
3.  **Ack/Reject** : Si succès, `Receiver->ack()`, sinon `Receiver->reject()`.

## 10. Points d'attention pour la Certification

*   **Serialization** : Par défaut, Messenger utilise le format natif PHP `serialize()`. Pour l'interopérabilité (ex: consommer par une app Node.js), utiliser le **Serializer Symfony** (JSON) en configurant `serializer: messenger.transport.symfony_serializer` dans le transport.
*   **Batch Processing** : Implémenter `BatchHandlerInterface` permet à un worker de traiter les messages par lots (ex: 10 par 10) pour optimiser les IO (ex: `ack` AMQP groupé).
*   **Idempotence** : Puisqu'un message peut être délivré plusieurs fois (retry, réseau), le Handler doit idéalement être idempotent (le rejouer n'a pas d'impact négatif).
