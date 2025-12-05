# Cache Asynchrone & Stampede Protection

## Le Problème "Cache Stampede"
Quand un item de cache très demandé expire, 1000 requêtes concurrentes peuvent essayer de le recalculer en même temps, faisant tomber la base de données.
Le composant Cache de Symfony utilise nativement le **Probabilistic Early Expiration** :
*   Il fait semblant que l'item a expiré un peu avant la vraie fin, pour *une seule* requête aléatoire.
*   Cette requête recalcule la valeur.
*   Les autres continuent de recevoir l'ancienne valeur (encore valide quelques secondes) en attendant.

## Calcul Asynchrone (Messenger)
Pour aller plus loin, on peut déléguer le re-calcul à un **Worker** (via Messenger).
L'utilisateur reçoit immédiatement l'ancienne valeur (stale), et le recalcul se fait en arrière-plan.

**Configuration :**

```yaml
framework:
    cache:
        pools:
            async.cache:
                # Bus Messenger à utiliser
                early_expiration_message_bus: messenger.default_bus

    messenger:
        routing:
            'Symfony\Component\Cache\Messenger\EarlyExpirationMessage': async_bus
```

**Fonctionnement :**
1.  On appelle `$cache->get()`.
2.  Si l'item est "élu" pour expiration anticipée :
3.  Symfony renvoie la valeur actuelle (rapide).
4.  Symfony dispatch un `EarlyExpirationMessage` dans le bus.
5.  Le Worker consomme le message et recalcule la valeur via le `CallbackInterface` fourni au `get()`.
6.  Le cache est mis à jour pour les prochaines lectures.

Cela garantit des performances optimales pour l'utilisateur final (jamais d'attente de calcul).

## Ressources
*   [Symfony Docs - Async Cache](https://symfony.com/doc/current/cache.html#computing-cache-values-asynchronously)
