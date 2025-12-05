# Promesse de Rétrocompatibilité (Backward Compatibility Promise)

## Concept clé
La stabilité est la marque de fabrique de Symfony. La "BC Promise" est un contrat strict qui garantit que les mises à jour mineures (ex: 7.0 vers 7.1) ne casseront jamais votre application, tant que vous respectez les règles du jeu.

## Règles d'Or

### 1. Ce qui est couvert (Safe)
*   **API Publique** : Classes, Méthodes et Interfaces non marquées `@internal`.
*   **Comportement** : L'input/output et les effets de bord restent identiques.
*   **Configuration** : Les formats YAML/XML et les noms d'options.
*   **Commandes Console** : Noms et arguments.

### 2. Ce qui n'est PAS couvert (Unsafe)
*   **Classes `@internal`** : Code interne au framework, susceptible de changer à tout moment. Ne jamais les utiliser directement.
*   **Fonctionnalités `@experimental`** : En test (beta), peuvent changer ou être supprimées dans la prochaine mineure.
*   **Tests** : La structure des messages d'erreur ou du HTML généré par le Profiler peut changer (vos tests ne doivent pas dépendre du texte exact d'une erreur système).

## Nuances Techniques

### Interfaces et Classes Finales
*   **Ajout de méthode dans une Interface** : C'est un "BC Break" (car vos classes implémentant l'interface planteront). Symfony s'interdit de le faire en version mineure, sauf pour les interfaces marquées `@experimental`.
*   **Classes Finales** : Symfony rend de plus en plus de classes `final` pour pouvoir modifier leur implémentation interne sans casser les classes qui en hériteraient.

### Constructeurs
Le constructeur d'un service interne n'est **pas** couvert par la BC Promise si vous étendez ce service.
*   *Pourquoi ?* Symfony peut avoir besoin d'injecter une nouvelle dépendance dans le constructeur d'un service core pour fixer un bug ou ajouter une feature.
*   *Solution* : Utilisez la **Décoration** plutôt que l'Héritage pour étendre les services natifs.

## Continuous Upgrade Path
Cette promesse permet une stratégie de mise à jour sereine :
1.  **Mise à jour régulière** (7.0 -> 7.1 -> 7.2) : Sans risque, apporte des features et des performances.
2.  **Traitement des Dépréciations** : Chaque version mineure peut introduire des dépréciations (avertissements sans casse). Vous avez jusqu'à la version majeure suivante pour les corriger.
3.  **Saut Majeur** (7.4 -> 8.0) : Si vous avez corrigé toutes les dépréciations en 7.4, le passage à 8.0 est instantané car 8.0 est identique à 7.4 sans le code déprécié.

## 🧠 Concepts Clés
1.  **@internal** : Balise PHPDoc signifiant "Touche pas à ça".
2.  **SemVer** : Symfony respecte strictement le Semantic Versioning.
3.  **Deprecation Layer** : Code de compatibilité gardé temporairement pour faire fonctionner l'ancienne et la nouvelle méthode en même temps, le temps que vous migriez.

## ⚠️ Points de vigilance (Certification)
*   **Héritage** : Hériter d'une classe du Core est la source #1 de problèmes de BC (changements de propriétés protected, constructeur). Préférez toujours la **Composition/Décoration**.
*   **ParameterBag** : Les noms des paramètres de conteneur internes (ex: `router.options`) peuvent changer, bien que ce soit rare.

## Ressources
*   [Symfony BC Promise](https://symfony.com/doc/current/contributing/code/bc.html)
*   [Semantic Versioning](https://semver.org/)
