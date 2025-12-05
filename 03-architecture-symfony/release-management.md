# Release Management

## Concept clé
Symfony garantit un cycle de développement prévisible pour permettre aux entreprises de planifier leurs mises à jour.
C'est un modèle **"Time-based"** (basé sur le temps) et non "Feature-based".

## Le Calendrier
*   **Fréquence** : Une version mineure tous les 6 mois (Mai et Novembre).
*   **Majeure** : Tous les 2 ans.

## Types de Versions

### 1. Version Standard (Standard Support)
*   Exemples : 7.0, 7.1, 7.2, 7.3.
*   **Support Bug** : 8 mois.
*   **Support Sécurité** : 8 mois (jusqu'à la sortie de la suivante + un peu de marge).
*   **Cible** : Développeurs voulant les dernières features. Nécessite une mise à jour tous les 6 mois.

### 2. Version LTS (Long Term Support)
*   Exemples : 4.4, 5.4, 6.4, **7.4** (future).
*   C'est toujours la **dernière version mineure** de la branche (x.4).
*   **Support Bug** : 3 ans.
*   **Support Sécurité** : 4 ans (3+1).
*   **Cible** : Projets d'entreprise nécessitant de la stabilité à long terme.

## Correspondance Majeure/Mineure (Crucial)
**Symfony 7.0 == Symfony 6.4 (au niveau features).**
La seule différence est que la 7.0 a **supprimé** tout le code qui était déprécié en 6.4.
*   6.4 = Features + Code Déprécié (Compatible 6.0).
*   7.0 = Features - Code Déprécié.

## Processus de Migration
Le chemin recommandé est le "Continuous Upgrade".
1.  Rester à jour sur les mineures (6.1 -> 6.2 -> 6.3).
2.  Arrivé à la LTS (6.4), corriger toutes les **Deprecations**.
3.  Passer à la majeure suivante (7.0). Ça doit passer "tout seul".

## Symfony 7 et PHP
*   Symfony 6 nécessite PHP 8.1+.
*   Symfony 7 nécessite **PHP 8.2+**.

## 🧠 Concepts Clés
1.  **Backward Compatibility (BC)** : Garantie sur toutes les versions mineures d'une même branche. On ne casse rien entre 7.0 et 7.1.
2.  **Feature Freeze** : Période avant la sortie où plus aucune nouvelle fonctionnalité n'est acceptée, focus sur la stabilisation et les bugs.

## ⚠️ Points de vigilance (Certification)
*   Savoir calculer la date de fin de support.
*   Savoir quelle version est LTS (toujours la x.4).
*   Comprendre que passer de 6.4 à 7.0 n'apporte **aucune** nouvelle feature, juste du nettoyage et de la performance (moins de code legacy).

## Ressources
*   [Symfony Release Cycle](https://symfony.com/releases)
*   [Symfony Roadmap](https://symfony.com/roadmap)
