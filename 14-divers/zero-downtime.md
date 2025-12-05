# Déploiement Zéro Downtime (ZDD)

## Concept Clé
Le déploiement sans interruption de service (Zero Downtime Deployment) est la norme pour les applications modernes.
L'application doit rester disponible pendant la mise à jour.
Cela implique que la version N et la version N+1 cohabitent pendant un court instant.

## Défis Techniques

### 1. Sessions
Si vous utilisez le stockage de session par fichier local, l'utilisateur perd sa session s'il bascule d'un serveur à l'autre.
*Solution* : Stocker les sessions dans un backend partagé (Redis, Base de données).

### 2. Base de Données (Migrations)
C'est le point le plus critique. Le code V1 doit fonctionner avec la DB V2 (le temps que le déploiement se finisse).
**Règle** : Ne jamais faire de changement "destructeur" (Breaking Change) immédiat.

#### Pattern "Expand and Contract" (Renommer une colonne)
Vous voulez renommer `username` en `login`.
1.  **Expand** (Déploiement 1) :
    *   Créer la colonne `login`.
    *   Modifier le code pour écrire dans `username` ET `login`.
    *   Migrer les données existantes (`UPDATE table SET login = username`).
    *   Le code lit toujours `username`.
2.  **Migrate** (Déploiement 2) :
    *   Le code lit maintenant `login`.
    *   Le code n'écrit plus que dans `login`.
3.  **Contract** (Déploiement 3) :
    *   Supprimer la colonne `username`.

### 3. Code PHP et Cache
Lors d'un déploiement atomique (symlink switch), il peut y avoir quelques millisecondes où le cache OPcache est invalide par rapport aux fichiers sur le disque.
*Solution* : Redémarrage gracieux de PHP-FPM (`reload` et non `restart`) ou architecture Container (Kubernetes) qui remplace les pods un par un.

## Feature Flags (Toggles)
Découpler le déploiement (code) de la mise en service (feature).
Le code est déployé mais inactif (caché derrière un `if (Feature::isActive('new_checkout'))`).
On peut activer la feature instantanément pour tous, ou progressivement (Canary Release), et revenir en arrière (Kill Switch) sans redéployer.

## Ressources
*   [Deploying Symfony Applications](https://symfony.com/doc/current/deployment.html)
*   [Database Migrations with Zero Downtime](https://www.doctrine-project.org/projects/doctrine-migrations/en/current/reference/managing-migrations.html)
