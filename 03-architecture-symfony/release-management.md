# Release Management

## Concept clé
Symfony garantit un cycle de développement prévisible pour permettre aux entreprises de planifier leurs mises à jour.
C'est un modèle **"Time-based"** (basé sur le temps) et non "Feature-based".

## Le Calendrier
*   **Patch (x.y.Z)** : Environ tous les mois. Contient uniquement des correctifs de bugs.
*   **Mineure (x.Y)** : Tous les **6 mois** (Mai et Novembre). Contient des nouvelles fonctionnalités, mais **aucune rupture de compatibilité** (BC Promise).
*   **Majeure (X.y)** : Tous les **2 ans** (Novembre des années impaires). Peut contenir des ruptures de compatibilité (breaking changes).

## Cycle de Développement (6 mois)
Chaque version mineure suit un cycle strict de 6 mois divisé en deux phases :
1.  **Development (4 mois)** : Ajout de nouvelles fonctionnalités.
2.  **Stabilization (2 mois)** : Feature freeze. On ne corrige que les bugs, on prépare la release, et on laisse l'écosystème (bundles) s'adapter.

## Types de Versions

### 1. Version Standard (Standard Support)
*   Exemples : 7.0, 7.1, 7.2, 7.3.
*   **Support Bug** : 8 mois.
*   **Support Sécurité** : 8 mois.
*   **Cible** : Développeurs voulant les dernières features ("Fast movers"). Nécessite une mise à jour régulière tous les 6 mois.

### 2. Version LTS (Long Term Support)
*   Exemples : 4.4, 5.4, 6.4, **7.4** (future).
*   C'est toujours la **dernière version mineure** de la branche (x.4).
*   **Support Bug** : 3 ans.
*   **Support Sécurité** : 4 ans (3 ans de fixes + 1 an de sécurité).
*   **Cible** : Projets d'entreprise nécessitant de la stabilité à long terme.

## Correspondance Majeure/Mineure (Crucial)
Pour faciliter la migration, Symfony développe deux versions en parallèle lors d'un cycle majeur.
**Symfony 7.0 sort en même temps que Symfony 6.4.**

*   **6.4 (LTS)** = Toutes les features + Code Déprécié (BC Layer).
*   **7.0** = Les mêmes features - Code Déprécié (Nettoyage).

Cela permet une stratégie de migration "sans effort" :
1.  Mettre à jour vers la dernière version mineure (6.4).
2.  Corriger toutes les **Deprecations** (le code vous prévient via les logs/profiler).
3.  Une fois qu'il n'y a plus de dépréciations, passer à la majeure (7.0).

## Compatibilité PHP
*   La version PHP minimale est décidée pour chaque version **Majeure**.
*   Symfony supporte toutes les versions PHP sorties durant sa vie, y compris les nouvelles versions majeures de PHP.
*   Symfony 7 nécessite **PHP 8.2+**.

## 🧠 Concepts Clés
1.  **Backward Compatibility (BC)** : Garantie stricte. On ne casse rien entre 7.0 et 7.1. Si une feature doit changer, l'ancienne est marquée `@deprecated` mais continue de fonctionner jusqu'à la prochaine majeure (8.0).
2.  **Maintenance End** : Une fois le support terminé, SensioLabs propose un support commercial étendu.

## Ressources
*   [Symfony Release Process](https://symfony.com/doc/current/contributing/community/releases.html)
*   [Symfony Release Cycle](https://symfony.com/releases)
