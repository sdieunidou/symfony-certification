# Niveaux de Verbosité (Verbosity)

## Concept clé
La sortie d'une commande doit s'adapter au contexte : silencieuse pour un Cron, informative pour un utilisateur, bavarde pour le débogage.
Symfony gère cela via des drapeaux passés à la commande.

## Les Niveaux

| Option | Constante `OutputInterface` | Usage |
| :--- | :--- | :--- |
| **-q** (Quiet) | `VERBOSITY_QUIET` | Ne rien afficher (sauf erreurs script). Pour les logs CRON. |
| (Défaut) | `VERBOSITY_NORMAL` | Informations utiles et erreurs. |
| **-v** | `VERBOSITY_VERBOSE` | Plus de détails (ex: temps d'exécution, noms de fichiers créés). |
| **-vv** | `VERBOSITY_VERY_VERBOSE` | Infos très détaillées. |
| **-vvv** (Debug) | `VERBOSITY_DEBUG` | Tout. Affiche les Stack Traces complètes des exceptions. |

## Utilisation dans le Code

### 1. Conditionnelle (`if`)
```php
if ($output->isVerbose()) {
    // Affiché si -v, -vv ou -vvv
    $output->writeln('Connexion au serveur...');
}

if ($output->isDebug()) {
    // Affiché uniquement si -vvv
    $output->writeln('Memory: ' . memory_get_usage());
}
```

### 2. Argument de `write`
On peut passer le niveau requis directement à la méthode d'écriture.

```php
// S'affiche toujours
$output->writeln('Terminé.');

// S'affiche seulement si -v
$output->writeln('Détails...', OutputInterface::VERBOSITY_VERBOSE);
```

## 🧠 Concepts Clés
1.  **Quiet** : En mode `-q`, même les exceptions catchées par Symfony ne sont pas affichées. Le script retourne juste un code d'erreur (1).
2.  **Accumulation** : `isVerbose()` est vrai pour `-v`, `-vv` et `-vvv`. `isVeryVerbose()` est vrai pour `-vv` et `-vvv`.

## ⚠️ Points de vigilance (Certification)
*   **Exceptions** : Sans `-v`, une exception affiche juste le message d'erreur. Avec `-v`, on a la classe de l'exception. Avec `-vvv`, on a la stack trace complète. C'est le premier réflexe à avoir en cas de bug CLI.

## Ressources
*   [Symfony Docs - Verbosity](https://symfony.com/doc/current/console/verbosity.html)
