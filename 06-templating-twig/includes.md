# Inclusions et Embeds (Modularité)

## Concept clé
Twig offre plusieurs mécanismes pour réutiliser des fragments de template et éviter la duplication de code. Comprendre la différence entre `include`, `embed`, `render` et `hinclude` est crucial.

## 1. `include()` (La fonction)
Insère le contenu d'un autre template. Simple et rapide.
```twig
{{ include('partials/_header.html.twig') }}
{{ include('partials/_alert.html.twig', { 'message': 'OK' }) }}
```
*   **Contexte** : Hérite des variables par défaut. `with_context = false` pour isoler.

## 2. `{% embed %}` (Le caméléon)
Inclut un template tout en **surchargeant ses blocs**. Puissant pour les composants UI (Modales, Cards).
```twig
{% embed '_modal.html.twig' %}
    {% block body %}Contenu spécifique{% endblock %}
{% endembed %}
```

## 3. `render(controller())` (Le lourd)
Exécute un contrôleur PHP complet (sous-requête). Voir le fichier `controllers.md`.

## 4. Contenu Asynchrone (`hinclude.js`)
Pour les parties de page lentes (ex: Sidebar "Derniers commentaires", Panier, Widget Météo), on peut les charger en **AJAX** automatiquement après le chargement de la page principale.
Symfony utilise la bibliothèque `hinclude.js`.

### Utilisation
1.  Inclure `hinclude.js` (via AssetMapper ou script tag).
2.  Utiliser la fonction `render_hinclude` au lieu de `render`.

```twig
{# Génère une balise <hx:include src="..."> #}
{{ render_hinclude(controller('App\\Controller\\WidgetController::weather')) }}

{# Avec contenu par défaut (Spinner) en attendant le chargement #}
{{ render_hinclude(controller('...'), {
    default: 'loading.html.twig' 
}) }}

{# Ou texte par défaut #}
{{ render_hinclude(controller('...'), { default: 'Chargement...' }) }}
```

### Configuration
Si vous utilisez `controller()`, vous devez configurer le chemin des fragments dans `framework.yaml` (car l'URL est signée pour la sécurité).
```yaml
framework:
    fragments: { path: /_fragment }
```

## 🧠 Concepts Clés
1.  **Convention** : Préfixez les templates partiels par `_` (ex: `_form.html.twig`).
2.  **Hinclude vs ESI** :
    *   **Hinclude** : Client-side (AJAX). Le navigateur fait 2 requêtes. Bon pour l'expérience utilisateur si le widget est lent.
    *   **ESI** : Server-side (Varnish). Le proxy assemble la page. Plus complexe à mettre en place.

## ⚠️ Points de vigilance (Certification)
*   **Include missing** : `ignore_missing: true` permet d'éviter une erreur 500 si le template n'existe pas.
*   **Performance** : Trop de `render_hinclude` peut flooder le serveur de petites requêtes AJAX au chargement de la page.

## Ressources
*   [Symfony Docs - Hinclude](https://symfony.com/doc/current/templates.html#how-to-embed-asynchronous-content-with-hinclude-js)
