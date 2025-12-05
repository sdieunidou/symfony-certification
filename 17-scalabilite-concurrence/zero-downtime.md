# Déploiement Zéro Downtime (ZDD)

## Concept Clé
Déployer une nouvelle version de l'application sans interrompre le service pour les utilisateurs (pas de page de maintenance).

## 1. Gestion des Migrations (Database)
C'est le point le plus critique. Le code V2 doit souvent tourner sur la base V1 pendant le déploiement.
**Règle d'or** : Ne jamais faire de changement destructif (Rename, Drop) tant que l'ancien code tourne.

### Le pattern "Expand & Contract"
Pour renommer une colonne `address` en `billing_address` :
1.  **Expand** : Créer `billing_address`, copier les données, et modifier le code pour écrire dans *les deux* colonnes (mais lire de l'ancienne). Déployer.
2.  **Migrate** : Migrer le code pour lire de la nouvelle colonne. Déployer.
3.  **Contract** : Supprimer l'ancienne colonne `address` (quand on est sûr que plus personne ne l'utilise).

## 2. Feature Flags (Toggles)
Déployer du code "dormant".
On met le nouveau code en prod, mais caché derrière un `if`.

```php
if ($this->featureManager->isActive('new_checkout')) {
    return $this->newCheckout();
}
return $this->oldCheckout();
```
Cela permet de tester en prod sur des admins, ou de faire un "Canary Release" (activer pour 10% des users).

## 3. Rolling Update (Kubernetes / Load Balancer)
Si vous avez 3 serveurs web :
1.  Sortir le Serveur A du Load Balancer.
2.  Mettre à jour le Serveur A.
3.  Remettre le Serveur A (qui traite les nouvelles requêtes avec le nouveau code).
4.  Répéter pour B et C.

Pendant ce processus, des requêtes arrivent sur le serveur V1 et d'autres sur le serveur V2.
D'où l'importance de la **compatibilité ascendante/descendante** du code avec la base de données et les sessions.

