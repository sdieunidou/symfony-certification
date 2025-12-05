# Variables Globales

## Concept clé
Les variables globales sont injectées automatiquement dans **tous** les templates Twig.
Elles évitent d'avoir à passer manuellement des données omniprésentes (User, Config, Request) depuis chaque contrôleur.

## La variable `app`
Symfony injecte une variable `app` (instance de `AppVariable`) qui donne accès au contexte applicatif.

*   `app.user` : L'objet User connecté (ou `null`).
*   `app.request` : L'objet Request courant.
*   `app.session` : La session.
*   `app.flashes` : Les messages flash (consommés à la lecture).
*   `app.environment` : L'environnement (ex: `dev`, `prod`).
*   `app.debug` : Booléen (mode debug actif ?).

Exemple :
```twig
{% if app.user %}
    Bonjour {{ app.user.userIdentifier }}
{% else %}
    <a href="{{ path('login') }}">Connexion</a>
{% endif %}

<body class="{{ app.environment }}">
```

## Définir vos propres globales
Vous pouvez définir des variables statiques ou des services comme globales dans `config/packages/twig.yaml`.

```yaml
twig:
    globals:
        # Valeur scalaire
        admin_email: 'contact@monsite.com'
        
        # Paramètre de service (via %...%)
        ga_tracking_id: '%ga_tracking_id%'
        
        # Service complet (via @...)
        # Attention à la performance : le service sera instancié à chaque page !
        cart_manager: '@App\Service\CartManager'
```

Usage :
```twig
<a href="mailto:{{ admin_email }}">Contact</a>
Total panier : {{ cart_manager.total }} €
```

## 🧠 Concepts Clés
1.  **Injection** : Les globales sont injectées avant le rendu du template.
2.  **Contexte** : Elles sont disponibles partout, y compris dans les templates inclus ou étendus.

## ⚠️ Points de vigilance (Certification)
*   **Performance** : Injecter un service lourd comme globale est une mauvaise pratique car il sera instancié sur toutes les pages (même une page d'erreur 404 ou une page statique). Préférez un Rendu de Contrôleur ou une Extension Twig (Lazy loading) pour les besoins dynamiques globaux.
*   **Surcharge** : Si vous passez une variable depuis le contrôleur avec le même nom qu'une globale (ex: `user`), la variable du contrôleur **écrase** la globale.

## Ressources
*   [Symfony Docs - Global Variables](https://symfony.com/doc/current/templates.html#global-variables)
