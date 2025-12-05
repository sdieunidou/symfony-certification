# Composant String

## Concept clé
Le composant **String** offre une approche orientée objet pour manipuler des chaînes de caractères, avec une gestion parfaite de l'**Unicode** (UTF-8).
Il résout les maux de tête des fonctions `mb_*` et `iconv`.

## Utilisation

On utilise la fonction helper `u()` pour créer un objet `AbstractString`.

```php
use function Symfony\Component\String\u;

$text = u('Bienvenue à Paris 🇫🇷');

// Manipulation
$slug = $text->ascii()->snake(); // "bienvenue_a_paris_fr"
$truncated = $text->truncate(10, '...'); // "Bienvenue..."

// Inspection
if ($text->containsAny(['Paris', 'Lyon'])) { ... }
if ($text->endsWith('🇫🇷')) { ... }

// Conversion
$length = $text->length(); // 18 (les emojis comptent pour 1 graphème)
```

## ByteString vs UnicodeString
*   **ByteString** : Pour les chaînes binaires ou ASCII pur (aléatoire, headers). Très rapide.
*   **UnicodeString** : Pour le texte humain. Gère les graphèmes complexes (emojis, accents).

## Slugger
Le composant fournit aussi un service `SluggerInterface` (basé sur `ascii()`) indispensable pour générer des URLs propres.

```php
$slug = $slugger->slug('Crème brûlée')->lower(); // "creme-brulee"
```

## Ressources
*   [Symfony Docs - String](https://symfony.com/doc/current/components/string.html)
