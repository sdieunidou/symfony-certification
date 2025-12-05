# Release Management et Roadmap

## Concept clé
Symfony suit un calendrier de sortie strict et prévisible (Time-based release).
*   Deux versions mineures par an (Mai et Novembre).
*   Une version majeure tous les 2 ans.

## Le Cycle
1.  **Versions Standard** (ex: 7.0, 7.1, 7.2, 7.3) : Supportées pendant 8 mois (bug fixes) + 8 mois (security fixes) = **8 mois** en pratique pour le dev actif, nécessitant une mise à jour rapide.
2.  **Versions LTS** (Long Term Support) (ex: 5.4, 6.4) : Sortent tous les 2 ans (la dernière mineure de la branche, ex: x.4). Supportées pendant **3 ans** (bugs) + **1 an** (sécurité) = 4 ans au total.

## Symfony 7.0
*   Sortie : Novembre 2023.
*   Statut : Version Standard (pas LTS).
*   Pré-requis : Identique à Symfony 6.4 mais **sans le code déprécié**.

## Roadmap
*   Symfony 7.1 : Mai 2024
*   Symfony 7.2 : Nov 2024
*   Symfony 7.3 : Mai 2025
*   Symfony 7.4 (LTS) : Nov 2025

## Points de vigilance (Certification)
*   **Différence Majeure vs Mineure** :
    *   Mineure (7.0 -> 7.1) : Nouvelles fonctionnalités, rétrocompatible.
    *   Majeure (6.4 -> 7.0) : Suppression du code déprécié. Théoriquement pas de nouvelles fonctionnalités (7.0 = 6.4 - deprecations).
*   **Mise à jour** : Pour passer de 6.x à 7.0, il faut d'abord passer en 6.4, corriger toutes les dépréciations, puis passer en 7.0.

## Ressources
*   [Symfony Release Process](https://symfony.com/releases)

