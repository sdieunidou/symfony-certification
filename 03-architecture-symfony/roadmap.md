# Roadmap et Versions Symfony

La roadmap de Symfony est publique et définie des années à l'avance. Ce modèle prévisible permet aux développeurs et aux entreprises d'anticiper les migrations et de choisir la version adaptée à leurs besoins (Standard vs LTS). À ce jour (Décembre 2025), le cycle de la version 7 arrive à son apogée avec la sortie de la 7.4 LTS.

## État des Lieux (Décembre 2025)

En cette fin d'année 2025, l'écosystème Symfony vit un moment charnière avec la sortie simultanée de la nouvelle LTS (7.4) et de la prochaine majeure (8.0).

### Versions Maintenues

| Version | Date Sortie | Type | Version PHP | Fin Support Bug | Fin Support Sécu |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **6.4** | Nov 2023 | **LTS** | 8.1+ | Nov 2026 | Nov 2027 |
| **7.3** | Mai 2025 | Standard | 8.2+ | Jan 2026 | Jan 2026 |
| **7.4** | Nov 2025 | **LTS** | 8.2+ | **Nov 2028** | **Nov 2029** |
| **8.0** | Nov 2025 | Standard | 8.4+ | Juil 2026 | Jan 2027 |

### Versions Non Maintenues (Branche 7.x)

Ces versions ont permis d'introduire les fonctionnalités qui se retrouvent aujourd'hui stabilisées dans la 7.4.

| Version | Date Sortie | Statut |
| :--- | :--- | :--- |
| **7.0** | Nov 2023 | Fin de vie (Juillet 2024) |
| **7.1** | Mai 2024 | Fin de vie (Janvier 2025) |
| **7.2** | Nov 2024 | Fin de vie (Juillet 2025) |

## Cycle de Vie Symfony 7

La branche 7.x suit le cycle classique de Symfony :
1.  **7.0, 7.1, 7.2, 7.3** : Versions standards pour itérer rapidement sur les nouvelles features.
2.  **7.4** : Version de consolidation (LTS). C'est la version recommandée pour les nouveaux projets qui cherchent la stabilité sur le long terme.
3.  **8.0** : Version identique à la 7.4 mais débarrassée de la couche de compatibilité (code déprécié supprimé).

## Comment suivre les nouveautés ?

1.  **Symfony Blog** : Source officielle pour les articles "New in Symfony x.x".
2.  **Releases Page** : La page officielle [symfony.com/releases](https://symfony.com/releases) offre une vue temps réel du support.
3.  **Fichiers UPGRADE** : `UPGRADE-7.0.md`, `UPGRADE-7.4.md` dans le dépôt officiel détaillent les changements.

## 🧠 Concepts Clés

*   **Synchronisation 7.4 / 8.0** : Comme pour 6.4/7.0, Symfony 7.4 et 8.0 sont sorties simultanément.
    *   **7.4** = Features + Deprecations.
    *   **8.0** = Features - Deprecations (Nécessite PHP 8.4).
*   **Planning Fixe** : Pas de surprise, on sait qu'une mineure sort tous les 6 mois (Mai/Nov).

## ⚠️ Points de vigilance (Certification)

*   Pour la certification Symfony 7, concentrez-vous sur les fonctionnalités stabilisées dans la **7.4 LTS**.
*   Comprenez bien que passer de 7.4 à 8.0 ne demande généralement que de nettoyer les dépréciations ("Make it deprecation-free").
*   Retenez que les versions impaires (7.1, 7.3) et intermédiaires paires (7.2) ont une durée de vie courte (8 mois).

## Ressources
*   [Symfony Releases](https://symfony.com/releases)
*   [Symfony Roadmap](https://symfony.com/roadmap)
