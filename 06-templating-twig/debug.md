# Débogage Twig

## Concept clé
Développer des templates complexes nécessite de pouvoir inspecter les variables disponibles.

## Outils de Débogage

### 1. Fonction `dump()`
Nécessite le composant `VarDumper`.
*   `{{ dump(user) }}` : Affiche la structure de la variable directement dans le HTML (Interactif).
*   `{% dump user %}` : Envoie la structure dans la **Web Debug Toolbar** (onglet Twig) sans polluer le visuel.
*   `{{ dump() }}` (sans argument) : Affiche **toutes** les variables disponibles dans le contexte courant.

### 2. Commande `debug:twig`
Liste tous les filtres, fonctions, tests et variables globales disponibles.
```bash
php bin/console debug:twig
php bin/console debug:twig --filter=date
```

### 3. Web Debug Toolbar
L'icône Twig affiche :
*   Le temps de rendu.
*   Le graphe des templates (qui hérite de qui, qui inclut quoi).
*   La liste des variables passées au template.

## Commentaire Twig (`{# ... #}`)
Utilisez `{# ... #}` pour commenter du code. Contrairement aux commentaires HTML `<!-- ... -->`, le contenu n'est **pas** rendu dans le code source final (sécurité + propreté).

```twig
{# Ce code ne sera pas visible par l'utilisateur #}
{# {{ dump(secret_key) }} #}
```

## 🧠 Concepts Clés
1.  **Environnement** : `dump()` n'est disponible que si le `DebugBundle` est activé (généralement en `APP_ENV=dev`). En production, l'appel est ignoré ou supprimé à la compilation.
2.  **Stopwatch** : Vous pouvez chronométrer des parties de template.
    ```twig
    {% stop "menu_rendering" %}
        ...
    {% endstop %}
    ```

## ⚠️ Points de vigilance (Certification)
*   **Dump en Prod** : Si vous forcez l'affichage du dump en prod, vous exposez des données sensibles.
*   **Extension** : `dump` est une fonction fournie par l'extension `DebugExtension`.

## Ressources
*   [Symfony Docs - Debugging variables](https://symfony.com/doc/current/templates.html#debugging-variables)
