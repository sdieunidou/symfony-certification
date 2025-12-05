# Classes Abstraites

## Concept clé
Une classe abstraite est une classe qui ne peut pas être instanciée directement. Elle sert de modèle pour d'autres classes. Elle peut contenir des méthodes abstraites (signatures sans implémentation) que les classes enfants *doivent* implémenter, ainsi que des méthodes concrètes (avec code) que les enfants héritent.

## Application dans Symfony 7.0
Symfony utilise abondamment les classes abstraites pour fournir des fonctionnalités de base communes aux composants utilisateur.
Exemples :
*   `AbstractController` : Fournit des aides (`render`, `json`, `redirectToRoute`).
*   `AbstractType` : Classe de base pour les formulaires personnalisés.

## Exemple de code

```php
<?php

abstract class AbstractExport
{
    // Méthode concrète partagée
    public function export(array $data): string
    {
        $formatted = $this->format($data);
        return $this->save($formatted);
    }

    // Méthode abstraite (contrat à respecter par les enfants)
    abstract protected function format(array $data): string;

    protected function save(string $content): string
    {
        // Logique par défaut de sauvegarde
        return "Saved content: " . $content;
    }
}

class JsonExport extends AbstractExport
{
    protected function format(array $data): string
    {
        return json_encode($data);
    }
}
```

## Points de vigilance (Certification)
*   **Instanciation** : Une classe abstraite ne peut pas être instanciée (`new AbstractClass()` génère une erreur fatale).
*   **Méthodes abstraites** : Si une classe contient au moins une méthode abstraite, la classe *doit* être déclarée abstraite.
*   **Visibilité** : Lors de l'implémentation d'une méthode abstraite, la visibilité doit être la même ou plus permissive (ex: `protected` -> `public` est OK, `public` -> `protected` est interdit).
*   **Signatures** : Les signatures des méthodes (types des arguments et type de retour) doivent être compatibles (respect de la contravariance des paramètres et de la covariance du retour).

## Ressources
*   [Manuel PHP - Abstraction de classes](https://www.php.net/manual/fr/language.oop5.abstract.php)

