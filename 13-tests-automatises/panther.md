# Tests E2E avec Panther
s
## Concept Clé
`symfony/panther` est une bibliothèque de tests de navigateur (Browser Testing) et de scraping web pour PHP.
Elle utilise le protocole WebDriver (via ChromeDriver ou GeckoDriver) pour piloter un **vrai navigateur** (Chrome ou Firefox).

Contrairement à `WebTestCase` (qui simule un navigateur en PHP, sans exécuter le JS), **Panther exécute le JavaScript**.

## Installation
```bash
composer require --dev symfony/panther
```

## Utilisation

Hériter de `PantherTestCase` au lieu de `WebTestCase`.

```php
use Symfony\Component\Panther\PantherTestCase;

class MyJsAppTest extends PantherTestCase
{
    public function testMyJsFeature(): void
    {
        // 1. Créer le client Panther (lance le serveur web interne + ChromeDriver)
        $client = static::createPantherClient(); 
        // Options : ['browser' => static::FIREFOX]

        // 2. Naviguer (Vrai navigateur)
        $client->request('GET', '/javascript-page');

        // 3. Attendre que le JS s'exécute (Fonctionnalité clé !)
        // Attendre qu'un élément apparaisse
        $client->waitFor('.js-loaded-element');
        
        // Ou attendre une condition
        $client->waitForVisibility('#modal');

        // 4. Assertions (API similaire à WebTestCase)
        $this->assertSelectorTextContains('#result', 'Calculé par JS');
        
        // 5. Interaction
        $client->executeScript('document.querySelector("h1").style.color = "red";');
        $client->takeScreenshot('screen.png');
    }
}
```

## Différences avec WebTestCase

| Feature | WebTestCase (BrowserKit) | Panther |
| :--- | :--- | :--- |
| **Moteur** | PHP (Simulation) | Vrai Navigateur (Chrome/Firefox) |
| **JavaScript** | ❌ Non exécuté | ✅ Exécuté |
| **Vitesse** | 🚀 Très rapide | 🐢 Plus lent |
| **Captures** | ❌ Non | ✅ Screenshots possibles |
| **Usage** | 90% des tests fonctionnels | Tests critiques avec JS complexe (React/Vue) |

## 🧠 Concepts Clés
1.  **API Unifiée** : Panther implémente l'interface `Client` et utilise `DomCrawler`. Si vous savez utiliser `WebTestCase`, vous savez utiliser Panther.
2.  **Serveur Web** : Panther lance automatiquement le serveur web interne de PHP (`php -S`) pour servir l'application au navigateur.

## ⚠️ Points de vigilance (Certification)
*   **Dépencances** : Nécessite les drivers (ChromeDriver) installés sur la machine (ou gérés par `dbrekelmans/bdi`).
*   **Base de données** : Comme Panther lance un processus serveur séparé, la gestion des transactions de test (`DAMADoctrineTestBundle`) peut nécessiter une configuration spécifique (DATABASE_URL doit être accessible par le serveur web).

## Ressources
*   [Symfony Panther GitHub](https://github.com/symfony/panther)
