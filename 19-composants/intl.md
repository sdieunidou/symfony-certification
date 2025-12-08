# Internationalisation (Composant Intl)

## Concept clé
Le composant **Intl** est une couche d'abstraction par-dessus l'extension PHP `intl` (qui utilise la librairie C ICU).
Il fournit l'accès aux données de localisation standardisées (Noms de pays, langues, devises) et aux formateurs.

## Accès aux Données (Data)
Plus besoin de stocker la liste des pays en base de données.

```php
use Symfony\Component\Intl\Countries;
use Symfony\Component\Intl\Languages;
use Symfony\Component\Intl\Currencies;
use Symfony\Component\Intl\Timezones;

// Noms traduits
$countries = Countries::getNames('fr'); // ['FR' => 'France', 'BE' => 'Belgique'...]
$lang = Languages::getName('de', 'fr'); // 'Allemand'

// Validation
$exists = Currencies::exists('BTC'); // false (standard ISO 4217)
```

## Polyfill
Le paquet `symfony/intl` pèse plusieurs méga-octets car il contient les données ICU (si l'extension native n'est pas disponible ou trop vieille).
Cependant, il est recommandé d'installer l'extension PHP `intl` pour la performance.

## Formulaires
Le composant Form utilise Intl pour les types :
*   `CountryType`
*   `LanguageType`
*   `CurrencyType`
*   `TimezoneType`

## Fonctionnement Interne

### Architecture
*   **ICU** : Le composant repose presque entièrement sur la librairie C **ICU** (International Components for Unicode) via l'extension PHP `intl`.
*   **ResourceBundle** : Les données (noms de pays, devises) sont stockées dans des fichiers binaires `.res` compilés par ICU, que Symfony lit.
*   **Fallback** : Si l'extension `intl` est absente, Symfony fournit (via composer `symfony/intl`) une version PHP pur avec des données extraites (fichiers PHP).

## 🧠 Concepts Clés
1.  **ICU** : International Components for Unicode. C'est le standard industriel.
2.  **Locale** : Les codes de locale utilisent le format `fr`, `fr_CA`, `zh_Hans_CN`.

## ⚠️ Points de vigilance (Certification)
*   **Traduction vs Intl** : Le composant `Translation` gère **vos** messages (`messages.yaml`). Le composant `Intl` fournit les données **standards** (Pays, Langues) déjà traduites.

## Ressources
*   [Symfony Docs - Intl](https://symfony.com/doc/current/components/intl.html)
