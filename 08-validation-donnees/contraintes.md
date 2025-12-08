# Contraintes de Validation Natives

## Concept clé
Symfony (via le composant `Validator`) fournit +60 contraintes prêtes à l'emploi pour couvrir 99% des besoins standards.
Elles s'utilisent via des Attributs PHP 8 (`#[Assert\Name]`).

## Classification et Usage

### 1. Basique (Basic)
*   `#[Assert\NotBlank]` : Le champ ne doit être ni `null`, ni `""`, ni `[]`. (Le standard pour "Requis").
*   `#[Assert\NotNull]` : Accepte `""` (chaine vide) mais pas `null`.
*   `#[Assert\IsNull]`.
*   `#[Assert\Type('integer')]`.

### 2. Texte (String)
*   `#[Assert\Length(min: 3, max: 255)]`.
*   `#[Assert\Email(mode: 'strict')]`.
*   `#[Assert\Regex('/^\d{5}$/')]`.
*   `#[Assert\Url]`.
*   `#[Assert\Uuid]`, `#[Assert\Ulid]`.
*   `#[Assert\Ip]`.
*   `#[Assert\UserPassword]` : Vérifie que la valeur correspond au mot de passe actuel de l'utilisateur (pour changement de MDP).
*   `#[Assert\NoSuspiciousCharacters]` : Vérifie les caractères invisibles ou homoglyphes (Sécurité).

### 3. Nombres (Number)
*   `#[Assert\Positive]`, `#[Assert\PositiveOrZero]`.
*   `#[Assert\Negative]`.
*   `#[Assert\Range(min: 18, max: 99)]`.
*   `#[Assert\DivisibleBy(0.5)]`.

### 4. Dates
*   `#[Assert\Date]`, `#[Assert\DateTime]`, `#[Assert\Time]`.
*   `#[Assert\GreaterThan('today')]`.
*   `#[Assert\LessThanOrEqual('+1 hour')]`.

### 5. Choix et Collections
*   `#[Assert\Choice(['male', 'female'])]` ou callback.
*   `#[Assert\Unique]` : Les éléments d'un tableau doivent être uniques.
*   `#[Assert\Count(min: 1)]`.
*   `#[Assert\All([...])]` : Applique une liste de contraintes à **chaque** élément d'un tableau.
    *   `#[Assert\All([new Assert\NotBlank, new Assert\Email])]`
*   `#[Assert\Collection]` : Valide la structure d'un tableau associatif (présence des clés et validation des valeurs par clé).
    ```php
    #[Assert\Collection(
        fields: [
            'name' => new Assert\Length(min: 5),
            'email' => new Assert\Email(),
        ],
        allowMissingFields: true
    )]
    protected array $profileData;
    ```

### 6. Fichiers
*   `#[Assert\File(maxSize: '10M')]`.
*   `#[Assert\Image(minWidth: 100)]`.

### 7. Logique & Conditionnel
*   `#[Assert\IsTrue]` : Utile pour une case "J'accepte les CGU" (qui n'est pas stockée dans l'entité mais doit être vraie).
*   `#[Assert\AtLeastOneOf]`.
*   `#[Assert\Sequentially]`.
*   `#[Assert\When]`.

## 🧠 Concepts Clés
1.  **Nullabilité** : Par défaut, la plupart des contraintes (Email, Length, Regex) **ignorent** les valeurs `null`. Si vous voulez qu'un champ soit obligatoire, vous **DEVEZ** ajouter `#[Assert\NotBlank]` ou `#[Assert\NotNull]`.
    *   *Exception* : `IsNull`, `NotNull`, `NotBlank`.
2.  **Messages** : Toutes les contraintes ont une option `message`. `#[Assert\NotBlank(message: 'Ce champ est vide.')]`.

## ⚠️ Points de vigilance (Certification)
*   **Type PHP vs Validator** : Typer une propriété `string` en PHP 8 ne remplace pas le validateur. PHP lance une `TypeError` (Fatal) si le type est mauvais. Le Validator génère une `ConstraintViolation` (Erreur utilisateur affichable). Les deux sont complémentaires.
*   **Email** : Le mode par défaut de `Email` est lâche (autorise `abc` parfois selon la RFC). Utilisez `mode: 'html5'` ou `'strict'` pour un comportement attendu.

## Ressources
*   [Symfony Docs - Constraints Reference](https://symfony.com/doc/current/reference/constraints.html)
