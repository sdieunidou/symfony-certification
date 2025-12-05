# Objet Profiler (Tests Fonctionnels)

## Concept clé
Lors des tests fonctionnels, vous n'avez pas d'yeux pour voir la "Web Debug Toolbar".
Cependant, vous pouvez accéder à l'objet `Profiler` pour inspecter les métadonnées de la requête :
*   Combien de requêtes SQL ?
*   Est-ce qu'un email a été envoyé ?
*   Quelles exceptions ont été levées ?

## Activation
Le profiler doit être activé dans la configuration de test (`config/packages/test/web_profiler.yaml` : `enabled: true`).

```php
// Dans le test
$client->enableProfiler();
```

## Récupération des Données
```php
$client->request('POST', '/register');

// Récupérer le profil de la requête
$profile = $client->getProfile();

if ($profile) {
    // 1. Collector Mailer
    $mailCollector = $profile->getCollector('mailer');
    $this->assertEquals(1, $mailCollector->getMessageCount());
    
    $email = $mailCollector->getEvents()->getMessages()[0];
    $this->assertInstanceOf(Email::class, $email);
    $this->assertEquals('Bienvenue', $email->getSubject());

    // 2. Collector Database (Doctrine)
    $dbCollector = $profile->getCollector('db');
    $this->assertLessThan(5, $dbCollector->getQueryCount());
}
```

## 🧠 Concepts Clés
1.  **Collectors** : Le profiler est composé de collecteurs de données (`db`, `mailer`, `time`, `security`, `twig`...).
2.  **Historique** : Si la page redirige, `$client->getProfile()` retourne le profil de la *dernière* requête (la page d'atterrissage).

## ⚠️ Points de vigilance (Certification)
*   **Performance** : Le profiler ralentit les tests. Ne l'activez que si nécessaire.
*   **API** : Fonctionne aussi pour les APIs JSON (le profiler collecte les données même si la toolbar n'est pas affichée).

## Ressources
*   [Symfony Docs - Profiling in Tests](https://symfony.com/doc/current/testing/profiling.html)
