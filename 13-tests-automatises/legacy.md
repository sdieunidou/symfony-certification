# Gestion du Code Déprécié (Tests)

## Concept clé
Comment tester une application qui génère des dépréciations sans que les tests n'échouent (ou au contraire, pour s'assurer qu'on n'en génère pas) ?

## Application dans Symfony 7.0
Le `SYMFONY_DEPRECATIONS_HELPER` variable d'environnement contrôle ce comportement.

*   `disabled=1` : Ignore tout.
*   `max[total]=999` : Tolère 999 dépréciations.
*   `max[self]=0` : Tolère 0 dépréciation dans VOTRE code (src/), mais ignore les vendors. (Recommandé).
*   `weak` : Affiche les dépréciations mais ne fait pas échouer les tests.

### Tester une dépréciation attendue
Si vous écrivez une librairie et que vous voulez tester qu'une méthode déclenche bien une dépréciation (pour prévenir vos utilisateurs) :
```php
/**
 * @group legacy
 */
public function testLegacyFeature(): void
{
    $this->expectDeprecation('Since my-lib 1.0: Use newMethod() instead.');
    $myObject->oldMethod();
}
```

## Points de vigilance (Certification)
*   **@group legacy** : Marquer un test comme legacy permet au Bridge de savoir que ce test a le droit de générer des dépréciations.

## Ressources
*   [Symfony Docs - Testing Deprecations](https://symfony.com/doc/current/components/phpunit_bridge.html#configuration)

