# Web Profiler et Data Collectors

## Concept clé
Le Profiler est l'outil de développement ultime. Il enregistre des informations détaillées sur chaque requête (Temps, DB, Cache, Logs, Auth).
Ces informations sont collectées par des **Data Collectors**.

## Accès aux Données (Tests)
En plus de l'interface Web (`/_profiler`), l'objet Profiler est accessible dans le code (notamment les tests fonctionnels).

```php
$client->enableProfiler();
$client->request('GET', '/');
$profile = $client->getProfile();

$dbCollector = $profile->getCollector('db');
echo $dbCollector->getQueryCount();
```

## Créer un Data Collector Personnalisé
Pour afficher des infos de votre application (ex: état d'une API tierce) dans la Toolbar.

1.  Créer une classe étendant `AbstractDataCollector`.
2.  Implémenter `collect(Request $r, Response $r)` pour stocker les données.
3.  Créer un template Twig pour la Toolbar et le Panel.
4.  Configurer le service avec le tag `data_collector`.

## 🧠 Concepts Clés
1.  **Stockage** : Les profils sont stockés (fichiers CSV/sérisalisés) dans `var/cache/dev/profiler`. Ils persistent entre les requêtes.
2.  **WDT** : La Web Debug Toolbar est injectée via un Listener (`WebDebugToolbarListener`) qui modifie la réponse HTML juste avant l'envoi (`</body>`).

## ⚠️ Points de vigilance (Certification)
*   **Désactivé** : Si le profiler est désactivé (`enable: false`), `$client->getProfile()` retourne null.
*   **Headers** : En cas d'appel API ou AJAX, la WDT n'est pas affichée, mais le lien vers le profiler est envoyé dans le header HTTP `X-Debug-Token-Link`.

## Ressources
*   [Symfony Docs - Profiler](https://symfony.com/doc/current/profiler.html)
*   [Create Custom Data Collector](https://symfony.com/doc/current/profiler/data_collector.html)
