# Doctrine & Messenger (Concurrency Pattern)

## Concept Clé
Traiter les écritures en base de données de manière asynchrone permet de :
1.  Lisser la charge (Queue).
2.  Gérer la concurrence (Serialisation des traitements).
3.  Gérer les erreurs (Retries).

## Middleware & Transaction
Messenger exécute les messages à travers des Middlewares.
Le `DoctrineTransactionMiddleware` (activé par défaut) enveloppe le traitement du Handler dans une transaction database.
Si le Handler lance une exception, la transaction est rollbackée.

## Idempotence
Si un message est rejoué (Retry) après un crash, il ne doit pas corrompre les données (ex: ne pas débiter le client 2 fois).
Le Handler doit être **Idempotent**.

### Stratégies d'Idempotence
1.  **Vérifier l'état** : "Si la commande est déjà payée, ne rien faire".
2.  **Dédoublonnage** : Utiliser l'ID du message ou une clé métier unique stockée en base (`ProcessedMessage`).

## Cas concret : Incrémentation concurrente
1000 utilisateurs cliquent sur "J'aime" en même temps.
*   **Approche synchrone (Controller)** : 1000 transactions concurrentes -> Deadlocks ou Lock Wait Timeout.
*   **Approche Messenger** :
    1.  Controller : Envoie 1000 messages `VoteForPost` dans une queue (Redis/RabbitMQ). C'est instantané.
    2.  Worker : Consomme les messages un par un (ou par petits lots).
    3.  Handler :
        ```php
        $em->beginTransaction(); // Via Middleware
        $post = $repo->find($msg->getPostId(), LockMode::PESSIMISTIC_WRITE); // Lock court
        $post->incrementLikes();
        // Commit
        ```
    
Comme le Worker traite les messages séquentiellement (ou avec une concurrence maîtrisée par le nombre de workers), on réduit drastiquement les conflits DB.

## Gestion des Retries
Si une `OptimisticLockException` ou `DeadlockException` survient, Messenger peut automatiquement rejouer le message X fois après Y secondes.

```yaml
framework:
    messenger:
        transports:
            async:
                retry_strategy:
                    max_retries: 3
                    delay: 1000
```

