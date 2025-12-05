# Web Profiler et Data Collectors

## Concept clé
Le Profiler collecte des données pendant l'exécution de la requête via des **Data Collectors**.
Chaque panneau du profiler (Request, Time, DB, Logger) correspond à un Collector.

## Application dans Symfony 7.0
Vous pouvez créer vos propres collecteurs pour afficher des données métier dans la Debug Toolbar.
1.  Créer une classe implémentant `DataCollectorInterface`.
2.  Créer un template Twig pour le panneau.
3.  Configurer le service (tag `data_collector`).

## Points de vigilance (Certification)
*   **Late Collection** : La méthode `lateCollect()` permet de collecter des données *après* l'envoi de la réponse (utile pour les loggers qui flush à la fin).
*   **API** : On peut accéder aux données du profiler programmatiquement (utile dans les tests fonctionnels).

## Ressources
*   [Symfony Docs - Profiler](https://symfony.com/doc/current/profiler.html)

