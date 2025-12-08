# Composant Translation

## Concept clé
Le composant **Translation** gère l'internationalisation (i18n) de l'application. Il permet d'abstraire les chaînes de caractères (messages) pour les afficher dans la langue de l'utilisateur (Locale).

Il supporte de nombreux formats de fichiers (YAML, XLIFF, PHP, etc.) et gère la pluralisation complexe via le standard **ICU MessageFormat**.

## Installation
```bash
composer require symfony/translation
```

## Utilisation

### 1. Basique (`trans`)
Le service `TranslatorInterface` est le point d'entrée.

```php
use Symfony\Contracts\Translation\TranslatorInterface;

public function index(TranslatorInterface $translator): Response
{
    // Traduit la clé 'hello' dans la locale courante
    $message = $translator->trans('hello');
    
    // Avec paramètres (ICU)
    $message = $translator->trans('hello_user', ['name' => 'Fabien']);
    
    return new Response($message);
}
```

### 2. Dans Twig
```twig
{# Filtre (Recommandé pour les variables) #}
{{ 'hello'|trans }}
{{ 'hello_user'|trans({'name': user.name}) }}

{# Tag (Pour les blocs de texte statique) #}
{% trans %}hello{% endtrans %}
```

### 3. Translatable Objects (Symfony 5.3+)
Pour retarder la traduction (ex: dans une entité ou une exception où le Translator n'est pas disponible), on utilise un objet porteur de message.

```php
use Symfony\Component\Translation\TranslatableMessage;
use function Symfony\Component\Translation\t; // Helper raccourci

// Crée l'objet sans traduire immédiatement
$error = new TranslatableMessage('user.not_found', ['%id%' => $id], 'validators');

// Ou avec la fonction t()
$error = t('user.not_found', ['%id%' => $id], 'validators');

// Sera traduit automatiquement lors du rendu dans Twig : {{ error|trans }}
```

## Formats de Ressources
Symfony cherche les traductions dans `translations/domain.locale.format` (ex: `messages.fr.yaml`).

### YAML (Recommandé pour les messages simples)
```yaml
# translations/messages.fr.yaml
hello: Bonjour
hello_user: Bonjour {name}
```

### XLIFF (Standard industriel)
Recommandé pour l'échange avec des outils de traduction professionnels.
```xml
<trans-unit id="hello">
    <source>hello</source>
    <target>Bonjour</target>
</trans-unit>
```

## Fonctionnement Interne

### Architecture
*   **Translator** : Le service central qui orchestre tout.
*   **Loader** : Classes responsables de parser les fichiers (`YamlFileLoader`, `XliffFileLoader`).
*   **Catalogue (`MessageCatalogue`)** : Un objet contenant toutes les traductions chargées pour une locale et ses fallbacks.
*   **Formatter** : Remplace les variables et gère la pluralisation (souvent via `IntlMessageFormatter`).

### Le Flux de Traduction
1.  **Locale Determination** : Le `LocaleListener` détermine la langue de la requête (`$request->getLocale()`) et configure le Translator.
2.  **Loading** : Au premier appel, le Translator charge les ressources depuis le disque pour la locale demandée ET les locales de fallback (ex: `fr_FR` -> `fr` -> `en`).
3.  **Caching** : Le catalogue complet est mis en cache (fichier PHP) pour la perf.
4.  **Lookup** : Le Translator cherche l'ID du message dans le catalogue.
5.  **Formatting** : Si trouvé, il injecte les paramètres (`{name}`) via le Formatter.

## 🧠 Concepts Clés
1.  **Domaines** : Les messages sont groupés par domaine (fichier). Par défaut `messages`. Les validateurs sont dans `validators`, la sécurité dans `security`.
2.  **ICU MessageFormat** : Symfony utilise le standard ICU pour gérer les pluriels complexes.
    *   `{count, plural, =0 {Aucune pomme} one {Une pomme} other {# pommes}}`.
3.  **Fallback** : Si une clé n'existe pas en `fr_FR`, Symfony cherche en `fr`, puis dans la locale par défaut (`en`).

## ⚠️ Points de vigilance (Certification)
*   **Extraction** : La commande `php bin/console translation:extract fr --force` permet de scanner le code (PHP/Twig) pour générer les fichiers de traduction manquants automatiquement.
*   **Interface** : Toujours typer avec `Symfony\Contracts\Translation\TranslatorInterface`, pas l'implémentation concrète.
*   **Performance** : Ne jamais traduire dans une boucle si possible. Les catalogues sont chargés paresseusement, mais le formatting a un coût.

## Ressources
*   [Symfony Docs - Translation](https://symfony.com/doc/current/translation.html)
*   [ICU MessageFormat](https://symfony.com/doc/current/translation/message_format.html)
