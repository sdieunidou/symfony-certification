# Débogage et VarDumper

## Concept clé
Symfony fournit des outils avancés pour inspecter l'état de l'application sans perturber son exécution ou l'affichage.

## Composant VarDumper
Remplace `var_dump()` par la fonction `dump()`.
*   **Formatage** : Affichage couleur, repliable (HTML), gestion des références circulaires.
*   **Cible** :
    *   Si Profiler actif : Le dump apparaît dans la **Debug Toolbar** (icône cible).
    *   Si CLI : Sortie standard formattée.
    *   Si Server Dump : Redirigé vers un serveur dédié.

### Server Dump (`server:dump`)
Très utile pour débugger des APIs ou des workers en arrière-plan où on ne voit pas la sortie HTML.
1.  Lancer le serveur : `php bin/console server:dump`
2.  Appeler `dump($var)` dans le code.
3.  Le résultat s'affiche dans le terminal du serveur, pas dans la réponse HTTP.

## Web Debug Toolbar (WDT)
Barre injectée en bas des pages HTML en mode `dev`.
Donne un aperçu immédiat :
*   Code HTTP / Temps de réponse / Mémoire.
*   Nombre de requêtes DB / Cache hits.
*   User connecté / Firewall.
*   Logs / Exceptions.

## Profiler
L'interface complète (accessible via la WDT ou `/_profiler`).
Permet de rejouer des requêtes passées, voir le graphe des services, l'arborescence Twig, etc.

## 🧠 Concepts Clés
1.  **DebugBundle** : Intègre ces outils dans le framework.
2.  **Stopwatch** : Le composant Stopwatch permet de mesurer le temps d'exécution de segments de code et de les afficher dans la Timeline du profiler.
    ```php
    $stopwatch->start('export');
    // ...
    $stopwatch->stop('export');
    ```

## ⚠️ Points de vigilance (Certification)
*   **Prod** : `dump()` ne doit jamais être utilisé en production. Le bundle `DebugBundle` est normalement dans `require-dev` de composer.json. Si vous laissez un `dump()` et que le bundle n'est pas là, c'est une erreur fatale "Call to undefined function dump()".
*   **Performance** : Le Profiler capture énormément de données. Il ralentit l'application.

## Ressources
*   [Symfony Docs - VarDumper](https://symfony.com/doc/current/components/var_dumper.html)
*   [Symfony Docs - Profiler](https://symfony.com/doc/current/profiler.html)
