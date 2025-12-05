# Syntaxe Twig

## Concept clé
Twig est un langage de templating conçu pour être lisible par les designers et puissant pour les développeurs.
Il repose sur 3 types de délimiteurs.

## Délimiteurs

### 1. `{{ ... }}` : Affichage (Output)
Affiche le résultat d'une expression (variable, fonction, calcul).
*   Équivalent PHP : `<?= ... ?>` + `htmlspecialchars()` (Auto-escaping).

```twig
{{ user.name }}
{{ 1 + 2 }}
{{ "now"|date }}
```

### 2. `{% ... %}` : Logique (Tags)
Exécute une commande (Contrôle, Héritage, définition de variable). N'affiche rien par défaut.

```twig
{% if active %}
{% for item in list %}
{% set foo = 'bar' %}
```

### 3. `{# ... #}` : Commentaires
Non rendus dans le HTML final.

```twig
{# Ceci est un commentaire secret pour les devs #}
<!-- Ceci est un commentaire HTML visible par le client -->
```

## L'Opérateur Point (`.`) "Magique"
L'accès aux données est unifié. Quand vous écrivez `foo.bar`, Twig essaie intelligemment :
1.  **Array** : `$foo['bar']`
2.  **Propriété** : `$foo->bar`
3.  **Méthode** : `$foo->bar()`
4.  **Getter** : `$foo->getBar()`
5.  **Isser** : `$foo->isBar()`
6.  **Hasser** : `$foo->hasBar()`
7.  **Dynamic** : `$foo->__call('bar')`

Cela permet de changer l'implémentation PHP (public property -> getter) sans changer le template.

## 🧠 Concepts Clés
1.  **Whitespace Control** : L'ajout d'un tiret `-` colle au délimiteur supprime les espaces blancs de ce côté.
    *   `{{- value -}}` : Supprime les espaces avant et après.
    *   Utile pour générer du JSON ou du texte précis.
2.  **Variables** : Définition via `{% set name = 'value' %}`.

## ⚠️ Points de vigilance (Certification)
*   **Strict** : Twig est strict sur la syntaxe. Pas de `$` devant les variables.
*   **Comparaison** : `==` (égalité), `and`, `or`, `not` (opérateurs littéraux).

## Ressources
*   [Twig Language Reference](https://twig.symfony.com/doc/3.x/templates.html)
