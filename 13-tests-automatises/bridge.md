# Pont PHPUnit (PHPUnit Bridge)

## Concept clé
Le `symfony/phpunit-bridge` améliore l'expérience de test de PHPUnit avec Symfony.
Fonctionnalités principales :
1.  **Gestion des dépréciations** : Signale si votre code utilise des fonctions dépréciées.
2.  **Polyfills** : Fournit des fonctionnalités de versions récentes de PHPUnit même sur d'anciennes versions (moins pertinent maintenant que Symfony requiert PHP récent).
3.  **Coverage** : Améliore la couverture de code.

## Application dans Symfony 7.0
Installé via `composer require --dev symfony/phpunit-bridge`.
Il crée un script `bin/phpunit` qui installe automatiquement la bonne version de PHPUnit.

### Configuration (phpunit.xml.dist)
```xml
<env name="SYMFONY_DEPRECATIONS_HELPER" value="max[self]=0" />
```

## Points de vigilance (Certification)
*   **Mode strict** : Le bridge peut faire échouer les tests si une dépréciation est déclenchée (`SYMFONY_DEPRECATIONS_HELPER=max[self]=0`).
*   **Weak Vendors** : Par défaut, il ignore les dépréciations provenant du dossier `vendor` (car vous ne pouvez pas les corriger).

## Ressources
*   [Symfony Docs - PHPUnit Bridge](https://symfony.com/doc/current/components/phpunit_bridge.html)

