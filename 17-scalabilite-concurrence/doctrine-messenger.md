# Doctrine & Messenger (Architecture Scalable)

## Concept Clé
Utiliser un **Command Bus** (Symfony Messenger) permet de :
1.  Lisser les pics de charge (Queue).
2.  Isoler les échecs (Retries).
3.  Découpler le métier de l'infrastructure.

## 1. Transactional Middleware
Symfony Messenger intègre `DoctrineTransactionMiddleware`.
Chaque message est traité dans une transaction unique.
*   Succès Handler → `COMMIT`
*   Exception Handler → `ROLLBACK`

Cela garantit l'atomicité : le message n'est dépilé que si le travail est fait.

```yaml
framework:
    messenger:
        buses:
            command.bus:
                middleware:
                    - doctrine_transaction
```

## 2. Stratégies de Retry
Il existe 3 niveaux pour gérer les erreurs transitoires (Deadlocks, Timeouts).

### Niveau 1 : Local (Dans le code)
Utiliser une boucle `try/catch` dans le service.
*   ✅ Précis.
*   ❌ Verbeux, duplication de code.

### Niveau 2 : Applicatif (Messenger - Recommandé)
Laisser Messenger gérer le retry. Si une `DeadlockException` survient, Messenger rollback, attend, et relance le message.

```yaml
framework:
    messenger:
        transports:
            async:
                retry_strategy:
                    max_retries: 5
                    delay: 500           # 500ms
                    multiplier: 2         # 1s, 2s, 4s...
                    max_delay: 10000
```
*   ✅ Centralisé, résilient, backoff exponentiel natif.
*   ✅ Chaque tentative est une nouvelle transaction propre.

### Niveau 3 : Infrastructure (DB/Proxy)
Certains drivers ou proxies (ProxySQL) peuvent rejouer les transactions.
*   ✅ Transparent.
*   ❌ Peu de contrôle métier.

## 3. Idempotence
Avec les retries, un handler peut être exécuté plusieurs fois pour le même message (ex: le commit passe, mais l'ack RabbitMQ échoue).
Votre code doit être **Idempotent** : "L'exécuter 2 fois a le même effet qu'une fois".

*   *Exemple* : "Débiter 10€". Si exécuté 2 fois → -20€. ❌ Pas idempotent.
*   *Exemple* : "Passer le statut de la commande #123 à PAYÉ". Si exécuté 2 fois → Statut PAYÉ. ✅ Idempotent.

## 4. Architecture CQRS & Workers
Grâce à Messenger, vous pouvez séparer :
*   **Commandes** (Écriture, via Handler synchrone ou asynchrone).
*   **Requêtes** (Lecture, via Repository direct).

Pour la scalabilité : lancez simplement plus de processus Workers (`php bin/console messenger:consume async`) pour dépiler plus vite. Les verrous (SKIP LOCKED) en base permettront aux workers de ne pas se marcher dessus.

## Ressources
*   [Symfony Messenger](https://symfony.com/doc/current/messenger.html)
