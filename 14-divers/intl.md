# Internationalisation et Composant Intl

## Internationalisation (i18n)
Processus de rendre l'application traduisible.
Géré par le composant **Translation** (fichiers YAML/XLIFF, service `Translator`).

## Composant Intl
Fournit l'accès à la librairie ICU (International Components for Unicode) pour formater les données selon la locale, sans traduire manuellement.
*   Noms de langues (`Français`, `English`)
*   Noms de pays (`France`, `Belgique`)
*   Devises (`Euro`, `Dollar`)
*   Formatage de dates et nombres.

```php
use Symfony\Component\Intl\Languages;
use Symfony\Component\Intl\Countries;

$name = Languages::getName('fr', 'de'); // 'Französisch' (Nom du FR en Allemand)
$country = Countries::getName('FR', 'en'); // 'France'
```

## Points de vigilance (Certification)
*   **Composer** : Le composant `symfony/intl` doit être installé (`composer require intl`). Il contient une version polyfill des données ICU si l'extension PHP `intl` n'est pas installée (mais l'extension est recommandée).

## Ressources
*   [Symfony Docs - Intl](https://symfony.com/doc/current/components/intl.html)

