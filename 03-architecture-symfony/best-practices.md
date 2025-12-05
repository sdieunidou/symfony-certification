# Bonnes Pratiques Officielles (Best Practices)

## Concept clé
Symfony maintient un guide de "Best Practices" pour éviter la paralysie du choix (Configuration vs Convention) et standardiser les développements.

## Application dans Symfony 7.0
Voici les règles d'or actuelles :

1.  **Autowiring** : Utiliser l'autowiring pour l'injection de dépendances. Ne pas configurer les services manuellement sauf nécessité.
2.  **Configuration** : Utiliser les variables d'environnement (`.env`) pour l'infrastructure (DB, Mailer) et les paramètres (`services.yaml` / `bind`) pour le comportement applicatif.
3.  **Logique métier** : Ne pas mettre de logique métier dans les contrôleurs. Utiliser des services dédiés.
4.  **Templates** : Utiliser Twig. Pas de PHP dans les vues.
5.  **Routing** : Utiliser les Attributs PHP (`#[Route]`) au-dessus des contrôleurs. (YAML et XML sont déconseillés pour le code applicatif, réservés aux bundles).
6.  **Entités** : Utiliser les Attributs PHP pour le mapping Doctrine.
7.  **Tests** : Utiliser PHPUnit et le `KernelTestCase` / `WebTestCase`.

## Points de vigilance (Certification)
L'examen teste souvent si vous connaissez la "manière recommandée" versus la "manière possible".
Exemple : "Comment configurer une route ?" -> Réponse recommandée : "Attributs". Réponse possible : "YAML, XML, PHP".
Autre exemple : "Où stocker les mots de passe DB ?" -> Variables d'environnement.

## Ressources
*   [Symfony Best Practices](https://symfony.com/doc/current/best_practices.html)

