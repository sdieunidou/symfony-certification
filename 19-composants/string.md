# Composant String

## Concept clé
Le composant **String** offre une approche orientée objet pour manipuler des chaînes de caractères, avec une gestion parfaite de l'**Unicode** (UTF-8).
Il résout les maux de tête des fonctions natives PHP (`str_*`, `mb_*`, `iconv`) en offrant une API fluide et cohérente.

## Installation
```bash
composer require symfony/string
```

## Classes & Création
Il existe 3 classes principales :
1.  **ByteString** : Traite la chaîne comme une suite d'octets. Rapide, idéal pour l'ASCII ou le binaire.
2.  **CodePointString** : Traite la chaîne comme une suite de points de code Unicode.
3.  **UnicodeString** : Traite la chaîne comme une suite de **graphèmes** (visuels). C'est la plus utilisée pour du texte humain (gère les émojis composés, accents combinés).

### Helpers (Fonctions raccourcis)
```php
use function Symfony\Component\String\u; // UnicodeString
use function Symfony\Component\String\b; // ByteString
use function Symfony\Component\String\s; // Auto-détection (ByteString si binaire, sinon UnicodeString)

$unicode = u('Bienvenue à Paris 🇫🇷');
$bytes = b('données binaires');
```

### Constructeurs Spéciaux
```php
use Symfony\Component\String\ByteString;

// Génération aléatoire (très utile pour tokens/mots de passe)
$token = ByteString::fromRandom(32); 
$pin = ByteString::fromRandom(4, '0123456789');
```

## Méthodes Principales (API Fluide)
L'API est immutable (chaque modification retourne un nouvel objet).

```php
$text = u('  Bienvenue à Paris 🇫🇷  ');

// Nettoyage et Casse
$text->trim(); // "Bienvenue à Paris 🇫🇷"
$text->lower(); // "bienvenue à paris 🇫🇷"
$text->upper(); // "BIENVENUE À PARIS 🇫🇷"
$text->camel(); // "bienvenueAParis🇫🇷"
$text->snake(); // "bienvenue_a_paris_🇫🇷"
$text->title(true); // "Bienvenue À Paris 🇫🇷" (Title Case)

// Manipulation
$text->truncate(10, '...'); // "Bienvenue..."
$text->replace('Paris', 'Lyon');
$text->append('!'); 
$text->prepend('Info: ');

// ASCII & Slug
$text->ascii(); // "  Bienvenue a Paris ??  " (Enlève les accents)

// Inspection
$text->length(); // 20 (compte les graphèmes, l'emoji compte pour 1)
$text->containsAny(['Paris', 'Lyon']); // true
$text->startsWith('Bienvenue'); // true
$text->endsWith('🇫🇷'); // true

// Découpage
$chunks = $text->chunk(5); // UnicodeString[]
$words = $text->split(' '); // UnicodeString[]
```

## Slugger
Le service `SluggerInterface` permet de générer des slugs d'URL propres en translittérant les caractères spéciaux.

```php
use Symfony\Component\String\Slugger\AsciiSlugger;

$slugger = new AsciiSlugger();
$slug = $slugger->slug('Crème brûlée 100%'); 
// Resultat : "Creme-brulee-100-percent" (Auto-détection de la locale pour % -> percent)

// Avec gestion des Emojis (Nouveau)
$slugger = $slugger->withEmoji();
$slug = $slugger->slug('I ❤️ Symfony');
// Resultat : "I-love-Symfony"
```

> **Note** : Dans une application Symfony, injectez `SluggerInterface` plutôt que d'instancier `AsciiSlugger`.

## Inflector (Pluriel / Singulier)
Permet de passer du singulier au pluriel (et inversement) pour l'anglais, le français et l'espagnol.

```php
use Symfony\Component\String\Inflector\EnglishInflector;
use Symfony\Component\String\Inflector\FrenchInflector;

$inflector = new EnglishInflector();
$inflector->pluralize('person'); // ['persons', 'people']
$inflector->singularize('news'); // ['news']

$frInflector = new FrenchInflector();
$frInflector->pluralize('cheval'); // ['chevaux']
```

## 🧠 Concepts Clés (Certification)
1.  **Immutabilité** : Les objets String sont immutables. `$u->append('a')` ne modifie pas `$u` mais retourne un nouvel objet.
2.  **Graphèmes vs Code Points** : `UnicodeString` travaille sur les graphèmes (ce que l'utilisateur voit).
    *   Exemple : `ñ` est 1 graphème, mais peut être 2 points de code (`n` + `~`). `length()` retournera 1.
3.  **Lazy Loading** : Certaines opérations coûteuses ne sont exécutées que si nécessaire (bien que ce soit transparent pour l'utilisateur).

## Ressources
*   [Symfony Docs - String](https://symfony.com/doc/current/components/string.html)
