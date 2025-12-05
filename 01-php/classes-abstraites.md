# Classes Abstraites

## Concept clé
Une classe abstraite est une classe qui ne peut pas être instanciée directement. Elle sert de **modèle** et de **contrat partiel** pour d'autres classes. 

Elle se distingue par deux capacités :
1.  **Contrat (Abstraction)** : Elle peut définir des méthodes `abstract` (signature uniquement) que les enfants **doivent** implémenter.
2.  **Factorisation (Concret)** : Elle peut contenir des méthodes concrètes (avec implémentation), des propriétés et des constantes que les enfants héritent.

## Application dans Symfony 7.0
Symfony utilise abondamment les classes abstraites pour appliquer le principe **DRY (Don't Repeat Yourself)** et le **Template Method Pattern**.

Exemples majeurs :
*   **`AbstractController`** : Fournit toutes les méthodes utilitaires (`render`, `json`, `createForm`, `getUser`).
*   **`AbstractType`** : Classe de base pour les formulaires, avec une implémentation par défaut de `configureOptions` et `getParent`.
*   **`Command` (Symfony Console)** : Bien que non nommée "Abstract", elle agit comme telle.

## Exemple de code : Template Method Pattern

Ce pattern est le cas d'usage roi des classes abstraites : l'algorithme global est défini dans le parent, mais certaines étapes spécifiques sont déléguées aux enfants.

```php
<?php

namespace App\Export;

use Symfony\Component\Filesystem\Filesystem;

abstract class AbstractFileExporter
{
    public function __construct(
        // PHP 8.0 : Constructor promotion
        protected Filesystem $filesystem,
        protected string $exportDir
    ) {}

    // Méthode "Template" : L'orchestrateur (souvent final pour empêcher la surcharge)
    final public function generateExport(array $data, string $filename): string
    {
        // 1. Préparation commune
        if (empty($data)) {
            throw new \InvalidArgumentException("No data to export");
        }

        // 2. Étape spécifique déléguée à l'enfant
        $content = $this->formatData($data);

        // 3. Finalisation commune
        $filePath = $this->exportDir . '/' . $filename . '.' . $this->getFileExtension();
        $this->filesystem->dumpFile($filePath, $content);

        return $filePath;
    }

    // Contrats à remplir par les enfants
    abstract protected function formatData(array $data): string;
    abstract protected function getFileExtension(): string;
}

// Implémentation Concrète
class CsvExporter extends AbstractFileExporter
{
    protected function getFileExtension(): string
    {
        return 'csv';
    }

    protected function formatData(array $data): string
    {
        // Logique spécifique CSV
        $lines = [];
        foreach ($data as $row) {
            $lines[] = implode(',', $row);
        }
        return implode("\n", $lines);
    }
}
```

## Classes Abstraites vs Interfaces

Question classique d'entretien et de certification.

| Caractéristique | Interface | Classe Abstraite |
| :--- | :--- | :--- |
| **Méthodes** | Signatures uniquement (publiques). Pas de code. | Signatures (abstract) ET Code concret. Visibilité libre. |
| **Propriétés** | Aucune (sauf constantes). | Oui (typées, statiques, etc.). |
| **Héritage** | Multiple (`implements A, B`). | Simple (`extends A`). |
| **Constructeur** | Non. | Oui. |
| **Usage** | Définir **ce que l'objet FAIT** (Capabilities). | Définir **ce que l'objet EST** (Is-A relationship) + Code partagé. |

## Classes Anonymes

Depuis PHP 7, on peut créer des classes anonymes qui étendent des classes abstraites. Très utile pour les tests unitaires ou les objets à usage unique.

```php
$exporter = new class($fs, '/tmp') extends AbstractFileExporter {
    protected function formatData(array $data): string { return 'test'; }
    protected function getFileExtension(): string { return 'txt'; }
};
```

## 🧠 Concepts Clés
1.  **Non-instanciable** : Tentative de `new AbstractClass()` = `Fatal Error`.
2.  **Partialité** : Une classe abstraite n'a pas besoin d'implémenter toutes les méthodes des interfaces qu'elle déclare implémenter. Elle peut déléguer le reste aux enfants concrets.
3.  **Contrainte** : Si une classe contient au moins une méthode abstraite, elle **doit** être déclarée `abstract`.
4.  **Static** : Une méthode abstraite peut être statique (`abstract public static function name();`).

## ⚠️ Points de vigilance (Certification)
*   **Signature** : Les règles de covariance (type de retour plus précis) et contravariance (type d'argument plus large) s'appliquent lors de l'implémentation des méthodes abstraites.
*   **Visibilité** : L'enfant doit définir une visibilité égale ou plus permissive (ex: `protected` -> `public` est OK, mais `public` -> `protected` est interdit).
*   **Arguments optionnels** : L'implémentation peut ajouter des arguments optionnels qui ne sont pas dans la signature abstraite parente.
*   **Tests** : Pour tester une classe abstraite avec PHPUnit, utilisez `getMockForAbstractClass()` ou une classe anonyme.

## Ressources
*   [Manuel PHP - Abstraction de classes](https://www.php.net/manual/fr/language.oop5.abstract.php)
*   [PHP The Right Way - OOP](https://phptherightway.com/#object_oriented_programming)
