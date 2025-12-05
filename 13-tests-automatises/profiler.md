# Objet Profiler (Tests)

## Concept clé
Lors des tests fonctionnels, on a accès au Profiler Symfony pour vérifier des données techniques invisibles dans le HTML (Emails envoyés, Requêtes SQL, Cache).

## Application dans Symfony 7.0
Le profilage doit être activé en environnement de test (`config/packages/test/web_profiler.yaml`).

```php
$client->enableProfiler(); // S'assurer qu'il est actif

$client->request('POST', '/contact');

// Récupérer le profil de la dernière requête
$profile = $client->getProfile();

if ($profile) {
    // Vérifier le collector Mailer
    $mailCollector = $profile->getCollector('mailer');
    $this->assertEquals(1, $mailCollector->getMessageCount());

    // Vérifier le collector DB
    $dbCollector = $profile->getCollector('db');
    $this->assertLessThan(10, $dbCollector->getQueryCount());
}
```

## Points de vigilance (Certification)
*   **Disponibilité** : Si `$client->getProfile()` retourne `false`, c'est que le profiler est désactivé (souvent pour gagner en perf dans la CI).
*   **Redirection** : Si la requête redirige, le client suit la redirection (selon config). Le profil récupéré est celui de la *dernière* requête. Pour inspecter la requête *avant* redirection, il faut désactiver `followRedirects` ou naviguer dans l'historique des profils.

## Ressources
*   [Symfony Docs - Profiling in Tests](https://symfony.com/doc/current/testing/profiling.html)

