# Gestion du Code Déprécié (Legacy Tests)

## Concept clé
Un test bien écrit doit non seulement vérifier que le code fonctionne, mais aussi qu'il n'utilise pas de fonctionnalités dépréciées (pour préparer l'avenir).
À l'inverse, si vous testez une fonctionnalité legacy que vous maintenez, vous devez pouvoir dire "Je sais que c'est déprécié, c'est normal".

## `expectDeprecation()`
Le trait `Symfony\Bridge\PhpUnit\ExpectDeprecationTrait` permet d'asserter qu'un appel va générer une dépréciation spécifique.

```php
public function testLegacyFunction(): void
{
    $this->expectDeprecation('Since my-package 1.2: The "foo()" method is deprecated, use "bar()" instead.');
    
    $myObject->foo(); // Si foo() ne déclenche pas la dépréciation, le test échoue.
}
```

## Groupe `@group legacy`
Si un test utilise du code déprécié mais que vous ne voulez pas utiliser `expectDeprecation` (ou qu'il y en a trop), marquez le test avec l'annotation `@group legacy`.
Le Bridge PHPUnit sera plus tolérant avec ces tests.

## Configuration Globale (`SYMFONY_DEPRECATIONS_HELPER`)
Variable d'environnement (dans `phpunit.xml`) pour contrôler la sévérité globale.
*   `max[self]=0` : Aucune dépréciation tolérée dans votre code (`src/`), mais tolérance pour les bibliothèques tierces (`vendor/`). **C'est la configuration recommandée.**
*   `disabled` : Désactive tout rapport.

## 🧠 Concepts Clés
1.  **Silence** : Le bridge rend les tests bruyants (rapports) pour vous forcer à agir.
2.  **Trigger** : Votre code déclenche des dépréciations via `trigger_deprecation()`.

## ⚠️ Points de vigilance (Certification)
*   **Direct vs Indirect** : Le bridge distingue les dépréciations causées par votre code (Direct) de celles causées par des appels internes du framework (Indirect).

## Ressources
*   [Symfony Docs - Deprecation Helper](https://symfony.com/doc/current/components/phpunit_bridge.html#configuration)
