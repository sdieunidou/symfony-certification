# Composant YAML

## Concept clé
Le composant **YAML** charge et dump des fichiers YAML.
YAML (*YAML Ain't Markup Language*) est un standard de sérialisation de données lisible par les humains, très utilisé pour la configuration Symfony.
Ce composant est rapide, dispose d'un vrai parser (subset de la spec YAML) et produit des messages d'erreurs clairs.

## Installation
```bash
composer require symfony/yaml
```

## Utilisation de base

Le composant expose deux classes principales (`Parser`, `Dumper`) et une façade statique `Yaml` qui simplifie l'usage.

### Lire (Parsing)

```php
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Exception\ParseException;

try {
    // Parse une chaîne
    $value = Yaml::parse('foo: bar'); // ['foo' => 'bar']

    // Parse un fichier
    $config = Yaml::parseFile('/path/to/file.yaml');
    
} catch (ParseException $exception) {
    printf('Erreur YAML ligne %d : %s', $exception->getParsedLine(), $exception->getMessage());
}
```

### Écrire (Dumping)

```php
$array = [
    'foo' => 'bar',
    'bar' => ['baz' => 'qux']
];

// Dump en chaîne YAML
$yaml = Yaml::dump($array);

file_put_contents('/path/to/file.yaml', $yaml);
```

#### Options de Dump
`Yaml::dump($array, $inline, $indent, $flags)`

1.  **Inline Level** : Niveau d'imbrication avant de passer en syntaxe "inline" (`{ foo: bar }`).
    *   `Yaml::dump($array, 1)` (défaut) : Tout est développé sauf le niveau 1.
    *   `Yaml::dump($array, 2)` : Développe jusqu'au niveau 2.
2.  **Indentation** : Nombre d'espaces (défaut 4).

## Drapeaux (Flags) - Usage Avancé

Les méthodes `parse()` et `dump()` acceptent des drapeaux binaires pour modifier le comportement.

### Parsing
*   `Yaml::PARSE_OBJECT` : Désérialise en objets `stdClass` au lieu de tableaux.
*   `Yaml::PARSE_OBJECT_FOR_MAP` : Force `stdClass` même pour les maps YAML.
*   `Yaml::PARSE_DATETIME` : Convertit automatiquement les dates en objets `DateTime`.
*   `Yaml::PARSE_CONSTANT` : Parse les constantes PHP `!php/const App\Entity\User::ROLE_ADMIN`.
*   `Yaml::PARSE_CUSTOM_TAGS` : Support des tags personnalisés (`!my_tag`).

### Dumping
*   `Yaml::DUMP_OBJECT` : Dump les objets PHP.
*   `Yaml::DUMP_OBJECT_AS_MAP` : Dump les objets comme des maps YAML (propriétés publiques).
*   `Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK` : Utilise le style `|` pour les chaînes multi-lignes (plus lisible).
*   `Yaml::DUMP_NULL_AS_TILDE` : Dump `null` comme `~` au lieu de `null`.
*   `Yaml::DUMP_EMPTY_ARRAY_AS_SEQUENCE` : Dump `[]` comme `[]` (sequence) au lieu de `{}` (map).
*   `Yaml::DUMP_NUMERIC_KEY_AS_STRING` : Force les clés numériques à être des chaînes (`'200': foo`).

## Syntaxe Supportée
*   **Types** : Strings, Integers, Floats, Booleans, Null, Dates.
*   **Collections** : Sequences (`- item`) et Maps (`key: value`).
*   **Commentaires** : `# commentaire`.
*   **Multi-lignes** : `|` (conserve les retours à la ligne) et `>` (plie les retours à la ligne).
*   **Nombres lisibles** : `10_000_000` (les underscores sont ignorés).

## Linting (Validation)
Le composant fournit une commande pour valider la syntaxe des fichiers YAML sans exécuter l'application.

```bash
# Requiert symfony/console
php bin/console lint:yaml config/
```

## ⚠️ Points de vigilance (Certification)
*   **Indentation** : YAML interdit les tabulations. Utilisez des espaces.
*   **Performance** : Le parsing YAML est coûteux. En prod, Symfony cache le résultat du parsing en PHP (via le Config Cache).
*   **Null** : En YAML standard, `~` et `null` sont équivalents.

## Ressources
*   [Symfony Docs - YAML](https://symfony.com/doc/current/components/yaml.html)
*   [YAML Specification](https://yaml.org/)

