# Traductions et Pluralisation (Twig)

## Concept clé
L'internationalisation (i18n) dans les vues repose sur le composant `Translation` et son intégration Twig.
Le but est de remplacer les textes statiques par des clés de traduction ou des objets traduisibles.

## Utilisation dans les Templates

### 1. Filtre `trans` (Recommandé)
C'est la méthode la plus courante pour les textes et les expressions.

```twig
<h1>{{ 'home.title'|trans }}</h1>

{# Avec paramètres (placeholders) #}
<p>{{ 'hello_user'|trans({'%name%': user.name}) }}</p>

{# Domaine spécifique (fichier messages par défaut) #}
<button>{{ 'delete'|trans({}, 'admin') }}</button>
```

### 2. Tag `{% trans %}`
Utile pour traduire des blocs statiques contenants des variables.

```twig
{% trans %}
    Hello %name%, welcome back!
{% endtrans %}
```
*Note : Dans le tag `trans`, les placeholders doivent utiliser la notation `%var%`.*

### 3. Domaine par défaut
Pour éviter de répéter le domaine dans tout le template :
```twig
{% trans_default_domain 'admin' %}

{{ 'delete'|trans }} {# Cherchera dans admin.fr.yaml #}
```

## Objets Traduisibles (`TranslatableMessage`)
Au lieu de traduire dans le contrôleur (ce qui nécessite d'injecter le Translator), vous pouvez renvoyer un objet `TranslatableMessage`. Twig le traduira automatiquement au rendu.

```php
// Controller
use Symfony\Component\Translation\TranslatableMessage;

public function index()
{
    // Le message n'est pas traduit ici, mais transporte ses paramètres
    $message = new TranslatableMessage('order.status', ['%status%' => 'shipped'], 'store');
    
    return $this->render('index.html.twig', ['status_message' => $message]);
}
```

```twig
{# Template #}
{{ status_message|trans }}
```

**`t()` shortcut** : Une fonction helper `t()` existe pour créer ces objets rapidement.

## Paramètres Globaux (Symfony 7.3+)
Vous pouvez définir des paramètres disponibles pour toutes les traductions (ex: nom de l'app).

```yaml
# config/packages/translation.yaml
framework:
    translator:
        globals:
            '%app_name%': 'My Super App'
```

## Pluralisation (ICU MessageFormat)
Depuis Symfony 6, le format recommandé est **ICU**.

### Dans le fichier YAML
```yaml
# translations/messages.fr.yaml
item_count: '{count, plural, =0 {Aucun article} one {1 article} other {# articles}}'
```

### Dans le template
```twig
{{ 'item_count'|trans({'count': cart.count}) }}
```

## Commandes Utiles
*   `php bin/console translation:extract --force fr` : Scanne les templates et met à jour les fichiers YAML.
*   `php bin/console debug:translation fr` : Affiche les traductions manquantes ou inutilisées.
*   `php bin/console lint:translations` : Vérifie la syntaxe des fichiers.
*   `php bin/console translation:pull loco` : Récupère les traductions d'un provider externe (Loco, Crowdin, etc.).

## 🧠 Concepts Clés
1.  **Locations** : Les fichiers sont stockés dans `translations/` (ex: `messages.fr.yaml`).
2.  **Fallback** : Si une clé n'existe pas dans la locale `fr_CA`, Symfony cherche dans `fr`, puis dans la locale de fallback (souvent `en`).
3.  **Pseudo-localization** : Pour tester l'UI avec des textes longs ou des caractères spéciaux, activez la pseudo-localization dans la config (transforme "Account" en "[!!! Àççôûñţ !!!]").

## ⚠️ Points de vigilance (Certification)
*   **Cache** : Après création d'un nouveau fichier de traduction, il faut vider le cache.
*   **Variables** : Le scanner (`extract`) ne détecte pas les clés dynamiques (`{{ status|trans }}`).

## Ressources
*   [Symfony Docs - Translations](https://symfony.com/doc/current/translation.html)
