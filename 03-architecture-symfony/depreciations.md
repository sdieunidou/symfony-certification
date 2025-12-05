# Bonnes pratiques concernant les dépréciations

## Concept clé
Une "Dépréciation" (Deprecation) est un avertissement signalant qu'une fonctionnalité (classe, méthode, option) sera supprimée dans la prochaine version majeure. Elle reste fonctionnelle dans la version actuelle.

## Gestion dans Symfony
1.  **Détection** : Symfony émet des notices PHP (`E_USER_DEPRECATED`).
2.  **Web Profiler** : Un onglet "Deprecations" liste tous les appels dépréciés déclenchés par la page courante.
3.  **PHPUnit** : Le `phpunit-bridge` affiche un résumé des dépréciations à la fin des tests.

## Stratégie de migration
1.  Ne pas ignorer les avertissements.
2.  Corriger les dépréciations au fur et à mesure.
3.  Avant une migration majeure (ex: vers Symfony 8), s'assurer que le compteur de dépréciations est à zéro.

## Exemple
Si `MyService::oldMethod()` est déprécié :
```php
// Symfony 7.1 (hypothèse)
public function oldMethod() {
    trigger_deprecation('mon/paquet', '7.1', 'Use newMethod() instead.');
    return $this->newMethod();
}
```

## Points de vigilance (Certification)
*   **Silence** : En prod, les dépréciations ne doivent pas être affichées à l'écran, mais logguées.
*   **Weak Vendors** : Par défaut, le bridge PHPUnit peut être configuré pour ignorer les dépréciations venant du dossier `vendor` (que vous ne pouvez pas corriger vous-même), via `SYMFONY_DEPRECATIONS_HELPER`.

## Ressources
*   [Symfony Docs - Upgrading](https://symfony.com/doc/current/setup/upgrade_major.html)

