# Débogage Twig

## Concept clé
Développer des templates complexes nécessite de pouvoir inspecter les variables disponibles et de valider la syntaxe.

## Outils de Débogage

### 1. Fonction `dump()`
Nécessite le composant `VarDumper` (`composer require symfony/debug-bundle --dev`).
*   `{{ dump(user) }}` : Affiche la structure de la variable directement dans le HTML (Interactif).
*   `{% dump user %}` : Envoie la structure dans la **Web Debug Toolbar** (onglet Twig) sans polluer le visuel.
*   `{{ dump() }}` (sans argument) : Affiche **toutes** les variables disponibles dans le contexte courant.

### 2. Commande `debug:twig`
Liste tous les filtres, fonctions, tests et variables globales disponibles.
```bash
php bin/console debug:twig
php bin/console debug:twig --filter=date
# Voir quel fichier physique est chargé pour un namespace
php bin/console debug:twig @Twig/Exception/error.html.twig
```

### 3. Linter Twig (`lint:twig`)
Vérifie la syntaxe de vos templates (erreurs de parsing). Indispensable en CI/CD.
```bash
# Vérifier tout le dossier templates
php bin/console lint:twig templates/

# Vérifier un fichier spécifique
php bin/console lint:twig templates/emails/registration.html.twig

# Afficher les dépréciations (Symfony 7.3+)
php bin/console lint:twig --show-deprecations

# Exclure des dossiers (Symfony 7.1+)
php bin/console lint:twig templates/ --excludes=vendor
```

### 4. Web Debug Toolbar
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
1.  **Environnement** : `dump()` n'est disponible que si le `DebugBundle` est activé (généralement en `APP_ENV=dev`). En production, l'appel est ignoré ou supprimé à la compilation (si configuré correctement) ou provoque une erreur si la fonction n'existe pas.
2.  **Stopwatch** : Vous pouvez chronométrer des parties de template pour le Profiler.
    ```twig
    {% stop "menu_rendering" %}
        ...
    {% endstop %}
    ```

## ⚠️ Points de vigilance (Certification)
*   **Dump en Prod** : Si vous forcez l'affichage du dump en prod, vous exposez des données sensibles.
*   **Syntax Error** : Le linter ne vérifie que la syntaxe (fautes de frappe, tags fermants manquants), pas la logique (variables inexistantes).

## Ressources
*   [Symfony Docs - Debugging variables](https://symfony.com/doc/current/templates.html#debugging-variables)
*   [Symfony Docs - Linting Templates](https://symfony.com/doc/current/templates.html#linting-twig-templates)
