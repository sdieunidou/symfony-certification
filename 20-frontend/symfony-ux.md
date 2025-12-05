# Symfony UX & Stimulus

Symfony UX est une initiative visant à intégrer l'écosystème JavaScript de manière transparente dans Symfony, en utilisant **Stimulus** comme framework JS léger.

## 1. Stimulus : Le Framework

Stimulus est un framework JavaScript modeste pour le HTML que vous possédez déjà. Contrairement à React ou Vue qui contrôlent tout le DOM, Stimulus s'attache à des éléments existants via des attributs HTML.

### Concepts Clés
*   **Controller** : Une classe JS qui gère le comportement.
*   **Target** : Les éléments du DOM que le contrôleur manipule.
*   **Value** : Les données passées au contrôleur depuis le HTML.
*   **Action** : Les événements (click, change) qui déclenchent des méthodes du contrôleur.

### Exemple

```html
<!-- HTML (Twig) -->
<div data-controller="hello" data-hello-name-value="World">
    <input data-hello-target="output" type="text">
    <button data-action="click->hello#greet">Say Hello</button>
</div>
```

```javascript
// assets/controllers/hello_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['output'];
    static values = { name: String };

    greet() {
        this.outputTarget.value = `Hello, ${this.nameValue}!`;
    }
}
```

## 2. StimulusBundle

Ce bundle intègre Stimulus dans Symfony. Il charge automatiquement tous les contrôleurs présents dans `assets/controllers/`.

*   **Lazy Loading** : Les contrôleurs peuvent être chargés uniquement quand l'élément apparaît dans le viewport (`/* stimulusFetch: 'lazy' */`).
*   **Symfony Integration** : Configuration automatique via `assets/controllers.json` (pour les paquets tiers).

## 3. Symfony UX Components

Ce sont des paquets PHP/JS pré-configurés pour des fonctionnalités courantes d'interface utilisateur. Ils s'installent via Composer et configurent automatiquement Stimulus.

Exemples populaires :
*   **UX Turbo** : Transforme la navigation classique en navigation SPA (Single Page App) sans rechargement complet de page (via Turbo Drive).
*   **UX Live Component** : Composants dynamiques Twig/PHP qui se mettent à jour en temps réel (Ajax auto-géré).
*   **UX Chart.js** : Graphiques simples avec Chart.js.
*   **UX Cropperjs** : Recadrage d'images.
*   **UX Dropzone** : Upload de fichiers par glisser-déposer.

### Installation d'un composant UX
```bash
composer require symfony/ux-chartjs
npm install --force # Si utilisation de Webpack Encore
php bin/console importmap:require chart.js # Si utilisation de AssetMapper
```

## 4. Turbo (UX Turbo)

Turbo est un complément essentiel souvent utilisé avec Stimulus.

*   **Turbo Drive** : Intercepte les clics sur les liens et les soumissions de formulaires, charge le HTML via AJAX, et remplace le `<body>` sans recharger les scripts/CSS. Accélère la navigation.
*   **Turbo Frames** : Permet de mettre à jour uniquement une partie de la page (ex: une liste paginée) sans toucher au reste.
*   **Turbo Streams** : Permet de pousser des mises à jour HTML via WebSocket (avec Symfony Mercure) ou en réponse à une soumission de formulaire.

## 5. Intégration avec Twig

Symfony UX permet souvent de configurer les composants JS directement depuis PHP/Twig.

```php
// Dans le contrôleur PHP
$chart = $chartBuilder->createChart(Chart::TYPE_LINE);
$chart->setData([/* ... */]);

return $this->render('stats.html.twig', ['chart' => $chart]);
```

```twig
{# Dans Twig #}
{{ render_chart(chart) }}
```

Cela génère le HTML nécessaire et connecte automatiquement le contrôleur Stimulus associé.

