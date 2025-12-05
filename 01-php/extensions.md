# Extensions PHP

## Concept clé
PHP est un langage modulaire ("Core" minimal + Extensions).
Les extensions fournissent la majorité des fonctionnalités standards. Elles peuvent être :
1.  **Bundled** : Compilées avec PHP par défaut (ex: `Standard`, `Date`, `Reflection`, `SPL`).
2.  **External** : Requiert une librairie externe et activation dans `php.ini` (ex: `Curl`, `OpenSSL`, `Intl`, `PDO`, `Mbstring`).
3.  **PECL** : Extensions communautaires installables via `pecl install` (ex: `Redis`, `Xdebug`, `Imagick`).

## Application dans Symfony 7.0
Symfony est très strict sur son environnement. Composer (`composer.json`) vérifie la présence des extensions requises via la section `require`.

### Extensions Critiques pour Symfony
| Extension | Usage dans Symfony | Risque si absente |
| :--- | :--- | :--- |
| **`intl`** | Internationalisation, Validation, Formattage (Dates, Monnaies). | Erreurs critiques, fallback lents ou comportements dégradés. |
| **`mbstring`** | Manipulation de chaînes Unicode (UTF-8). | Mauvaise gestion des caractères accentués/emojis. |
| **`xml` / `dom`** | Parsing des configurations (services.xml, routes.xml) et PHPUnit. | Impossible de démarrer le Kernel si config XML. |
| **`pdo`** | Abstraction DB (Doctrine). | Pas d'accès base de données. |
| **`ctype`** | Vérification de types de caractères (utilisé par le Validator/Form). | Requis par de nombreux composants (dépendance hard). |
| **`filter`** | Validation de données brutes (Request). | Failles de sécurité potentielles. |
| **`iconv`** | Conversion d'encodage (Transliteration, Slugger). | Problèmes de génération d'URLs (ASCII folding). |

## Polyfills
Symfony fournit d'excellents "Polyfills" (bibliothèques PHP pures) qui émulent le comportement des extensions manquantes.
*   Exemple : `symfony/polyfill-intl-icu` émule certaines fonctions de l'extension `intl` si elle n'est pas installée.
*   *Note : Les polyfills sont plus lents que les extensions natives (C vs PHP).*

## Points de vigilance (Certification)

### 1. OPcache (Zend Opcache)
*   **Indispensable en Production**.
*   Il cache le bytecode PHP compilé en mémoire partagée pour éviter de parser/compiler les scripts à chaque requête.
*   **Preloading (PHP 7.4+)** : Permet de charger des classes en mémoire au démarrage du serveur (PHP-FPM), offrant un gain de performance significatif pour les frameworks lourds comme Symfony.

### 2. Extensions de Sécurité & Crypto
*   **`openssl`** : Requis pour HTTPS, génération de clés, Composer.
*   **`sodium`** : (Intégré depuis 7.2) La librairie standard moderne pour la cryptographie (Argon2i pour les mots de passe, chiffrement symétrique). Préférée à Mcrypt (obsolète).

### 3. Débogage & Qualité
*   **`xdebug`** : Le standard pour le débogage pas-à-pas et le coverage de code. **NE JAMAIS ACTIVER EN PROD** (impact performance énorme).
*   **`pcov`** : Alternative légère à Xdebug pour générer le Code Coverage lors des tests.

### 4. Vérification au Runtime
Savoir vérifier si une extension est chargée :
```php
if (!extension_loaded('gd')) {
    throw new \RuntimeException("L'extension GD est requise pour le traitement d'images.");
}
```

## Ressources
*   [Manuel PHP - Liste des extensions](https://www.php.net/manual/fr/extensions.alphabetical.php)
*   [Symfony Requirements](https://symfony.com/doc/current/setup.html#technical-requirements)
*   [PECL Repository](https://pecl.php.net/)
