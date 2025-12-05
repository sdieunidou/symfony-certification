# Promesse de Rétrocompatibilité (BC Promise)

## Concept clé
Symfony garantit que les mises à jour mineures (ex: 7.0 vers 7.1) ne casseront jamais votre application existante, à condition que vous utilisiez les fonctionnalités stables (non taguées `@internal` ou `@experimental`).

## Règles
1.  **API Publique** : Les classes, interfaces et méthodes publiques sont couvertes.
2.  **Comportement** : Le comportement attendu ne change pas.
3.  **Exceptions** :
    *   Le code marqué `@internal` n'est pas couvert.
    *   Le code marqué `@experimental` peut changer.
    *   Les changements de bogues manifestes peuvent changer le comportement.

## Continuous Upgrade Path
Cette promesse permet le "Continuous Upgrade Path" :
1.  Mettre à jour régulièrement les versions mineures (facile).
2.  Traiter les dépréciations au fil de l'eau (les logs vous avertissent).
3.  Le saut de version majeure (ex: 7.4 -> 8.0) devient une formalité car le code est déjà prêt.

## Points de vigilance (Certification)
*   L'ajout d'une méthode à une interface casse la BC (car les classes qui l'implémentent doivent ajouter la méthode). Symfony évite cela dans les versions mineures, ou utilise des astuces (méthodes avec corps par défaut via traits, ou interfaces optionnelles).
*   Le constructeur n'est pas toujours couvert par la BC promise pour les services internes étendus par l'utilisateur (bien que Symfony fasse des efforts pour supporter les arguments optionnels).

## Ressources
*   [Symfony BC Promise](https://symfony.com/doc/current/contributing/code/bc.html)

