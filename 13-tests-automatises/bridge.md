# Pont PHPUnit (PHPUnit Bridge)

## Concept clé
Le **PHPUnit Bridge** est un composant essentiel qui enrichit PHPUnit pour l'écosystème Symfony.
Il ne sert pas qu'à installer PHPUnit, mais surtout à gérer la **Rétrocompatibilité** et les **Dépréciations**.

## Fonctionnalités Clés

### 1. Gestion des Dépréciations (`SYMFONY_DEPRECATIONS_HELPER`)
C'est la feature principale. Le bridge intercepte tous les appels `trigger_deprecation()` et génère un rapport à la fin des tests.
Il permet de rendre les tests "Deprecation-Free" (préparation migration majeure).

### 2. Mocks Temporels (`ClockMock`)
Permet de mocker les fonctions natives de temps (`time()`, `microtime()`, `date()`) si elles sont utilisées dans des classes namespacées.
*Note : Depuis Symfony 6.3 et le composant `Clock`, on préfère utiliser `ClockInterface` et `MockClock` plutôt que ce hack du bridge.*

### 3. Mocks Réseau (`DnsMock`)
Simule des réponses DNS pour `checkdnsrr()`.

### 4. Installation automatique
Le bridge installe une version de PHPUnit compatible avec votre version de PHP dans `bin/.phpunit/`. Cela isole votre projet de la version globale de PHPUnit.

## Application dans Symfony 7.0
Le bridge s'active via `composer require --dev symfony/phpunit-bridge`.
Configuration via `phpunit.xml.dist` :

```xml
<listeners>
    <listener class="Symfony\Bridge\PhpUnit\SymfonyTestsListener" />
</listeners>
```

## 🧠 Concepts Clés
1.  **Polyfills** : Historiquement, le bridge apportait des features des nouveaux PHPUnit aux vieux PHP. En Symfony 7 (PHP 8.2+), c'est moins utile car on utilise un PHPUnit récent.
2.  **Reporting** : À la fin de la suite de tests, un résumé rouge ou vert indique le nombre de dépréciations restantes.

## ⚠️ Points de vigilance (Certification)
*   **Mode Strict vs Weak** : Comprendre la variable d'env `max[total]=999` vs `max[self]=0`.
*   **Vendor** : Par défaut, les dépréciations dans `/vendor` sont ignorées (car ce n'est pas votre code).

## Ressources
*   [Symfony Docs - PHPUnit Bridge](https://symfony.com/doc/current/components/phpunit_bridge.html)
