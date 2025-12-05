# Traductions et Pluralisation (Twig)

## Concept clé
L'internationalisation (i18n) dans les vues repose sur le composant `Translation` et son intégration Twig.
Le but est de remplacer les textes statiques par des clés de traduction.

## Utilisation

### 1. Filtre `trans` (Pour les textes courts)
C'est la méthode la plus courante.

```twig
<h1>{{ 'home.title'|trans }}</h1>

{# Avec paramètres (placeholders) #}
<p>{{ 'hello_user'|trans({'%name%': user.name}) }}</p>

{# Domaine spécifique (fichier messages par défaut) #}
<button>{{ 'delete'|trans({}, 'admin') }}</button>
```

### 2. Tag `{% trans %}` (Pour les blocs complexes)
Utile si le texte contient des variables entrelacées.

```twig
{% trans %}
    Hello %name%, welcome back!
{% endtrans %}
```

## Pluralisation (ICU MessageFormat)
Depuis Symfony 6, le format recommandé est **ICU** (International Components for Unicode). C'est un standard puissant géré par l'extension `intl`.

### Dans le fichier YAML (translations/messages.fr.yaml)
```yaml
# Format ICU
item_count: '{count, plural, =0 {Aucun article} one {1 article} other {# articles}}'
```

### Dans le template
```twig
{{ 'item_count'|trans({'count': cart.count}) }}
```
*Note : La variable utilisée pour le choix du pluriel doit être passée en paramètre.*

## 🧠 Concepts Clés
1.  **Scanner** : La commande `php bin/console translation:extract` scanne les templates Twig pour trouver les tags `trans` et créer/mettre à jour les fichiers YAML automatiquement.
2.  **Variable** : Si vous traduisez une variable (`{{ status|trans }}`), le scanner ne peut pas la détecter. C'est valide au runtime, mais vous devrez ajouter la clé manuellement dans le YAML.

## ⚠️ Points de vigilance (Certification)
*   **Legacy** : L'ancien format de pluralisation avec les pipes `|` et les intervalles `[0,1]` (ex: `one|many`) est supporté mais moins puissant que ICU.
*   **Domaine** : Si vous ne spécifiez pas de domaine, c'est `messages`.

## Ressources
*   [Symfony Docs - Translations in Templates](https://symfony.com/doc/current/translation.html#templates)
*   [ICU MessageFormat Syntax](https://symfony.com/doc/current/translation/message_format.html)
