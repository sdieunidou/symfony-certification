# Roadmap et Versions Symfony

*Voir [Release Management](./release-management.md) pour les détails techniques.*

## Versions Actuelles (Contexte Examen)
*   **Symfony 7.0** (Nov 2023) : Version stable courante. Supporte PHP 8.2+.
*   **Symfony 6.4** (Nov 2023) : Version LTS courante. Supporte PHP 8.1+.

## Calendrier Prévisionnel
Symfony est une horloge suisse.

| Version | Date Sortie | Type | Fin Support Bug | Fin Support Sécu |
| :--- | :--- | :--- | :--- | :--- |
| **6.4** | Nov 2023 | **LTS** | Nov 2026 | Nov 2027 |
| **7.0** | Nov 2023 | Standard | Juil 2024 | Jan 2025 |
| **7.1** | Mai 2024 | Standard | Jan 2025 | Juil 2025 |
| **7.2** | Nov 2024 | Standard | Juil 2025 | Jan 2026 |
| **7.3** | Mai 2025 | Standard | Jan 2026 | Juil 2026 |
| **7.4** | Nov 2025 | **LTS** | Nov 2028 | Nov 2029 |

## Comment suivre les nouveautés ?
1.  **Symfony Blog** : Source officielle ("Living on the Edge", "New in Symfony x.x").
2.  **Changlogs** : Fichiers `CHANGELOG.md` dans chaque composant.
3.  **Upgrade Files** : `UPGRADE-7.0.md` décrit les cassures de BC (normalement nulles) et les changements majeurs.

## 🧠 Concepts Clés
*   **LTS (Long Term Support)** : Tous les 2 ans (x.4).
*   **Standard** : Tous les 6 mois.
*   **Synchronisation** : La première version de la branche N (7.0) sort en même temps que la dernière version de la branche N-1 (6.4). Elles ont les mêmes features.

## ⚠️ Points de vigilance (Certification)
*   L'examen se base souvent sur la version stable courante (7.0/7.1).
*   Il faut savoir que 7.0 = 6.4 sans dépréciations.

## Ressources
*   [Symfony Roadmap](https://symfony.com/roadmap)
