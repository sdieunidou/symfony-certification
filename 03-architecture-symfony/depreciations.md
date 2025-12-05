# Bonnes pratiques concernant les dépréciations

## Concept clé
Une **Dépréciation** est un signal émis par le framework pour prévenir : "Cette fonctionnalité fonctionne encore, mais sera supprimée dans la prochaine version majeure. Migrez maintenant."
C'est le moteur du **Continuous Upgrade Path**.

## Mécanisme Technique

### Émission (`trigger_deprecation`)
Depuis Symfony 5.1, une fonction globale standardisée est utilisée :

```php
// package, version, message, args
trigger_deprecation('symfony/http-kernel', '6.4', 'The "%s" method is deprecated, use "%s" instead.', 'oldMethod', 'newMethod');
```
Cela déclenche une erreur PHP de niveau `E_USER_DEPRECATED`.

### Détection
1.  **Web Profiler** : Icône "Dépréciations" dans la toolbar. Liste complète avec stack trace.
2.  **Logs** : Canalisées dans le channel `php`.
3.  **Tests (PHPUnit)** : Le `phpunit-bridge` capte ces erreurs et affiche un rapport en fin de suite.

## Gestion des Dépréciations (Cycle de vie)

### 1. En Développement
Soyez proactif. Corrigez les dépréciations de **votre code** (direct/self) immédiatement.
Pour les dépréciations venant de **vendors** (indirect), mettez à jour les paquets.

### 2. En Test (Configuration du Bridge)
Vous pouvez configurer la tolérance via la variable d'environnement `SYMFONY_DEPRECATIONS_HELPER`.

```bash
# Échoue si N'IMPORTE QUELLE dépréciation est détectée (Mode strict)
SYMFONY_DEPRECATIONS_HELPER=max[total]=0

# Tolère les dépréciations venant du dossier /vendor (Weak mode)
# Utile si une lib tierce n'est pas encore à jour pour Symfony 7
SYMFONY_DEPRECATIONS_HELPER=max[self]=0

# Désactive totalement (Déconseillé)
SYMFONY_DEPRECATIONS_HELPER=disabled
```

### 3. En Production
Les dépréciations ne doivent **JAMAIS** être affichées.
Le `error_reporting` de PHP en prod exclut souvent `E_USER_DEPRECATED`, mais il est conseillé de les logger dans un fichier séparé (via Monolog) pour préparer les futures migrations.

## Stratégie de Migration Majeure
Pour passer de Symfony 6.4 à 7.0 :
1.  Mettre à jour en 6.4 (dernière mineure).
2.  Lancer les tests et naviguer sur le site.
3.  S'assurer que le log des dépréciations est vide.
4.  Mettre à jour `composer.json` -> `"symfony/*": "^7.0"`.
5.  `composer update`.
6.  C'est fini (théoriquement).

## 🧠 Concepts Clés
1.  **Opt-in** : Utiliser une version majeure (ex: 7.0) c'est accepter de ne plus utiliser de code déprécié en 6.4.
2.  **Types** :
    *   **Direct** : Votre code appelle une méthode dépréciée.
    *   **Indirect** : Une librairie que vous utilisez appelle une méthode dépréciée.
    *   **Self** : Vous déclenchez une dépréciation pour vos propres utilisateurs.

## ⚠️ Points de vigilance (Certification)
*   **Silence** : L'opérateur `@` (silence) de PHP fonctionne sur `trigger_error` mais il est fortement déconseillé de l'utiliser pour masquer les dépréciations.
*   **WDT** : Savoir localiser l'onglet Deprecations dans le Web Debug Toolbar.

## Ressources
*   [Symfony Docs - Deprecations](https://symfony.com/doc/current/configuration/using_parameters_in_dic.html#making-services-public) (Note: lien contextuel, chercher "Deprecations" dans la doc officielle)
*   [PHPUnit Bridge Configuration](https://symfony.com/doc/current/components/phpunit_bridge.html#configuration)
