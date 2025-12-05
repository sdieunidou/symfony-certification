# Débogage du Code

## Concept clé
Outils pour introspecter le code en cours d'exécution.

## Application dans Symfony 7.0

### VarDumper
La fonction `dump()` remplace `var_dump()`.
*   En Web : Affiche dans la Toolbar ou en HTML stylisé.
*   En CLI : Affiche en couleur formatée.
*   Server : `server:dump` permet de rediriger les dumps vers une fenêtre terminal dédiée pour ne pas casser la sortie API/AJAX.

### Web Debug Toolbar (WDT)
Barre en bas de page donnant accès au Profiler.

### Profiler
Interface complète pour analyser la requête (Timeline, DB queries, Cache, Logs, Events).

## Points de vigilance (Certification)
*   **Performance** : Le Profiler consomme beaucoup de ressources. Il ne doit **jamais** être activé en Production.
*   **Dump** : Laisser un `dump()` dans le code peut casser la prod ou fuiter des infos. Utiliser le linter ou CI pour interdire `dump()` en prod.

## Ressources
*   [Symfony Docs - Debugging](https://symfony.com/doc/current/components/var_dumper.html)

